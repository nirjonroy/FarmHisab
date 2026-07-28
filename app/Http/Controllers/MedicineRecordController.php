<?php

namespace App\Http\Controllers;

use App\Enums\MedicineRecordType;
use App\Http\Requests\MedicineRecord\StoreMedicineRecordRequest;
use App\Http\Requests\MedicineRecord\UpdateMedicineRecordRequest;
use App\Models\Batch;
use App\Models\MedicineRecord;
use App\Models\Product;
use App\Repositories\MedicineRecordRepository;
use App\Services\MedicineRecordService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicineRecordController extends Controller
{
    public function __construct(
        private MedicineRecordRepository $medicineRecords,
        private MedicineRecordService $service
    ) {
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', MedicineRecord::class);

        $search = $request->string('search')->toString();
        $batchId = $request->input('batch_id');
        $type = $request->input('type');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $records = $this->medicineRecords->query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('medicine_name', 'like', "%{$search}%")
                        ->orWhere('supplier_name', 'like', "%{$search}%")
                        ->orWhere('dosage', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%")
                        ->orWhere('notes', 'like', "%{$search}%")
                        ->orWhereHas('batch', fn ($query) => $query->where('batch_no', 'like', "%{$search}%")->orWhere('batch_name', 'like', "%{$search}%"))
                        ->orWhereHas('product', fn ($query) => $query->where('name_en', 'like', "%{$search}%")->orWhere('name_bn', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%"));
                });
            })
            ->when($batchId, fn ($query) => $query->where('batch_id', $batchId))
            ->when(in_array($type, MedicineRecordType::values(), true), fn ($query) => $query->where('type', $type))
            ->when($dateFrom, fn ($query) => $query->whereDate('record_date', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('record_date', '<=', $dateTo))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('medicine.index', [
            'records' => $records,
            'batches' => $this->batches(),
            'types' => MedicineRecordType::options(),
            'search' => $search,
            'batchId' => $batchId,
            'type' => $type,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', MedicineRecord::class);

        return view('medicine.create', $this->formData());
    }

    public function store(StoreMedicineRecordRequest $request): RedirectResponse
    {
        $this->authorize('create', MedicineRecord::class);

        $record = MedicineRecord::create($this->service->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('medicine.show', $record)->with('success', __('medicine.create_success'));
    }

    public function show(MedicineRecord $medicineRecord): View
    {
        $this->authorize('view', $medicineRecord);

        $medicineRecord->load(['batch.farm', 'product', 'createdBy']);

        return view('medicine.show', ['record' => $medicineRecord]);
    }

    public function edit(MedicineRecord $medicineRecord): View
    {
        $this->authorize('update', $medicineRecord);

        return view('medicine.edit', $this->formData() + ['record' => $medicineRecord]);
    }

    public function update(UpdateMedicineRecordRequest $request, MedicineRecord $medicineRecord): RedirectResponse
    {
        $this->authorize('update', $medicineRecord);

        $medicineRecord->update($this->service->payload($request->validated()));

        return redirect()->route('medicine.show', $medicineRecord)->with('success', __('medicine.update_success'));
    }

    public function destroy(MedicineRecord $medicineRecord): RedirectResponse
    {
        $this->authorize('delete', $medicineRecord);

        $medicineRecord->delete();

        return redirect()->route('medicine.index')->with('success', __('medicine.delete_success'));
    }

    private function formData(): array
    {
        return [
            'batches' => $this->batches(),
            'products' => Product::active()->ordered()->get(),
            'types' => MedicineRecordType::options(),
        ];
    }

    private function batches()
    {
        return Batch::with('farm')->active()->ordered()->get();
    }
}
