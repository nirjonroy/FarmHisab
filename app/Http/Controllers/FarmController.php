<?php

namespace App\Http\Controllers;

use App\Http\Requests\Farm\StoreFarmRequest;
use App\Http\Requests\Farm\UpdateFarmRequest;
use App\Models\Farm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $farms = Farm::query()
            ->with('createdBy')
            ->withCount('sheds')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('upazila', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('farms.index', compact('farms', 'search'));
    }

    public function create(): View
    {
        return view('farms.create');
    }

    public function store(StoreFarmRequest $request): RedirectResponse
    {
        Farm::create($this->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('farms.index')->with('success', __('farms.create_success'));
    }

    public function edit(Farm $farm): View
    {
        return view('farms.edit', compact('farm'));
    }

    public function update(UpdateFarmRequest $request, Farm $farm): RedirectResponse
    {
        $farm->update($this->payload($request->validated()));

        return redirect()->route('farms.index')->with('success', __('farms.update_success'));
    }

    private function payload(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
