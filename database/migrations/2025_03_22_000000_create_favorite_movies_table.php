<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoriteMoviesTable extends Migration
{
    public function up()
    {
        Schema::create('favorite_movies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('year');
            $table->string('poster');
            $table->string('genre');
            $table->string('rating');
            $table->string('runtime');
            $table->string('imdb_id')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorite_movies');
    }
}
