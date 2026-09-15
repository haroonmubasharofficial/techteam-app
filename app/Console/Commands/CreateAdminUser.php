<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin {name} {email} {password}';
    protected $description = 'Create or update an administrator account';

    public function handle(): int
    {
        $user = User::updateOrCreate(
            ['email' => $this->argument('email')],
            ['name' => $this->argument('name'), 'password' => $this->argument('password'), 'role' => 'admin', 'is_active' => true]
        );
        $this->info("Administrator {$user->email} is ready.");
        return self::SUCCESS;
    }
}
