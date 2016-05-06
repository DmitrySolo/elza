<?php

namespace App\Console;

use App\Http\Controllers\BitrixController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\TaskController;
use App\Models\Task;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $model=new Task();
            $bitrix=new BitrixController();
            $model->reviewBitrix($bitrix->getNewOrders());
        })->everyFiveMinutes();

        $schedule->call(function () {
            $model=new Task();
            $model->reviewTasks();
        })->everyFiveMinutes();

    }
}
