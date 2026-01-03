<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public function getAllCustomers($filters = [])
    {
        $query = Customer::with(['orders', 'processingRequests']);

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('phone', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['allow_credit'])) {
            $query->where('allow_credit', $filters['allow_credit']);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createCustomer(array $data)
    {
        return Customer::create($data);
    }

    public function updateCustomer(Customer $customer, array $data)
    {
        $customer->update($data);
        return $customer;
    }

    public function deleteCustomer(Customer $customer)
    {
        $customer->delete();
        return true;
    }

    public function getCustomerPurchaseHistory(Customer $customer)
    {
        return $customer->orders()
            ->with('items')
            ->latest()
            ->get();
    }

    public function getCustomerOutstandingBalance(Customer $customer)
    {
        return $customer->outstanding_balance;
    }

    public function updateCustomerBalance(Customer $customer, float $amount)
    {
        $customer->outstanding_balance += $amount;
        $customer->save();
        return $customer;
    }

    public function getTopCustomers($limit = 10)
    {
        return Customer::withCount('orders')
            ->withSum('orders as total_spent', 'total')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();
    }
}
