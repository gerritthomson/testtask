<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_chimp_lists', function (Blueprint $table) {
            $table->string('id');
            $table->string("campaign_defaults");
            $table->string("contact");
            $table->boolean("email_type_option");
            $table->string("mailchimp_id")->nullable();
            $table->string("name");
            $table->string("notify_on_subscribe")->nullable();
            $table->string("notify_on_unsubscribe")->nullable();
            $table->string("permission_reminder");
            $table->boolean("use_archive_bar")->nullable();
            $table->string("visibility")->nullable();
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
        Schema::dropIfExists('mail_chimp_lists');
    }
}
