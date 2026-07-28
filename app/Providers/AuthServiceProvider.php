<?php

namespace App\Providers;

use App\Models\Batch;
use App\Models\DailyRecord;
use App\Models\User;
use App\Policies\BatchPolicy;
use App\Policies\DailyRecordPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Batch::class => BatchPolicy::class,
        DailyRecord::class => DailyRecordPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
