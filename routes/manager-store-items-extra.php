<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\StoreItemController;

// ...existing code...

Route::patch('/manager/store-items/{storeItem}/adjust-stock', [StoreItemController::class, 'adjustStock'])->name('manager.store-items.adjust-stock');
