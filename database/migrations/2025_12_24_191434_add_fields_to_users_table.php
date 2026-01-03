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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->unique()->after('email');
            $table->string('company_name')->nullable()->after('phone');
            $table->text('purpose')->nullable()->after('company_name');
            $table->boolean('is_approved')->default(false)->after('purpose');
            $table->timestamp('approved_at')->nullable()->after('is_approved');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('approved_at');
            $table->string('otp')->nullable()->after('approved_by');
            $table->timestamp('otp_expires_at')->nullable()->after('otp');
            $table->boolean('phone_verified')->default(false)->after('otp_expires_at');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'company_name', 'purpose', 'is_approved', 
                'approved_at', 'approved_by', 'otp', 'otp_expires_at', 
                'phone_verified', 'deleted_at'
            ]);
        });
    }
};
