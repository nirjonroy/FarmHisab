<?php

namespace App\Http\Controllers;

use App\Http\Requests\Shed\StoreShedRequest;
use App\Http\Requests\Shed\UpdateShedRequest;
use App\Models\Farm;
use App\Models\Shed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShedController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $farmId = $request->integer('farm_id') ?: null;
        $status = $request->string('status')->toString();

        $sheds = Shed::query()
            ->with(['farm', 'createdBy'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhereHas('farm', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($farmId, fn ($query) => $query->where('farm_id', $farmId))
            ->when(in_array($status, ['active', 'inactive'], true), function ($query) use ($status) {
                $query->where('is_active', $status === 'active');
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('sheds.index', [
            'sheds' => $sheds,
            'farms' => Farm::orderBy('name')->get(),
            'search' => $search,
            'farmId' => $farmId,
            'status' => $status,
        ]);
    }

    public function create(): View
    {
        return view('sheds.create', [
            'farms' => Farm::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreShedRequest $request): RedirectResponse
    {
        Shed::create($this->payload($request->validated()) + [
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('sheds.index')->with('success', __('sheds.create_success'));
    }

    public function edit(Shed $shed): View
    {
        $farms = Farm::query()
            ->where('is_active', true)
            ->orWhereKey($shed->farm_id)
            ->orderBy('name')
            ->get();

        return view('sheds.edit', compact('shed', 'farms'));
    }

    public function update(UpdateShedRequest $request, Shed $shed): RedirectResponse
    {
        $shed->update($this->payload($request->validated()));

        return redirect()->route('sheds.index')->with('success', __('sheds.update_success'));
    }

    private function payload(array $data): array
    {
        $data['is_active'] = (bool) ($data['is_active'] ?? false);

        return $data;
    }
}
