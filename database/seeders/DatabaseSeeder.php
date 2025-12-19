<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            DocumentTypeSeeder::class,
            RoleSeeder::class
        ]);


        $role = Role::find(1);

        $role->users()->create([
            'username' => 'user.test',
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $role->users()->create([
            'username' => 'ivan_dev',
            'email' => 'idev_x@gmail.com',
            'password' => 'password'
        ]);

        // User::factory(10)->create();

    }
}
