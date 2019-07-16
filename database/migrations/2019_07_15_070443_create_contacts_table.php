<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('team_id')->index('contacts_team_id_index')->nullable();
            $table->string('unsubscribed_status')->nullable();
            $table->string('first_name')->index('contacts_first_name_index')->nullable();
            $table->string('last_name')->index('contacts_last_name_index')->nullable();
            $table->string('phone')->index('contacts_phone_index');
            $table->string('email')->index('contacts_email_index')->nullable();
            $table->integer('sticky_phone_number_id')->nullable();
            $table->string('twitter_id')->index('contacts_twitter_id_index')->nullable();
            $table->string('fb_messenger_id')->index('contacts_fb_messenger_id_index')->nullable();
            $table->index(['team_id', 'phone'],'idx_phone_search');
            $table->string('time_zone')->nullable();
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
        Schema::dropIfExists('contacts');
    }
}
