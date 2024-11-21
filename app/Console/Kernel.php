<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

  protected $commands = [];

  /**
   * Define the application's command schedule.
   */
  protected function schedule(Schedule $schedule): void {

    $schedule->command('queue:work --queue=high,default --stop-when-empty')->everyMinute()->withoutOverlapping();
  }

  /**
   * Register the commands for the application.
   */
  protected function commands(): void {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
