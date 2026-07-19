@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="farm_id" class="form-label">{{ __('sheds.farm') }}</label>
        <select id="farm_id" name="farm_id" class="form-select @error('farm_id') is-invalid @enderror" required>
            <option value="">{{ __('sheds.select_farm') }}</option>
            @foreach ($farms as $farm)
                <option value="{{ $farm->id }}" @selected((int) old('farm_id', $shed->farm_id ?? '') === $farm->id)>
                    {{ $farm->name }} @if (! $farm->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('farm_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="name" class="form-label">{{ __('sheds.shed_name') }}</label>
        <input id="name" type="text" name="name" value="{{ old('name', $shed->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('sheds.shed_code') }}</label>
        <input id="code" type="text" name="code" value="{{ old('code', $shed->code ?? '') }}" class="form-control @error('code') is-invalid @enderror" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="capacity" class="form-label">{{ __('sheds.capacity') }}</label>
        <input id="capacity" type="number" min="1" name="capacity" value="{{ old('capacity', $shed->capacity ?? '') }}" class="form-control @error('capacity') is-invalid @enderror" required>
        @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">{{ __('sheds.description') }}</label>
        <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $shed->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $shed->is_active ?? true))>
            <label for="is_active" class="form-check-label">{{ __('common.active') }}</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('sheds.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
