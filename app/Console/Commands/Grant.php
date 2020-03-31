<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class Grant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'corona:grant {user} {role=admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign role to user';

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
     * @return mixed
     */
    public function handle()
    {
        $user = $this->argument('user');
        $role = $this->argument('role');

        try {
            User::where('username', $user)
                ->first()
                ->assign($role);

            $this->info("Succeed assigning role '$role' to user '$user'.");
        } catch (\Exception $e) {
            $this->error("Failed to assign role '$role' to user '$user'.");
        }
    }
}
