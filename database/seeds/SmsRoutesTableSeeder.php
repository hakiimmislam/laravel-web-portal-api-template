<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsRoutesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('sms_routes')->insert([
        	'name' => 'office.gonow.my',
            'country_code' => '60',
            'priority' => '1',
            'url' => 'http://office.gonow.my:5010/cgi-bin/sendsms?username=smGWrootuser&password=Hbfc7e7177d8BTf7ee04d9fc0e&smsc=gsm1&charset=UTF-8&coding=2',
            'recipient' => '&to=',
            'message' => '&text=',
            'success_msg' => '0', // first character of response - 0: Accepted for delivery
            'active' => true
        ]);

        DB::table('sms_routes')->insert([
        	'name' => 'SMSS360',
            'country_code' => '60',
            'priority' => '2',
            'url' => 'https://www.smss360.com/api/sendsms.php?email=christine.cheong@dwave.my&key=276428519272a612cf030451bdfa1e2f',
            'recipient' => '&recipient=',
            'message' => '&message=DW:%20',
            'success_msg' => '1606',
            'active' => true
        ]);

        DB::table('sms_routes')->insert([
        	'name' => 'EvoSMS',
            'country_code' => '60',
            'priority' => '3',
            'url' => 'http://evosms.asuscomm.com:13022/smsapi/mt.php?customer=china',
            'recipient' => '&msisdn=',
            'message' => '&sms_text=',
            'success_msg' => '2',
            'active' => true
        ]);

        DB::table('sms_routes')->insert([
        	'name' => 'hengbaoapp.gonow.my',
            'country_code' => '855',
            'priority' => '1',
            'url' => 'http://hengbaoapp.gonow.my:5020/cgi-bin/sendsms?username=hbappgwuser&password=317H964adLfPd63132VacdddUJ7B23H8Hee4Lk&smsc=gsm1&charset=UTF-8&coding=2',
            'recipient' => '&to=',
            'message' => '&text=',
            'success_msg' => '0',
            'active' => true
        ]);
    }
}
