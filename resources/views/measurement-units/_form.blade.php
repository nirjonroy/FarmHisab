@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="name_en" class="form-label">{{ __('measurement_units.english_name') }}</label>
        <input id="name_en" type="text" name="name_en" value="{{ old('name_en', $measurementUnit->name_en ?? '') }}" class="form-control @error('name_en') is-invalid @enderror">
        @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="name_bn" class="form-label">{{ __('measurement_units.bengali_name') }}</label>
        <input id="name_bn" type="text" name="name_bn" value="{{ old('name_bn', $measurementUnit->name_bn ?? '') }}" class="form-control @error('name_bn') is-invalid @enderror">
        @error('name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="short_name_en" class="form-label">{{ __('measurement_units.english_short_name') }}</label>
        <input id="short_name_en" type="text" name="short_name_en" value="{{ old('short_name_en', $measurementUnit->short_name_en ?? '') }}" class="form-control @error('short_name_en') is-invalid @enderror">
        @error('short_name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="short_name_bn" class="form-label">{{ __('measurement_units.bengali_short_name') }}</label>
        <input id="short_name_bn" type="text" name="short_name_bn" value="{{ old('short_name_bn', $measurementUnit->short_name_bn ?? '') }}" class="form-control @error('short_name_bn') is-invalid @enderror">
        @error('short_name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('measurement_units.code') }}</label>
        <input id="code" type="text" name="code" value="{{ old('code', $measurementUnit->code ?? '') }}" class="form-control @error('code') is-invalid @enderror" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="decimal_places" class="form-label">{{ __('measurement_units.decimal_places') }}</label>
        <input id="decimal_places" type="number" min="0" max="4" name="decimal_places" value="{{ old('decimal_places', $measurementUnit->decimal_places ?? 2) }}" class="form-control @error('decimal_places') is-invalid @enderror" required>
        <div class="form-text">{{ __('measurement_units.decimal_places_help') }}</div>
        @error('decimal_places')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="sort_order" class="form-label">{{ __('measurement_units.sort_order') }}</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $measurementUnit->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check mb-2">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $measurementUnit->is_active ?? true))>
            <label for="is_active" class="form-check-label">{{ __('common.active') }}</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <label for="description_en" class="form-label">{{ __('measurement_units.english_description') }}</label>
        <textarea id="description_en" name="description_en" rows="3" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $measurementUnit->description_en ?? '') }}</textarea>
        @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description_bn" class="form-label">{{ __('measurement_units.bengali_description') }}</label>
        <textarea id="description_bn" name="description_bn" rows="3" class="form-control @error('description_bn') is-invalid @enderror">{{ old('description_bn', $measurementUnit->description_bn ?? '') }}</textarea>
        @error('description_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('measurement-units.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
