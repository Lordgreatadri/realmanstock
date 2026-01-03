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
        Schema::create('animals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('tag_number')->unique()->nullable();
            $table->string('breed')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->decimal('purchase_price', 10, 2);
            $table->date('purchase_date');
            $table->string('supplier')->nullable();
            $table->decimal('current_weight', 8, 2)->nullable();
            $table->enum('status', ['available', 'quarantined', 'under_treatment', 'reserved', 'sold', 'deceased'])->default('available');
            $table->text('health_notes')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_vaccinated')->default(false);
            $table->date('last_vaccination_date')->nullable();
            $table->decimal('selling_price_per_kg', 10, 2)->nullable();
            $table->decimal('fixed_selling_price', 10, 2)->nullable();
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
        Schema::dropIfExists('animals');
    }
};
