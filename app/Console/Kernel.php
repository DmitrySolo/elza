<?php

namespace App\Console;

use App\Http\Controllers\BitrixController;
use App\Http\Controllers\DocumentController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
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
            $model->reviewBitrix($bitrix->getLastOrders());
            $model->reviewBitrix($bitrix->getLastOrders('ove-cfo.ru'));
        })->everyFiveMinutes();
        $schedule->call(function () {
            $doc=new DocumentController();
            $doc->import();
        })->hourly();

        $schedule->call(function () {
            $model=new Task();
            $model->reviewTasks();
        })->everyFiveMinutes();

    }
}
