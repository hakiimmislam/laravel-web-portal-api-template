<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('country_code')->nullable();
            $table->string('priority');
            $table->string('url');
            $table->string('recipient');
            $table->string('message');
            $table->string('success_msg');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_routes');
    }
}
