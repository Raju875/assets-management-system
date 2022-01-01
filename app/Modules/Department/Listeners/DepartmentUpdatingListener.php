<?php

namespace App\Modules\Department\Listeners;

use App\Libraries\CommonFunction;
use App\Modules\Department\Events\DepartmentUpdatingEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DepartmentUpdatingListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\DepartmentUpdatingEvent  $event
     * @return void
     */
    public function handle(DepartmentUpdatingEvent $event)
    {
        $auth_user_id = CommonFunction::getUserId();

        $event->department->created_by = $auth_user_id;
        $event->department->updated_by = $auth_user_id;
    }
}
