# S3 Storage Implementation Guide

This guide shows exactly where to add S3 storage code in your RealMan application.

## Current File Upload Locations

Your application currently uploads files to local storage (`'public'` disk) in these controllers:

### 1. **Category Icons**
- **File:** `app/Http/Controllers/Manager/CategoryController.php`
- **Lines:** 70-71 (store), 118 (update)
- **File Type:** Category icons (images)

### 2. **Animal Images**
- **File:** `app/Http/Controllers/Admin/AnimalController.php`
- **Lines:** 89-90 (store), 144-149 (update with deletion)
- **File Type:** Animal photos

### 3. **Other Upload Locations**
Similar patterns exist in:
- `app/Http/Controllers/Manager/AnimalController.php`
- Other controllers (to be identified)

---

## How to Switch from Local Storage to S3

### Option 1: Change Globally (Recommended for Production)

Simply update your `.env` file:

```env
# Change from:
FILESYSTEM_DISK=local

# To:
FILESYSTEM_DISK=s3
```

**That's it!** All existing code will automatically use S3 because Laravel uses the default disk.

### Option 2: Explicitly Use S3 Disk (Recommended for Flexibility)

Modify your controllers to explicitly specify the S3 disk.

---

## Specific Code Changes

### 1. Category Icon Upload (Manager)

**File:** `app/Http/Controllers/Manager/CategoryController.php`

**Current Code (Line 70-71):**
```php
if ($request->hasFile('icon')) {
    $validated['icon'] = $request->file('icon')->store('categories', 'public');
}
```

**Change to S3:**
```php
if ($request->hasFile('icon')) {
    $validated['icon'] = $request->file('icon')->store('categories', 's3');
}
```

**Get Public URL:**
```php
if ($request->hasFile('icon')) {
    $path = $request->file('icon')->store('categories', 's3');
    $validated['icon'] = $path;
    $validated['icon_url'] = Storage::disk('s3')->url($path); // Optional: store URL
}
```

**Update Method (Line 117-118):**
```php
if ($request->hasFile('icon')) {
    // Delete old icon from S3
    if ($category->icon && Storage::disk('s3')->exists($category->icon)) {
        Storage::disk('s3')->delete($category->icon);
    }
    
    // Upload new icon to S3
    $validated['icon'] = $request->file('icon')->store('categories', 's3');
}
```

---

### 2. Animal Image Upload (Admin)

**File:** `app/Http/Controllers/Admin/AnimalController.php`

**Store Method (Line 89-90):**

**Current:**
```php
if ($request->hasFile('image')) {
    $validated['image'] = $request->file('image')->store('animals', 'public');
}
```

**Change to S3:**
```php
if ($request->hasFile('image')) {
    $validated['image'] = $request->file('image')->store('animals', 's3');
}
```

**Update Method (Line 144-149):**

**Current:**
```php
if ($request->hasFile('image')) {
    // Delete old image if exists
    if ($animal->image && \Storage::disk('public')->exists($animal->image)) {
        \Storage::disk('public')->delete($animal->image);
    }
    $validated['image'] = $request->file('image')->store('animals', 'public');
}
```

**Change to S3:**
```php
if ($request->hasFile('image')) {
    // Delete old image from S3
    if ($animal->image && Storage::disk('s3')->exists($animal->image)) {
        Storage::disk('s3')->delete($animal->image);
    }
    
    // Upload new image to S3
    $validated['image'] = $request->file('image')->store('animals', 's3');
}
```

---

### 3. Advanced Example: Document Upload with Metadata

**Example Controller Method:**

```php
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

public function uploadDocument(Request $request)
{
    $request->validate([
        'document' => 'required|file|mimes:pdf,doc,docx|max:10240', // 10MB max
    ]);

    // Upload to S3
    $path = $request->file('document')->store('documents', 's3');
    
    // Get file details
    $file = $request->file('document');
    $url = Storage::disk('s3')->url($path);
    
    // Save to database (example)
    $document = Document::create([
        'filename' => $file->getClientOriginalName(),
        'file_path' => $path,
        'file_url' => $url,
        'mime_type' => $file->getMimeType(),
        'file_size' => $file->getSize(),
        'uploaded_by' => auth()->id(),
    ]);
    
    return response()->json([
        'success' => true,
        'url' => $url,
        'document' => $document
    ]);
}
```

---

### 4. Processing Request with File Attachments

**Example: Upload processing reports/documents**

```php
// In ProcessingController.php
public function uploadReport(Request $request, ProcessingRequest $processingRequest)
{
    $request->validate([
        'report' => 'required|file|mimes:pdf|max:5120', // 5MB
    ]);

    // Upload to S3 in processing-reports folder
    $path = $request->file('report')->store(
        'processing-reports/' . $processingRequest->id, 
        's3'
    );
    
    // Update processing request
    $processingRequest->update([
        'report_path' => $path,
        'report_url' => Storage::disk('s3')->url($path),
        'report_uploaded_at' => now(),
    ]);
    
    return redirect()->back()->with('success', 'Report uploaded successfully!');
}
```

---

### 5. Export Reports to S3

**Example: Save generated Excel/PDF reports**

```php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalesReportExport;

public function exportSalesReport(Request $request)
{
    $filename = 'sales-report-' . now()->format('Y-m-d-His') . '.xlsx';
    $path = 'reports/sales/' . $filename;
    
    // Export directly to S3
    Excel::store(new SalesReportExport($request->all()), $path, 's3');
    
    // Get download URL (valid for 5 minutes)
    $url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));
    
    return response()->json([
        'success' => true,
        'download_url' => $url,
        'filename' => $filename
    ]);
}
```

**For PDF Reports:**
```php
use Barryvdh\DomPDF\Facade\Pdf;

public function generateInvoicePDF(Order $order)
{
    $pdf = Pdf::loadView('pdfs.invoice', compact('order'));
    $filename = 'invoice-' . $order->order_number . '.pdf';
    $path = 'invoices/' . $filename;
    
    // Save PDF to S3
    Storage::disk('s3')->put($path, $pdf->output());
    
    // Get URL
    $url = Storage::disk('s3')->url($path);
    
    // Save to order
    $order->update([
        'invoice_path' => $path,
        'invoice_url' => $url
    ]);
    
    return response()->json(['url' => $url]);
}
```

---

## Displaying Images from S3 in Blade Views

### Current Code (Public Disk):
```blade
@if($animal->image)
    <img src="{{ asset('storage/' . $animal->image) }}" alt="{{ $animal->tag_number }}">
@endif
```

### Change to S3:

**Option 1: Using Storage Facade**
```blade
@if($animal->image)
    <img src="{{ Storage::disk('s3')->url($animal->image) }}" alt="{{ $animal->tag_number }}">
@endif
```

**Option 2: Store URL in Database (Recommended)**

When uploading, save the URL:
```php
$path = $request->file('image')->store('animals', 's3');
$validated['image'] = $path;
$validated['image_url'] = Storage::disk('s3')->url($path);
```

Then in Blade:
```blade
@if($animal->image_url)
    <img src="{{ $animal->image_url }}" alt="{{ $animal->tag_number }}">
@endif
```

**Option 3: Use Accessor in Model**

Add to `Animal` model:
```php
use Illuminate\Support\Facades\Storage;

public function getImageUrlAttribute()
{
    if ($this->image) {
        return Storage::disk('s3')->url($this->image);
    }
    return null;
}
```

Then in Blade:
```blade
@if($animal->image_url)
    <img src="{{ $animal->image_url }}" alt="{{ $animal->tag_number }}">
@endif
```

---

## Complete Controller Example with S3

**File:** `app/Http/Controllers/Manager/AnimalController.php`

```php
<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AnimalController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tag_number' => 'required|string|unique:animals,tag_number',
            'breed' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'current_weight' => 'required|numeric|min:0',
            'status' => 'required|in:available,quarantined,sold,deceased',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload to S3
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('animals', 's3');
            $validated['image'] = $path;
            $validated['image_url'] = Storage::disk('s3')->url($path);
        }

        Animal::create($validated);

        return redirect()->route('manager.animals.index')
            ->with('success', 'Animal created successfully.');
    }

    public function update(Request $request, Animal $animal): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'tag_number' => 'required|string|unique:animals,tag_number,' . $animal->id,
            'breed' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'purchase_price' => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'current_weight' => 'required|numeric|min:0',
            'status' => 'required|in:available,quarantined,sold,deceased',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Handle image upload to S3
        if ($request->hasFile('image')) {
            // Delete old image from S3
            if ($animal->image && Storage::disk('s3')->exists($animal->image)) {
                Storage::disk('s3')->delete($animal->image);
            }
            
            // Upload new image
            $path = $request->file('image')->store('animals', 's3');
            $validated['image'] = $path;
            $validated['image_url'] = Storage::disk('s3')->url($path);
        }

        $animal->update($validated);

        return redirect()->route('manager.animals.index')
            ->with('success', 'Animal updated successfully.');
    }

    public function destroy(Animal $animal): RedirectResponse
    {
        // Delete image from S3 before deleting record
        if ($animal->image && Storage::disk('s3')->exists($animal->image)) {
            Storage::disk('s3')->delete($animal->image);
        }

        $animal->delete();

        return redirect()->route('manager.animals.index')
            ->with('success', 'Animal deleted successfully.');
    }
}
```

---

## Database Schema Updates (Optional)

If you want to store S3 URLs in database, add migrations:

```php
// database/migrations/xxxx_add_s3_urls_to_animals_table.php
public function up()
{
    Schema::table('animals', function (Blueprint $table) {
        $table->string('image_url')->nullable()->after('image');
    });
}

// For categories
public function up()
{
    Schema::table('categories', function (Blueprint $table) {
        $table->string('icon_url')->nullable()->after('icon');
    });
}
```

---

## Testing S3 Integration

### Test File Upload
```bash
php artisan tinker

# Upload a test file
$file = new \Illuminate\Http\UploadedFile(
    storage_path('app/test.jpg'),
    'test.jpg',
    'image/jpeg',
    null,
    true
);

$path = $file->store('test', 's3');
echo "Uploaded to: " . $path . "\n";

$url = Storage::disk('s3')->url($path);
echo "URL: " . $url . "\n";

# Delete test file
Storage::disk('s3')->delete($path);
```

---

## Common S3 Operations in Your App

### 1. Check if File Exists
```php
if (Storage::disk('s3')->exists($path)) {
    // File exists
}
```

### 2. Get File Size
```php
$size = Storage::disk('s3')->size($path); // in bytes
```

### 3. Get File Last Modified
```php
$timestamp = Storage::disk('s3')->lastModified($path);
```

### 4. Download File
```php
$contents = Storage::disk('s3')->get($path);
return response($contents)->header('Content-Type', 'image/jpeg');
```

### 5. Generate Temporary Download URL
```php
// URL valid for 5 minutes
$url = Storage::disk('s3')->temporaryUrl($path, now()->addMinutes(5));
```

### 6. List Files in Directory
```php
$files = Storage::disk('s3')->files('animals');
```

### 7. Copy File
```php
Storage::disk('s3')->copy('old/path.jpg', 'new/path.jpg');
```

### 8. Move File
```php
Storage::disk('s3')->move('old/path.jpg', 'new/path.jpg');
```

---

## Summary: Quick Action Steps

1. **Add AWS credentials to `.env`:**
   ```env
   FILESYSTEM_DISK=s3
   AWS_ACCESS_KEY_ID=your-key
   AWS_SECRET_ACCESS_KEY=your-secret
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=realman-production-storage
   ```

2. **Update Controllers:**
   - Replace `'public'` with `'s3'` in all `.store()` calls
   - Update `Storage::disk('public')` to `Storage::disk('s3')`

3. **Update Blade Views:**
   - Replace `asset('storage/' . $path)` with `Storage::disk('s3')->url($path)`
   - Or use model accessors for cleaner code

4. **Test:**
   ```bash
   php artisan tinker
   Storage::disk('s3')->put('test.txt', 'Hello S3!');
   Storage::disk('s3')->url('test.txt');
   ```

---

**Files to Update in Your App:**

- ✅ `app/Http/Controllers/Manager/CategoryController.php` (Lines 70, 118)
- ✅ `app/Http/Controllers/Admin/AnimalController.php` (Lines 89, 146-149)
- ✅ `app/Http/Controllers/Manager/AnimalController.php` (similar locations)
- ✅ Any blade views displaying images
- ✅ Models (optional: add accessor methods)

That's it! Your app will now use S3 for all file storage.
