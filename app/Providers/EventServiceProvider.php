<?php

namespace App\Providers;

use App\Listeners\QueryListener;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ExampleEvent' => [
            'App\Listeners\ExampleListener',
        ],
        'Illuminate\Database\Events\QueryExecuted' => [
            QueryListener::class
        ],
    ];
}
