<?php

namespace App\Services;

use App\Models\FreezerInventory;
use App\Models\StoreItem;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function getAllFreezerInventory($filters = [])
    {
        $query = FreezerInventory::with(['category', 'processingRequest']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['expiring_soon'])) {
            $days = $filters['expiring_days'] ?? 7;
            $query->expiringSoon($days);
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createFreezerInventory(array $data)
    {
        if (!isset($data['batch_number'])) {
            $data['batch_number'] = 'BATCH-' . strtoupper(uniqid());
        }

        return FreezerInventory::create($data);
    }

    public function updateFreezerInventory(FreezerInventory $inventory, array $data)
    {
        $inventory->update($data);
        return $inventory;
    }

    public function getExpiringInventory($days = 7)
    {
        return FreezerInventory::expiringSoon($days)
            ->with('category')
            ->get();
    }

    public function getAllStoreItems($filters = [])
    {
        $query = StoreItem::with('category');

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['low_stock'])) {
            $query->lowStock();
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('sku', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate($filters['per_page'] ?? 15);
    }

    public function createStoreItem(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $data['image']->store('store-items', 'public');
        }

        return StoreItem::create($data);
    }

    public function updateStoreItem(StoreItem $item, array $data)
    {
        if (isset($data['image']) && !is_string($data['image'])) {
            if ($item->image) {
                \Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $data['image']->store('store-items', 'public');
        }

        $item->update($data);
        return $item;
    }

    public function getLowStockItems()
    {
        return StoreItem::lowStock()
            ->active()
            ->with('category')
            ->get();
    }

    public function adjustStockQuantity(StoreItem $item, float $quantity, string $type = 'add')
    {
        if ($type === 'add') {
            $item->quantity += $quantity;
        } else {
            $item->quantity -= $quantity;
        }

        $item->save();
        return $item;
    }

    public function getInventoryAlerts()
    {
        return [
            'low_stock_items' => $this->getLowStockItems(),
            'expiring_inventory' => $this->getExpiringInventory(),
        ];
    }
}
