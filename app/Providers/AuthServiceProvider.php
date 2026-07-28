<?php

namespace App\Providers;

use App\Models\Batch;
use App\Models\DailyRecord;
use App\Models\Expense;
use App\Models\FeedRecord;
use App\Models\MedicineRecord;
use App\Models\MortalityRecord;
use App\Models\User;
use App\Models\WeightRecord;
use App\Policies\BatchPolicy;
use App\Policies\DailyRecordPolicy;
use App\Policies\ExpensePolicy;
use App\Policies\FeedRecordPolicy;
use App\Policies\MedicineRecordPolicy;
use App\Policies\MortalityRecordPolicy;
use App\Policies\UserPolicy;
use App\Policies\WeightRecordPolicy;
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
        Expense::class => ExpensePolicy::class,
        FeedRecord::class => FeedRecordPolicy::class,
        MedicineRecord::class => MedicineRecordPolicy::class,
        MortalityRecord::class => MortalityRecordPolicy::class,
        WeightRecord::class => WeightRecordPolicy::class,
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
