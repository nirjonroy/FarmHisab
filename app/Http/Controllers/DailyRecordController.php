<?php

namespace App\Http\Controllers;

use App\Http\Requests\DailyRecord\StoreDailyRecordRequest;
use App\Http\Requests\DailyRecord\UpdateDailyRecordRequest;
use App\Models\Batch;
use App\Models\DailyRecord;
use App\Repositories\DailyRecordRepository;
use App\Services\DailyRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DailyRecordController extends Controller
{
    public function __construct(
        private DailyRecordRepository $dailyRecords,
        private DailyRecordService $service
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', DailyRecord::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->dailyRecords->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', function ($query) use ($search) {
                            $query->where('batch_no', 'like', "%{$search}%")
                                ->orWhere('batch_name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when($dateFrom, fn ($query) => $query->whereDate('record_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('record_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('daily-records.index', [
            'records' => $records,
            'batches' => $this->batches(),
            'search' => $search,
            'batchId' => $batchId,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create(Request $request): View
    {
        $this->authorize('create', DailyRecord::class);

        $selectedBatch = $request->filled('batch_id') ? Batch::find($request->input('batch_id')) : null;

        return view('daily-records.create', [
            'batches' => $this->batches(),
            'defaultOpeningBirds' => $selectedBatch ? $this->service->defaultOpeningBirds($selectedBatch) : null,
        ]);
    }

    public function store(StoreDailyRecordRequest $request): RedirectResponse
    {
        $this->authorize('create', DailyRecord::class);

        $record = DailyRecord::create($this->service->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('daily-records.show', $record)->with('success', __('daily_records.create_success'));
    }

    public function show(DailyRecord $dailyRecord): View
    {
        $this->authorize('view', $dailyRecord);

        $dailyRecord->load(['batch.farm', 'batch.birdType', 'batch.breed', 'createdBy']);

        return view('daily-records.show', ['record' => $dailyRecord]);
    }

    public function edit(DailyRecord $dailyRecord): View
    {
        $this->authorize('update', $dailyRecord);

        return view('daily-records.edit', [
            'record' => $dailyRecord,
            'batches' => $this->batches(),
            'defaultOpeningBirds' => $this->service->defaultOpeningBirds($dailyRecord->batch, $dailyRecord),
        ]);
    }

    public function update(UpdateDailyRecordRequest $request, DailyRecord $dailyRecord): RedirectResponse
    {
        $this->authorize('update', $dailyRecord);

        $dailyRecord->update($this->service->payload($request->validated()));

        return redirect()->route('daily-records.show', $dailyRecord)->with('success', __('daily_records.update_success'));
    }

    public function destroy(DailyRecord $dailyRecord): RedirectResponse
    {
        $this->authorize('delete', $dailyRecord);

        $dailyRecord->delete();

        return redirect()->route('daily-records.index')->with('success', __('daily_records.delete_success'));
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
