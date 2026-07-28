<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeedRecord\StoreFeedRecordRequest;
use App\Http\Requests\FeedRecord\UpdateFeedRecordRequest;
use App\Models\Batch;
use App\Models\FeedRecord;
use App\Models\Product;
use App\Repositories\FeedRecordRepository;
use App\Services\FeedRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedRecordController extends Controller
{
    public function __construct(
        private FeedRecordRepository $feedRecords,
        private FeedRecordService $service
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', FeedRecord::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->feedRecords->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('feed_name', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"))
                        ->orWhereHas('product', fn ($query) => $query->where('name_en', 'like', "%{$search}%")->orWhere('name_bn', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when($dateFrom, fn ($query) => $query->whereDate('record_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('record_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('feed.index', [
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
        $this->authorize('create', FeedRecord::class);

        return view('feed.create', $this->formData());
    }

    public function store(StoreFeedRecordRequest $request): RedirectResponse
    {
        $this->authorize('create', FeedRecord::class);

        $record = FeedRecord::create($this->service->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('feed.show', $record)->with('success', __('feed.create_success'));
    }

    public function show(FeedRecord $feedRecord): View
    {
        $this->authorize('view', $feedRecord);

        $feedRecord->load(['batch.farm', 'product', 'createdBy']);

        return view('feed.show', ['record' => $feedRecord]);
    }

    public function edit(FeedRecord $feedRecord): View
    {
        $this->authorize('update', $feedRecord);

        return view('feed.edit', $this->formData() + ['record' => $feedRecord]);
    }

    public function update(UpdateFeedRecordRequest $request, FeedRecord $feedRecord): RedirectResponse
    {
        $this->authorize('update', $feedRecord);

        $feedRecord->update($this->service->payload($request->validated()));

        return redirect()->route('feed.show', $feedRecord)->with('success', __('feed.update_success'));
    }

    public function destroy(FeedRecord $feedRecord): RedirectResponse
    {
        $this->authorize('delete', $feedRecord);

        $feedRecord->delete();

        return redirect()->route('feed.index')->with('success', __('feed.delete_success'));
    }

    private function formData(): array
    {
        return [
            'batches' => $this->batches(),
            'products' => Product::active()->ordered()->get(),
        ];
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
