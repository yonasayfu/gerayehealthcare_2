<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify email configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        try {
            Mail::raw('This is a test email from Laravel Boilerplate', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email from Laravel Boilerplate');
            });

            $this->info("Test email sent successfully to {$email}");
        } catch (\Exception $e) {
            $this->error("Failed to send test email: " . $e->getMessage());
        }
    }
}
