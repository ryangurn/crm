<?php

namespace App\Console\Commands;

use App\Models\Reference\Timezone;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\confirm;

class UserAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:user-add {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (config('app.env') == 'production' && !$this->option('force')) {
            $this->warn('Cannot add users directly to production without --force');
            return;
        }

        $name = text('Name', required: true);
        $email = text('Email', required: true);
        $emailVerified = confirm('Email Verified', false);
        $timezones = Timezone::all()->pluck('name');
        if (count($timezones) != 0) {
            $timezone = select('Timezone', options: Timezone::all()->pluck('name'));
        } else {
            $timezone = null;
        }

        // check if the user already exists
        if (User::where('email', $email)->count() != 0) {
            $this->warn('A user already exists with email: ' . $email);
            return;
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->email_verified_at = $emailVerified ? now() : null;
        $user->timezone_id = ($timezone != null) ? Timezone::where('name', $timezone)->get()->id : null;
        $user->password = Hash::make('password');
        $user->save();
        
        $this->info('User: '. $email);
        $this->info('Password: password');
    }
}
