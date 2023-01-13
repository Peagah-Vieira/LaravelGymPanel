<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Member::factory(500)->create();
    }
}
