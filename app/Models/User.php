<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function createdFarms(): HasMany
    {
        return $this->hasMany(Farm::class, 'created_by');
    }

    public function createdSheds(): HasMany
    {
        return $this->hasMany(Shed::class, 'created_by');
    }

    public function createdFarmCategories(): HasMany
    {
        return $this->hasMany(FarmCategory::class, 'created_by');
    }

    public function setLocaleAttribute(?string $value): void
    {
        $supportedLocales = config('localization.supported_locales', ['bn', 'en']);
        $defaultLocale = config('localization.default_locale', 'bn');

        $this->attributes['locale'] = in_array($value, $supportedLocales, true)
            ? $value
            : $defaultLocale;
    }
}
