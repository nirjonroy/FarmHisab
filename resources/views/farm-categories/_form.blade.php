@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Category name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $farmCategory->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="parent_id" class="form-label">Parent category</label>
        <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
            <option value="">No Parent / Top-level Category</option>
            @foreach ($parentCategories as $parentCategory)
                <option value="{{ $parentCategory->id }}" @selected((int) old('parent_id', $farmCategory->parent_id ?? '') === $parentCategory->id)>
                    {{ $parentCategory->name }} @if (! $parentCategory->is_active) (Inactive) @endif
                </option>
            @endforeach
        </select>
        @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="slug" class="form-label">Slug</label>
        <input id="slug" type="text" name="slug" value="{{ old('slug', $farmCategory->slug ?? '') }}" class="form-control @error('slug') is-invalid @enderror">
        <div class="form-text">Leave blank to generate from the category name.</div>
        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="icon" class="form-label">Icon</label>
        <input id="icon" type="text" name="icon" value="{{ old('icon', $farmCategory->icon ?? '') }}" class="form-control @error('icon') is-invalid @enderror">
        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="sort_order" class="form-label">Sort order</label>
        <input id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order', $farmCategory->sort_order ?? 0) }}" class="form-control @error('sort_order') is-invalid @enderror">
        @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $farmCategory->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $farmCategory->is_active ?? true))>
            <label for="is_active" class="form-check-label">Active</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('farm-categories.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
