<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class UserSeeder extends Seeder
{
//    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
//        $this->disableForeignKeys();

        // Add the master administrator, user id of 1
        User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'user_role' => 1,
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => bcrypt('secret'),
        ]);

//        $this->enableForeignKeys();
    }
}
