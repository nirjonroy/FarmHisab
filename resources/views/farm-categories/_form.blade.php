@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="name_en" class="form-label">{{ __('farm_categories.english_name') }}</label>
        <input id="name_en" type="text" name="name_en" value="{{ old('name_en', $farmCategory->name_en ?? '') }}" class="form-control @error('name_en') is-invalid @enderror">
        @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="name_bn" class="form-label">{{ __('farm_categories.bengali_name') }}</label>
        <input id="name_bn" type="text" name="name_bn" value="{{ old('name_bn', $farmCategory->name_bn ?? '') }}" class="form-control @error('name_bn') is-invalid @enderror">
        @error('name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="parent_id" class="form-label">{{ __('farm_categories.parent_category') }}</label>
        <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
            <option value="">{{ __('farm_categories.no_parent_top_level') }}</option>
            @foreach ($parentCategories as $parentCategory)
                <option value="{{ $parentCategory->id }}" @selected((int) old('parent_id', $farmCategory->parent_id ?? '') === $parentCategory->id)>
                    {{ $parentCategory->display_name }} @if (! $parentCategory->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="slug" class="form-label">{{ __('farm_categories.slug') }}</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $farmCategory->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror">
        <div class="form-text">{{ __('farm_categories.slug_help') }}</div>
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="activity_type" class="form-label">{{ __('farm_categories.activity_type') }}</label>
        <select id="activity_type" name="activity_type" class="form-select @error('activity_type') is-invalid @enderror" required>
            @foreach ($activityTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('activity_type', ($farmCategory->activity_type->value ?? 'production')) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('activity_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="icon" class="form-label">{{ __('farm_categories.icon') }}</label>
        <input id="icon" type="text" name="icon" value="{{ old('icon', $farmCategory->icon ?? '') }}" class="form-control @error('icon') is-invalid @enderror">
        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="sort_order" class="form-label">{{ __('farm_categories.sort_order') }}</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $farmCategory->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description_en" class="form-label">{{ __('farm_categories.english_description') }}</label>
        <textarea id="description_en" name="description_en" rows="3" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $farmCategory->description_en ?? '') }}</textarea>
        @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description_bn" class="form-label">{{ __('farm_categories.bengali_description') }}</label>
        <textarea id="description_bn" name="description_bn" rows="3" class="form-control @error('description_bn') is-invalid @enderror">{{ old('description_bn', $farmCategory->description_bn ?? '') }}</textarea>
        @error('description_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $farmCategory->is_active ?? true))>
            <label for="is_active" class="form-check-label">{{ __('common.active') }}</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('farm-categories.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
