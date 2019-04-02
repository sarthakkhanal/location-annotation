<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TweetsForLabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('location_tweets',function(Blueprint $table){
        $table->string('id')->primary();
        $table->string('category')->nullable();
        $table->text("text");
        $table->integer("relevance")->default(1);
      });
      Schema::create('location_tweet_labels',function(Blueprint $table){
        $table->increments('id');
        $table->integer('user_id')->unsigned();
        $table->foreign('user_id')->references('id')->on('users');
        $table->string('tweet_id');
        $table->foreign('tweet_id')->references('id')->on('location_tweets');
        $table->string('type');
        $table->string('location');
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
        Schema::drop('location_tweet_labels');
        Schema::drop('location_tweets');
    }
}
