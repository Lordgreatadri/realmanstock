<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add UUID to users table
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to animals table
        Schema::table('animals', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to processing_requests table
        Schema::table('processing_requests', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to freezer_inventories table
        Schema::table('freezer_inventories', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Add UUID to store_items table
        Schema::table('store_items', function (Blueprint $table) {
            $table->uuid('uuid')->after('id')->nullable();
        });

        // Generate UUIDs for existing records
        DB::table('users')->whereNull('uuid')->get()->each(function ($user) {
            DB::table('users')->where('id', $user->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('categories')->whereNull('uuid')->get()->each(function ($category) {
            DB::table('categories')->where('id', $category->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('animals')->whereNull('uuid')->get()->each(function ($animal) {
            DB::table('animals')->where('id', $animal->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('orders')->whereNull('uuid')->get()->each(function ($order) {
            DB::table('orders')->where('id', $order->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('customers')->whereNull('uuid')->get()->each(function ($customer) {
            DB::table('customers')->where('id', $customer->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('processing_requests')->whereNull('uuid')->get()->each(function ($request) {
            DB::table('processing_requests')->where('id', $request->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('freezer_inventories')->whereNull('uuid')->get()->each(function ($inventory) {
            DB::table('freezer_inventories')->where('id', $inventory->id)->update(['uuid' => Str::uuid()]);
        });

        DB::table('store_items')->whereNull('uuid')->get()->each(function ($item) {
            DB::table('store_items')->where('id', $item->id)->update(['uuid' => Str::uuid()]);
        });

        // Now make UUID columns unique
        Schema::table('users', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('animals', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('processing_requests', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('freezer_inventories', function (Blueprint $table) {
            $table->unique('uuid');
        });

        Schema::table('store_items', function (Blueprint $table) {
            $table->unique('uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('animals', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('processing_requests', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('freezer_inventories', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });

        Schema::table('store_items', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
