@csrf
<div class="row g-3">
    <div class="col-12">
        <label for="farm_category_id" class="form-label">{{ __('farm_varieties.category') }}</label>
        <select id="farm_category_id" name="farm_category_id" class="form-select @error('farm_category_id') is-invalid @enderror" required>
            <option value="">{{ __('farm_varieties.all_categories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('farm_category_id', $farmVariety->farm_category_id ?? '') === $category->id)>
                    {{ $category->parent?->display_name }} — {{ $category->display_name }} @if (! $category->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('farm_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="name_en" class="form-label">{{ __('farm_varieties.english_name') }}</label>
        <input id="name_en" type="text" name="name_en" value="{{ old('name_en', $farmVariety->name_en ?? '') }}" class="form-control @error('name_en') is-invalid @enderror">
        @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="name_bn" class="form-label">{{ __('farm_varieties.bengali_name') }}</label>
        <input id="name_bn" type="text" name="name_bn" value="{{ old('name_bn', $farmVariety->name_bn ?? '') }}" class="form-control @error('name_bn') is-invalid @enderror">
        @error('name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">{{ __('farm_varieties.code') }}</label>
        <input id="code" type="text" name="code" value="{{ old('code', $farmVariety->code ?? '') }}" class="form-control @error('code') is-invalid @enderror">
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="slug" class="form-label">{{ __('farm_varieties.slug') }}</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $farmVariety->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror">
        <div class="form-text">{{ __('farm_varieties.slug_help') }}</div>
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="sort_order" class="form-label">{{ __('farm_varieties.sort_order') }}</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $farmVariety->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $farmVariety->is_active ?? true))>
            <label for="is_active" class="form-check-label">{{ __('common.active') }}</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-12">
        <label for="description_en" class="form-label">{{ __('farm_varieties.english_description') }}</label>
        <textarea id="description_en" name="description_en" rows="3" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $farmVariety->description_en ?? '') }}</textarea>
        @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description_bn" class="form-label">{{ __('farm_varieties.bengali_description') }}</label>
        <textarea id="description_bn" name="description_bn" rows="3" class="form-control @error('description_bn') is-invalid @enderror">{{ old('description_bn', $farmVariety->description_bn ?? '') }}</textarea>
        @error('description_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('farm-varieties.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
