<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("description");
            $table->unsignedBigInteger('type_id')->nullable();
            $table->boolean("featured")->default(false);
            $table->boolean("active")->default(false);
            $table->integer("fact_index")->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->float('long')->nullable();
            $table->float('lat')->nullable();
            $table->string('era')->nullable();
            $table->string('source_links')->nullable();
            $table->string('location')->nullable();
            $table->text('history_fact')->nullable();
            $table->dateTime("start_date")->nullable();
            $table->dateTime("end_date")->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->foreign('country_id')->references('id')->on('countries');
            $table->foreign('author_id')->references('id')->on('authors');
            $table->foreign('type_id')->references('id')->on('article_types');
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
        Schema::dropIfExists('articles');
    }
}
