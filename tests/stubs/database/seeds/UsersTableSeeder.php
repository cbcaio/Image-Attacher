<?php
namespace CbCaio\ImgAttacher\Testing;

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        User::unguard();
        User::create([
             'name' => 'user_1',
             'email' => 'user_1@localhost.com',
             'password' => bcrypt('123456'),
         ]);
        User::create([
             'name' => 'user_2',
             'email' => 'user_2@localhost.com',
             'password' => bcrypt('123456'),
         ]);
    }
}