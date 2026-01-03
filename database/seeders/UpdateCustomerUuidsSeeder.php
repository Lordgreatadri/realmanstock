<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UpdateCustomerUuidsSeeder extends Seeder
{
    public function run(): void
    {
        $customers = DB::table('customers')->whereNull('uuid')->get();
        
        foreach ($customers as $customer) {
            DB::table('customers')
                ->where('id', $customer->id)
                ->update(['uuid' => Str::uuid()->toString()]);
        }
        
        $this->command->info('Updated ' . $customers->count() . ' customers with UUIDs');
    }
}
