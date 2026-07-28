<?php

namespace App\Http\Controllers;

use App\Enums\ExpenseCategory;
use App\Enums\PaymentMethod;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Models\Batch;
use App\Models\Expense;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseController extends Controller
{
    public function __construct(private ExpenseRepository $expenses)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Expense::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $category = $request->input('category');
        $paymentMethod = $request->input('payment_method');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->expenses->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('payee', 'like', "%{$search}%")
                        ->orWhere('reference_no', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"));
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when(in_array($category, ExpenseCategory::values(), true), fn ($query) => $query->where('category', $category))
            ->when(in_array($paymentMethod, PaymentMethod::values(), true), fn ($query) => $query->where('payment_method', $paymentMethod))
            ->when($dateFrom, fn ($query) => $query->whereDate('expense_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('expense_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('expenses.index', [
            'records' => $records,
            'batches' => $this->batches(),
            'categories' => ExpenseCategory::options(),
            'paymentMethods' => PaymentMethod::options(),
            'search' => $search,
            'batchId' => $batchId,
            'category' => $category,
            'paymentMethod' => $paymentMethod,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalAmount' => $records->sum('amount'),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Expense::class);

        return view('expenses.create', $this->formData());
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        $this->authorize('create', Expense::class);

        $record = Expense::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('expenses.show', $record)->with('success', __('expenses.create_success'));
    }

    public function show(Expense $expense): View
    {
        $this->authorize('view', $expense);

        $expense->load(['batch.farm', 'createdBy']);

        return view('expenses.show', ['record' => $expense]);
    }

    public function edit(Expense $expense): View
    {
        $this->authorize('update', $expense);

        return view('expenses.edit', $this->formData() + ['record' => $expense]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->authorize('update', $expense);

        $expense->update($request->validated());

        return redirect()->route('expenses.show', $expense)->with('success', __('expenses.update_success'));
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->authorize('delete', $expense);

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', __('expenses.delete_success'));
    }

    private function formData(): array
    {
        return [
            'batches' => $this->batches(),
            'categories' => ExpenseCategory::options(),
            'paymentMethods' => PaymentMethod::options(),
        ];
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
