<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('from_user_id');
            $table->string('review', 1024);
            $table->unsignedSmallInteger('estimate');
            $table->unsignedInteger('reviewable_id');
            $table->string('reviewable_type');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('reviews', function (Blueprint $table){
            $table->foreign('from_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
