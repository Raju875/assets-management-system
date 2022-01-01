<?php

namespace App\Providers;

use App\Modules\Department;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        Department\Events\DepartmentCreatingEvent::class => [
            Department\Listeners\DepartmentCreatingListener::class
        ],
        Department\Events\DepartmentUpdatingEvent::class => [
            Department\Listeners\DepartmentUpdatingListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
