<?php

namespace App\Services;

use App\Models\ProcessingRequest;
use App\Models\Animal;
use App\Models\FreezerInventory;
use Illuminate\Support\Facades\DB;

class ProcessingService
{
    public function createProcessingRequest(array $data)
    {
        try {
            DB::beginTransaction();

            $request = ProcessingRequest::create($data);

            // If an animal is being processed, update its status
            if (isset($data['animal_id'])) {
                $animal = Animal::find($data['animal_id']);
                if ($animal) {
                    $animal->update(['status' => 'under_treatment']);
                }
            }

            DB::commit();
            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateProcessingRequest(ProcessingRequest $request, array $data)
    {
        try {
            DB::beginTransaction();

            $request->update($data);

            // If processing is completed, create freezer inventory
            if ($data['status'] === 'completed' && !$request->wasChanged('status')) {
                $this->createFreezerInventoryFromProcessing($request);
            }

            DB::commit();
            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function completeProcessing(ProcessingRequest $request, array $data)
    {
        try {
            DB::beginTransaction();

            $request->update([
                'status' => 'completed',
                'completed_date' => now(),
                'dressed_weight' => $data['dressed_weight'],
                'quality_notes' => $data['quality_notes'] ?? null,
                'processed_by' => auth()->id(),
            ]);

            // Mark animal as sold if it exists
            if ($request->animal_id) {
                $animal = Animal::find($request->animal_id);
                if ($animal) {
                    $animal->update(['status' => 'sold']);
                }
            }

            // Create freezer inventory
            if (isset($data['store_in_freezer']) && $data['store_in_freezer']) {
                $this->createFreezerInventoryFromProcessing($request, $data);
            }

            DB::commit();
            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function createFreezerInventoryFromProcessing(ProcessingRequest $request, array $additionalData = [])
    {
        $animal = $request->animal;
        $expiryDate = now()->addMonths(6); // Default 6 months expiry

        return FreezerInventory::create([
            'category_id' => $animal->category_id ?? 1,
            'processing_request_id' => $request->id,
            'batch_number' => 'BATCH-' . strtoupper(uniqid()),
            'product_name' => $additionalData['product_name'] ?? ($animal->breed ?? 'Processed Meat'),
            'weight' => $request->dressed_weight,
            'cost_price' => $animal->purchase_price ?? 0,
            'selling_price_per_kg' => $additionalData['selling_price_per_kg'] ?? ($animal->selling_price_per_kg ?? 0),
            'processing_date' => now(),
            'expiry_date' => $additionalData['expiry_date'] ?? $expiryDate,
            'storage_location' => $additionalData['storage_location'] ?? 'Main Freezer',
            'temperature_zone' => $additionalData['temperature_zone'] ?? 'Zone A',
            'status' => 'in_stock',
        ]);
    }

    public function getPendingRequests()
    {
        return ProcessingRequest::with(['animal', 'customer'])
            ->pending()
            ->latest()
            ->get();
    }

    public function getRequestsByDate($date = null)
    {
        $date = $date ?? today();

        return ProcessingRequest::with(['animal', 'customer'])
            ->whereDate('scheduled_date', $date)
            ->get();
    }

    public function scheduleProcessing(ProcessingRequest $request, $scheduledDate)
    {
        $request->update([
            'scheduled_date' => $scheduledDate,
            'status' => 'in_progress',
        ]);

        return $request;
    }

    public function cancelProcessing(ProcessingRequest $request, $reason = null)
    {
        try {
            DB::beginTransaction();

            $request->update([
                'status' => 'cancelled',
                'quality_notes' => $reason,
            ]);

            // Return animal to available status
            if ($request->animal_id) {
                $animal = Animal::find($request->animal_id);
                if ($animal) {
                    $animal->update(['status' => 'available']);
                }
            }

            DB::commit();
            return $request;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
