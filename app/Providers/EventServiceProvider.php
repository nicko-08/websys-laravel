<?php

namespace App\Providers;

use App\Events\BudgetItemModified;
use App\Events\BudgetModified;
use App\Events\ExpenseModified;
use App\Events\FiscalYearModified;
use App\Events\UserModified;
use App\Events\GovernmentUnitModified;
use App\Listeners\LogModelModification;
use App\Listeners\AutoRecalculateAnalytics;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        BudgetModified::class => [
            LogModelModification::class,
            AutoRecalculateAnalytics::class,
        ],
        BudgetItemModified::class => [
            LogModelModification::class,
            AutoRecalculateAnalytics::class,
        ],
        ExpenseModified::class => [
            LogModelModification::class,
            AutoRecalculateAnalytics::class,
        ],
        GovernmentUnitModified::class => [
            LogModelModification::class,
        ],
        FiscalYearModified::class => [
            LogModelModification::class,
            AutoRecalculateAnalytics::class,
        ],
        UserModified::class => [
            LogModelModification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
