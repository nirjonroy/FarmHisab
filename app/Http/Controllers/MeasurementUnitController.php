<?php

namespace App\Http\Controllers;

use App\Http\Requests\MeasurementUnit\StoreMeasurementUnitRequest;
use App\Http\Requests\MeasurementUnit\UpdateMeasurementUnitRequest;
use App\Models\MeasurementUnit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MeasurementUnitController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $units = MeasurementUnit::query()
            ->with('createdBy')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_bn', 'like', "%{$search}%")
                        ->orWhere('short_name_en', 'like', "%{$search}%")
                        ->orWhere('short_name_bn', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return view('measurement-units.index', compact('units', 'search', 'status'));
    }

    public function create(): View
    {
        return view('measurement-units.create');
    }

    public function store(StoreMeasurementUnitRequest $request): RedirectResponse
    {
        MeasurementUnit::create($this->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('measurement-units.index')->with('success', __('measurement_units.create_success'));
    }

    public function edit(MeasurementUnit $measurementUnit): View
    {
        return view('measurement-units.edit', compact('measurementUnit'));
    }

    public function update(UpdateMeasurementUnitRequest $request, MeasurementUnit $measurementUnit): RedirectResponse
    {
        $measurementUnit->update($this->payload($request->validated()));

        return redirect()->route('measurement-units.index')->with('success', __('measurement_units.update_success'));
    }

    private function payload(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['decimal_places'] = (int) ($data['decimal_places'] ?? 2);

        return $data;
    }
}
