<?php

namespace App\Http\Controllers;

use App\Enums\MortalityRecordType;
use App\Http\Requests\MortalityRecord\StoreMortalityRecordRequest;
use App\Http\Requests\MortalityRecord\UpdateMortalityRecordRequest;
use App\Models\Batch;
use App\Models\MortalityRecord;
use App\Repositories\MortalityRecordRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MortalityRecordController extends Controller
{
    public function __construct(private MortalityRecordRepository $mortalityRecords)
    {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', MortalityRecord::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->mortalityRecords->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('cause', 'like', "%{$search}%")
                        ->orWhere('action_taken', 'like', "%{$search}%")
                        ->orWhere('reported_by', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"));
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when(in_array($type, MortalityRecordType::values(), true), fn ($query) => $query->where('type', $type))
            ->when($dateFrom, fn ($query) => $query->whereDate('record_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('record_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('mortality.index', [
            'records' => $records,
            'batches' => $this->batches(),
            'types' => MortalityRecordType::options(),
            'search' => $search,
            'batchId' => $batchId,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', MortalityRecord::class);

        return view('mortality.create', $this->formData());
    }

    public function store(StoreMortalityRecordRequest $request): RedirectResponse
    {
        $this->authorize('create', MortalityRecord::class);

        $record = MortalityRecord::create($request->validated() + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('mortality.show', $record)->with('success', __('mortality.create_success'));
    }

    public function show(MortalityRecord $mortalityRecord): View
    {
        $this->authorize('view', $mortalityRecord);

        $mortalityRecord->load(['batch.farm', 'createdBy']);

        return view('mortality.show', ['record' => $mortalityRecord]);
    }

    public function edit(MortalityRecord $mortalityRecord): View
    {
        $this->authorize('update', $mortalityRecord);

        return view('mortality.edit', $this->formData() + ['record' => $mortalityRecord]);
    }

    public function update(UpdateMortalityRecordRequest $request, MortalityRecord $mortalityRecord): RedirectResponse
    {
        $this->authorize('update', $mortalityRecord);

        $mortalityRecord->update($request->validated());

        return redirect()->route('mortality.show', $mortalityRecord)->with('success', __('mortality.update_success'));
    }

    public function destroy(MortalityRecord $mortalityRecord): RedirectResponse
    {
        $this->authorize('delete', $mortalityRecord);

        $mortalityRecord->delete();

        return redirect()->route('mortality.index')->with('success', __('mortality.delete_success'));
    }

    private function formData(): array
    {
        return [
            'batches' => $this->batches(),
            'types' => MortalityRecordType::options(),
        ];
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
