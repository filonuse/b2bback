<?php

namespace App\Console\Commands;

use App\Events\ReminderStart;
use App\Models\Reminder;
use Illuminate\Console\Command;

class ReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ReminderNotification:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        list($date_at, $time_at, $day) = explode(' ', date('Y-m-d H:i w'));

        $reminders = Reminder::query()
            ->where(function ($query) use ($date_at, $day) {
                $query
                    ->whereDate('date_at', '=', $date_at)
                    ->orWhere('on_days', 'LIKE', '%' . $day . '%');
            })
            ->whereTime('time_at', '=', $time_at)
            ->where('activated', true)
            ->with('user')
            ->get();

        if ($reminders->count()) {
            foreach ($reminders as $reminder) {
                event(new ReminderStart($reminder));

                if ($reminder->on_days === null) {
                    $reminder->delete();
                }
            }
        }
    }
}
