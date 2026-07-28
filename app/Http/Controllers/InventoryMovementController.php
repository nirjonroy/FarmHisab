<?php

namespace App\Http\Controllers;

use App\Enums\InventoryMovementType;
use App\Http\Requests\Inventory\StoreInventoryMovementRequest;
use App\Http\Requests\Inventory\UpdateInventoryMovementRequest;
use App\Models\Batch;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Repositories\InventoryMovementRepository;
use App\Services\InventoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryMovementController extends Controller
{
    public function __construct(
        private InventoryMovementRepository $inventoryMovements,
        private InventoryService $inventoryService
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', InventoryMovement::class);

        $search = $request->string('search')->toString();
        $productId = $request->input('product_id');
        $batchId = $request->input('batch_id');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->inventoryMovements->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('supplier_name', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('product', fn ($query) => $query->where('name_en', 'like', "%{$search}%")->orWhere('name_bn', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"))
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"));
                });
            })
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when(in_array($type, InventoryMovementType::values(), true), fn ($query) => $query->where('type', $type))
            ->when($dateFrom, fn ($query) => $query->whereDate('movement_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('movement_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        $products = $this->products();
        $selectedProduct = $productId ? $products->firstWhere('id', (int) $productId) : null;

        return view('inventory.index', [
            'records' => $records,
            'products' => $products,
            'batches' => $this->batches(),
            'types' => InventoryMovementType::options(),
            'search' => $search,
            'productId' => $productId,
            'batchId' => $batchId,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentStock' => $selectedProduct ? $this->inventoryService->currentStock($selectedProduct->id) : null,
            'stockValue' => $selectedProduct ? $this->inventoryService->stockValue($selectedProduct->id) : null,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', InventoryMovement::class);

        return view('inventory.create', $this->formData());
    }

    public function store(StoreInventoryMovementRequest $request): RedirectResponse
    {
        $this->authorize('create', InventoryMovement::class);

        $record = InventoryMovement::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('inventory.show', $record)->with('success', __('inventory.create_success'));
    }

    public function show(InventoryMovement $inventoryMovement): View
    {
        $this->authorize('view', $inventoryMovement);

        $inventoryMovement->load(['product.unit', 'batch.farm', 'createdBy']);

        return view('inventory.show', ['record' => $inventoryMovement]);
    }

    public function edit(InventoryMovement $inventoryMovement): View
    {
        $this->authorize('update', $inventoryMovement);

        return view('inventory.edit', $this->formData() + ['record' => $inventoryMovement]);
    }

    public function update(UpdateInventoryMovementRequest $request, InventoryMovement $inventoryMovement): RedirectResponse
    {
        $this->authorize('update', $inventoryMovement);

        $inventoryMovement->update($request->validated());

        return redirect()->route('inventory.show', $inventoryMovement)->with('success', __('inventory.update_success'));
    }

    public function destroy(InventoryMovement $inventoryMovement): RedirectResponse
    {
        $this->authorize('delete', $inventoryMovement);

        $inventoryMovement->delete();

        return redirect()->route('inventory.index')->with('success', __('inventory.delete_success'));
    }

    private function formData(): array
    {
        return [
            'products' => $this->products(),
            'batches' => $this->batches(),
            'types' => InventoryMovementType::options(),
        ];
    }

    private function products()
    {
        return Product::with('unit')->active()->stockTracked()->ordered()->get();
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
