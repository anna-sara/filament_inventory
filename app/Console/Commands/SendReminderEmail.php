<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reserveditem;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReturnDateReminder;
use Carbon\Carbon;

class SendReminderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send-reminder-email:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reservations = Reserveditem::where('return_date', '<=', Carbon::now()->add(5, 'days')->toDateTimeString())
        ->where('return_date', '>', Carbon::now()->toDateTimeString())
        ->where('notified', 0)
        ->get();

        if ($reservations) {
            foreach ($reservations as $reservation) {
                Mail::to($reservation['email'])
                ->send(new ReturnDateReminder($reservation));
                $reservation['notified'] = 1;
                $reservation->save();
            }
        }
    }
}
