<?php

use App\Model\CWS_USERS;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CWS_USERS::create([
            'nama'=> 'admin',
            'email'=>'admin@admin.com',
            'no_hp'=>'08123123',
            'level'=>'1',
            'status'=>'1',
            'password' => bcrypt('123456'),
            'is_deleted' => 0,
            'is_email_verified' => 1,
        ]);

        CWS_USERS::create([
            'nama'=> 'Lalan Fathurrahman',
            'email'=>'lalanfathurrahman@gmail.com',
            'no_hp'=>'08123123123',
            'level'=>'2',
            'status'=>'1',
            'password' => bcrypt('123456'),
            'is_deleted' => 0,
            'is_email_verified' => 1,
        ]);
        
    }
}
