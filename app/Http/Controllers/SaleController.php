<?php

namespace App\Http\Controllers;

use App\Enums\PaymentMethod;
use App\Http\Requests\Sale\StoreSaleRequest;
use App\Http\Requests\Sale\UpdateSaleRequest;
use App\Models\Batch;
use App\Models\Sale;
use App\Repositories\SaleRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function __construct(private SaleRepository $sales)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Sale::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $paymentMethod = $request->input('payment_method');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->sales->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('buyer_name', 'like', "%{$search}%")
                        ->orWhere('buyer_phone', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"));
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when(in_array($paymentMethod, PaymentMethod::values(), true), fn ($query) => $query->where('payment_method', $paymentMethod))
            ->when($dateFrom, fn ($query) => $query->whereDate('sale_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('sale_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', [
            'records' => $records,
            'batches' => $this->batches(),
            'paymentMethods' => PaymentMethod::options(),
            'search' => $search,
            'batchId' => $batchId,
            'paymentMethod' => $paymentMethod,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalAmount' => $records->sum('total_amount'),
            'totalDue' => $records->sum('due_amount'),
            'totalBirds' => $records->sum('birds_sold'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Sale::class);

        return view('sales.create', $this->formData());
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $this->authorize('create', Sale::class);

        $record = Sale::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('sales.show', $record)->with('success', __('sales.create_success'));
    }

    public function show(Sale $sale): View
    {
        $this->authorize('view', $sale);

        $sale->load(['batch.farm', 'createdBy']);

        return view('sales.show', ['record' => $sale]);
    }

    public function edit(Sale $sale): View
    {
        $this->authorize('update', $sale);

        return view('sales.edit', $this->formData() + ['record' => $sale]);
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $this->authorize('update', $sale);

        $sale->update($request->validated());

        return redirect()->route('sales.show', $sale)->with('success', __('sales.update_success'));
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        $this->authorize('delete', $sale);

        $sale->delete();

        return redirect()->route('sales.index')->with('success', __('sales.delete_success'));
    }

    private function formData(): array
    {
        return [
            'batches' => $this->batches(),
            'paymentMethods' => PaymentMethod::options(),
        ];
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
