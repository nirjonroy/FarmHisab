@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="name_en" class="form-label">{{ __('products.english_name') }}</label>
        <input id="name_en" type="text" name="name_en" value="{{ old('name_en', $product->name_en ?? '') }}" class="form-control @error('name_en') is-invalid @enderror">
        @error('name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="name_bn" class="form-label">{{ __('products.bengali_name') }}</label>
        <input id="name_bn" type="text" name="name_bn" value="{{ old('name_bn', $product->name_bn ?? '') }}" class="form-control @error('name_bn') is-invalid @enderror">
        @error('name_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="farm_category_id" class="form-label">{{ __('products.category') }}</label>
        <select id="farm_category_id" name="farm_category_id" class="form-select @error('farm_category_id') is-invalid @enderror" required>
            <option value="">{{ __('products.all_categories') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((int) old('farm_category_id', $product->farm_category_id ?? '') === $category->id)>
                    {{ $category->parent?->display_name }} - {{ $category->display_name }} @if (! $category->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('farm_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="measurement_unit_id" class="form-label">{{ __('products.measurement_unit') }}</label>
        <select id="measurement_unit_id" name="measurement_unit_id" class="form-select @error('measurement_unit_id') is-invalid @enderror" required>
            <option value="">{{ __('products.all_units') }}</option>
            @foreach ($units as $unit)
                <option value="{{ $unit->id }}" @selected((int) old('measurement_unit_id', $product->measurement_unit_id ?? '') === $unit->id)>
                    {{ $unit->display_name }} ({{ $unit->display_short_name }}) @if (! $unit->is_active) ({{ __('common.inactive') }}) @endif
                </option>
            @endforeach
        </select>
        @error('measurement_unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="sku" class="form-label">{{ __('products.sku') }}</label>
        <input id="sku" type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" class="form-control @error('sku') is-invalid @enderror" required>
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="barcode" class="form-label">{{ __('products.barcode') }}</label>
        <input id="barcode" type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="form-control @error('barcode') is-invalid @enderror">
        @error('barcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="usage_type" class="form-label">{{ __('products.usage_type') }}</label>
        <select id="usage_type" name="usage_type" class="form-select @error('usage_type') is-invalid @enderror" required>
            @foreach ($usageTypes as $value => $label)
                <option value="{{ $value }}" @selected(old('usage_type', ($product->usage_type->value ?? 'both')) === $value)>{{ $label }}</option>
            @endforeach
        </select>
        @error('usage_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="sort_order" class="form-label">{{ __('products.sort_order') }}</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $product->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description_en" class="form-label">{{ __('products.english_description') }}</label>
        <textarea id="description_en" name="description_en" rows="3" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en', $product->description_en ?? '') }}</textarea>
        @error('description_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description_bn" class="form-label">{{ __('products.bengali_description') }}</label>
        <textarea id="description_bn" name="description_bn" rows="3" class="form-control @error('description_bn') is-invalid @enderror">{{ old('description_bn', $product->description_bn ?? '') }}</textarea>
        @error('description_bn')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <input type="hidden" name="is_stock_tracked" value="0">
        <div class="form-check">
            <input id="is_stock_tracked" type="checkbox" name="is_stock_tracked" value="1" class="form-check-input @error('is_stock_tracked') is-invalid @enderror" @checked(old('is_stock_tracked', $product->is_stock_tracked ?? true))>
            <label for="is_stock_tracked" class="form-check-label">{{ __('products.stock_tracked') }}</label>
            @error('is_stock_tracked')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $product->is_active ?? true))>
            <label for="is_active" class="form-check-label">{{ __('common.active') }}</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">{{ __('common.cancel') }}</a>
</div>
