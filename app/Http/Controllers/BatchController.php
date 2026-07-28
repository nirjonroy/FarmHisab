<?php

namespace App\Http\Controllers;

use App\Enums\BatchStatus;
use App\Http\Requests\Batch\StoreBatchRequest;
use App\Http\Requests\Batch\UpdateBatchRequest;
use App\Models\Batch;
use App\Models\Farm;
use App\Models\FarmCategory;
use App\Models\FarmVariety;
use App\Repositories\BatchRepository;
use App\Services\BatchCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatchController extends Controller
{
    public function __construct(
        private BatchRepository $batches,
        private BatchCalculationService $calculations
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Batch::class);

        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $batches = $this->batches->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('batch_no', 'like', "%{$search}%")
                        ->orWhere('batch_name', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%");
                });
            })
            ->when(BatchStatus::tryFrom($status), fn ($query) => $query->where('status', $status))
            ->when($dateFrom, fn ($query) => $query->whereDate('purchase_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('purchase_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('batches.index', [
            'batches' => $batches,
            'summary' => $this->calculations->dashboard(),
            'statuses' => BatchStatus::options(),
            'search' => $search,
            'status' => $status,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Batch::class);

        return view('batches.create', $this->formData());
    }

    public function store(StoreBatchRequest $request): RedirectResponse
    {
        $this->authorize('create', Batch::class);

        Batch::create($this->payload($request->validated()) + [
            'batch_no' => $this->batches->nextBatchNo(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('batches.index')->with('success', __('batches.create_success'));
    }

    public function show(Batch $batch): View
    {
        $this->authorize('view', $batch);

        $batch->load(['farm', 'birdType.parent', 'breed', 'createdBy']);

        return view('batches.show', [
            'batch' => $batch,
            'details' => $this->calculations->details($batch),
        ]);
    }

    public function edit(Batch $batch): View
    {
        $this->authorize('update', $batch);

        return view('batches.edit', $this->formData($batch) + ['batch' => $batch]);
    }

    public function update(UpdateBatchRequest $request, Batch $batch): RedirectResponse
    {
        $this->authorize('update', $batch);

        $batch->update($this->payload($request->validated()));

        return redirect()->route('batches.show', $batch)->with('success', __('batches.update_success'));
    }

    public function destroy(Batch $batch): RedirectResponse
    {
        $this->authorize('delete', $batch);

        $batch->delete();

        return redirect()->route('batches.index')->with('success', __('batches.delete_success'));
    }

    private function formData(?Batch $batch = null): array
    {
        return [
            'farms' => Farm::where('is_active', true)->orderBy('name')->get(),
            'birdTypes' => FarmCategory::with('parent')
                ->whereNotNull('parent_id')
                ->where(function ($query) use ($batch) {
                    $query->where('is_active', true)
                        ->when($batch, fn ($query) => $query->orWhere('id', $batch->bird_type_id));
                })
                ->ordered()
                ->get(),
            'breeds' => FarmVariety::with('category.parent')
                ->where(function ($query) use ($batch) {
                    $query->where('is_active', true)
                        ->when($batch, fn ($query) => $query->orWhere('id', $batch->breed_id));
                })
                ->ordered()
                ->get(),
            'statuses' => BatchStatus::options(),
        ];
    }

    private function payload(array $data): array
    {
        $data['medicine_budget'] = (float) ($data['medicine_budget'] ?? 0);
        $data['other_budget'] = (float) ($data['other_budget'] ?? 0);

        return $data;
    }
}
