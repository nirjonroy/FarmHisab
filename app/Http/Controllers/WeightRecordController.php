<?php

namespace App\Http\Controllers;

use App\Http\Requests\WeightRecord\StoreWeightRecordRequest;
use App\Http\Requests\WeightRecord\UpdateWeightRecordRequest;
use App\Models\Batch;
use App\Models\WeightRecord;
use App\Repositories\WeightRecordRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WeightRecordController extends Controller
{
    public function __construct(private WeightRecordRepository $weightRecords)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', WeightRecord::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->weightRecords->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('weighed_by', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"));
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when($dateFrom, fn ($query) => $query->whereDate('record_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('record_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('weights.index', [
            'records' => $records,
            'batches' => $this->batches(),
            'search' => $search,
            'batchId' => $batchId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', WeightRecord::class);

        return view('weights.create', $this->formData());
    }

    public function store(StoreWeightRecordRequest $request): RedirectResponse
    {
        $this->authorize('create', WeightRecord::class);

        $record = WeightRecord::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('weights.show', $record)->with('success', __('weights.create_success'));
    }

    public function show(WeightRecord $weightRecord): View
    {
        $this->authorize('view', $weightRecord);

        $weightRecord->load(['batch.farm', 'createdBy']);

        return view('weights.show', ['record' => $weightRecord]);
    }

    public function edit(WeightRecord $weightRecord): View
    {
        $this->authorize('update', $weightRecord);

        return view('weights.edit', $this->formData() + ['record' => $weightRecord]);
    }

    public function update(UpdateWeightRecordRequest $request, WeightRecord $weightRecord): RedirectResponse
    {
        $this->authorize('update', $weightRecord);

        $weightRecord->update($request->validated());

        return redirect()->route('weights.show', $weightRecord)->with('success', __('weights.update_success'));
    }

    public function destroy(WeightRecord $weightRecord): RedirectResponse
    {
        $this->authorize('delete', $weightRecord);

        $weightRecord->delete();

        return redirect()->route('weights.index')->with('success', __('weights.delete_success'));
    }

    private function formData(): array
    {
        return ['batches' => $this->batches()];
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
