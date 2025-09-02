<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class TestPasswordResetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:password-reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test password reset functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Testing password reset for: {$email}");

        // Check if user exists
        $user = User::where('email', $email)->first();
        if (! $user) {
            $this->error("User with email {$email} not found");

            return 1;
        }

        $this->info("User found: {$user->name}");

        try {
            // Attempt to send password reset link
            $response = Password::sendResetLink(['email' => $email]);

            $this->info('Password reset response: '.$response);

            if ($response === Password::RESET_LINK_SENT) {
                $this->info('Password reset link sent successfully');
            } else {
                $this->error('Failed to send password reset link: '.$response);
            }
        } catch (\Exception $e) {
            $this->error('Exception occurred: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
