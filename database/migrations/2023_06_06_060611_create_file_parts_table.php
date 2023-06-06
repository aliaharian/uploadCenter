<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_parts', function (Blueprint $table) {
            $table->id();
            $table->json('data');
            $table->foreignId('file_meta_id')->constrained('file_metas')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('offset');
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
        Schema::dropIfExists('file_parts');
    }
}