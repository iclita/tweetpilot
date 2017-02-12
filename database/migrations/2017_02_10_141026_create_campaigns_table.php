<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('website_id')->unsigned();
            $table->foreign('website_id')->references('id')->on('websites')->onDelete('cascade');
            $table->enum('type', ['post', 'like', 'retweet']);
            $table->enum('status', ['stopped', 'running', 'paused'])->default('stopped');
            $table->string('custom_message')->nullable(); // Custom message to post
            $table->string('custom_link')->nullable(); // Custom link to post
            $table->bigInteger('post_id')->unsigned()->nullable(); // Post ID for like/retweet
            $table->boolean('active')->default(1);
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
        Schema::dropIfExists('campaigns');
    }
}
