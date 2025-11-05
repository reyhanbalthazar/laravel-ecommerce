<?php
// database/seeders/UsersTableSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@laravelstore.com',
        ]);

        // Create regular users
        User::factory(20)->create();

        // Create some unverified users
        User::factory(5)->unverified()->create();

        $this->command->info('✅ Users seeded successfully!');
        $this->command->info('👑 Admin user: admin@laravelstore.com / password');
    }
}
