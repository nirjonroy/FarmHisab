@csrf
<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Farm name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $farm->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="code" class="form-label">Farm code</label>
        <input id="code" type="text" name="code" value="{{ old('code', $farm->code ?? '') }}" class="form-control @error('code') is-invalid @enderror" required>
        @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="phone" class="form-label">Phone</label>
        <input id="phone" type="text" name="phone" value="{{ old('phone', $farm->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="district" class="form-label">District</label>
        <input id="district" type="text" name="district" value="{{ old('district', $farm->district ?? '') }}" class="form-control @error('district') is-invalid @enderror">
        @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="upazila" class="form-label">Upazila</label>
        <input id="upazila" type="text" name="upazila" value="{{ old('upazila', $farm->upazila ?? '') }}" class="form-control @error('upazila') is-invalid @enderror">
        @error('upazila')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="union_name" class="form-label">Union</label>
        <input id="union_name" type="text" name="union_name" value="{{ old('union_name', $farm->union_name ?? '') }}" class="form-control @error('union_name') is-invalid @enderror">
        @error('union_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="address" class="form-label">Address</label>
        <textarea id="address" name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $farm->address ?? '') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea id="description" name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $farm->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <input type="hidden" name="is_active" value="0">
        <div class="form-check">
            <input id="is_active" type="checkbox" name="is_active" value="1" class="form-check-input @error('is_active') is-invalid @enderror" @checked(old('is_active', $farm->is_active ?? true))>
            <label for="is_active" class="form-check-label">Active</label>
            @error('is_active')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-success">{{ $submit }}</button>
    <a href="{{ route('farms.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
