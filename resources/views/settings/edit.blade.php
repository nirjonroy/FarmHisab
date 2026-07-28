@extends('layouts.app')

@section('title', __('modules.settings').' - '.($appName ?? __('common.app_name')))
@section('page_title', __('modules.settings'))
@section('breadcrumbs')
    <li class="breadcrumb-item active" aria-current="page">{{ __('modules.settings') }}</li>
@endsection

@section('content')
    <form method="POST" action="{{ route('settings.update') }}" class="settings-form" novalidate>
        @csrf
        @method('PUT')

        <div class="settings-hero mb-4">
            <div>
                <span class="settings-eyebrow">{{ __('settings.system_settings') }}</span>
                <h2>{{ __('settings.manage_application') }}</h2>
                <p>{{ __('settings.manage_application_description') }}</p>
            </div>
            <button type="submit" class="btn btn-light settings-save-button">{{ __('common.save') }}</button>
        </div>

        <div class="row g-4">
            <div class="col-xl-7">
                <div class="card border-0 shadow-sm settings-card">
                    <div class="card-body">
                        <h3>{{ __('settings.general_information') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.app_name') }}</label>
                                <input type="text" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" class="form-control @error('app_name') is-invalid @enderror" required maxlength="80">
                                @error('app_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.business_name') }}</label>
                                <input type="text" name="business_name" value="{{ old('business_name', $settings['business_name']) }}" class="form-control @error('business_name') is-invalid @enderror" required maxlength="120">
                                @error('business_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.owner_name') }}</label>
                                <input type="text" name="owner_name" value="{{ old('owner_name', $settings['owner_name']) }}" class="form-control @error('owner_name') is-invalid @enderror" maxlength="120">
                                @error('owner_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.phone') }}</label>
                                <input type="text" name="phone" value="{{ old('phone', $settings['phone']) }}" class="form-control @error('phone') is-invalid @enderror" maxlength="40">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.email') }}</label>
                                <input type="email" name="email" value="{{ old('email', $settings['email']) }}" class="form-control @error('email') is-invalid @enderror" maxlength="120">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('settings.address') }}</label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" maxlength="500">{{ old('address', $settings['address']) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-5">
                <div class="card border-0 shadow-sm settings-card">
                    <div class="card-body">
                        <h3>{{ __('settings.localization_finance') }}</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.default_locale') }}</label>
                                <select name="default_locale" class="form-select @error('default_locale') is-invalid @enderror" required>
                                    @foreach ($locales as $locale => $label)
                                        <option value="{{ $locale }}" @selected(old('default_locale', $settings['default_locale']) === $locale)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('default_locale')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.timezone') }}</label>
                                <select name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                                    @foreach ($timezones as $timezone)
                                        <option value="{{ $timezone }}" @selected(old('timezone', $settings['timezone']) === $timezone)>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                                @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.currency_code') }}</label>
                                <input type="text" name="currency_code" value="{{ old('currency_code', $settings['currency_code']) }}" class="form-control text-uppercase @error('currency_code') is-invalid @enderror" required maxlength="3">
                                @error('currency_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('settings.currency_symbol') }}</label>
                                <input type="text" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" class="form-control @error('currency_symbol') is-invalid @enderror" required maxlength="10">
                                @error('currency_symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">{{ __('settings.fiscal_year_start_month') }}</label>
                                <select name="fiscal_year_start_month" class="form-select @error('fiscal_year_start_month') is-invalid @enderror" required>
                                    @foreach ($months as $month)
                                        <option value="{{ $month }}" @selected(old('fiscal_year_start_month', $settings['fiscal_year_start_month']) === $month)>{{ __('settings.months.'.$month) }}</option>
                                    @endforeach
                                </select>
                                @error('fiscal_year_start_month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm settings-card mt-4">
                    <div class="card-body">
                        <h3>{{ __('settings.notifications') }}</h3>
                        <div class="settings-toggle-row">
                            <div>
                                <strong>{{ __('settings.low_stock_alert_enabled') }}</strong>
                                <p>{{ __('settings.low_stock_alert_description') }}</p>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="low_stock_alert_enabled" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="low_stock_alert_enabled" value="1" @checked((bool) old('low_stock_alert_enabled', $settings['low_stock_alert_enabled']))>
                            </div>
                        </div>
                        <div class="settings-toggle-row">
                            <div>
                                <strong>{{ __('settings.due_alert_enabled') }}</strong>
                                <p>{{ __('settings.due_alert_description') }}</p>
                            </div>
                            <div class="form-check form-switch">
                                <input type="hidden" name="due_alert_enabled" value="0">
                                <input class="form-check-input" type="checkbox" role="switch" name="due_alert_enabled" value="1" @checked((bool) old('due_alert_enabled', $settings['due_alert_enabled']))>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
