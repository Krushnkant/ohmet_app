<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_media', function (Blueprint $table) {
            $table->id();
            $table->integer('chat_id')->nullable();
            $table->longText('name')->nullable();
            $table->enum('type', ['image', 'video', 'content', 'audio', 'document', 'contact', 'location'])->nullable();
            $table->enum('is_delete', [0, 1])->nullable();
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
        Schema::dropIfExists('chat_media');
    }
};
