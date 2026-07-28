<?php

namespace App\Http\Requests\Settings;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('settings.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'app_name' => ['required', 'string', 'max:80'],
            'business_name' => ['required', 'string', 'max:120'],
            'owner_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:120'],
            'address' => ['nullable', 'string', 'max:500'],
            'default_locale' => ['required', Rule::in(array_keys(config('localization.names', ['bn' => 'Bangla', 'en' => 'English'])))],
            'timezone' => ['required', 'string', 'max:80'],
            'currency_code' => ['required', 'string', 'size:3'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'fiscal_year_start_month' => ['required', Rule::in($this->months())],
            'low_stock_alert_enabled' => ['nullable', 'boolean'],
            'due_alert_enabled' => ['nullable', 'boolean'],
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated();
        $data['currency_code'] = strtoupper($data['currency_code']);
        $data['low_stock_alert_enabled'] = (string) (int) $this->boolean('low_stock_alert_enabled');
        $data['due_alert_enabled'] = (string) (int) $this->boolean('due_alert_enabled');

        return $data;
    }

    private function months(): array
    {
        return [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];
    }
}
