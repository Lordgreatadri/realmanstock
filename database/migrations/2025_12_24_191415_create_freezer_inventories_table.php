<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('freezer_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('processing_request_id')->nullable()->constrained()->onDelete('set null');
            $table->string('batch_number')->unique();
            $table->string('product_name');
            $table->decimal('weight', 8, 2);
            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price_per_kg', 10, 2);
            $table->date('processing_date');
            $table->date('expiry_date');
            $table->string('storage_location')->nullable();
            $table->string('temperature_zone')->nullable();
            $table->enum('status', ['in_stock', 'reserved', 'sold', 'expired'])->default('in_stock');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('freezer_inventories');
    }
};
