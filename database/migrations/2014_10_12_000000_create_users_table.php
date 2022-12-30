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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->text('email')->nullable();
            $table->text('mobile_no')->nullable();
            $table->text('password')->nullable();
            $table->text('decrypted_password')->nullable();
            $table->text('profile_pic')->nullable();
            $table->date('dob')->nullable();
            $table->integer('age')->nullable();
            $table->integer('gender')->default(1)->comment('1->Male,2->Female,3->Other');
            $table->text('bio')->nullable();
            $table->text('location')->nullable();
            $table->text('images')->nullable();
            $table->text('video')->nullable();
            $table->text('shot_video')->nullable();
            $table->string('rate_per_minite',255)->nullable();
            $table->text('otp')->nullable();
            $table->dateTime('otp_created_at')->nullable();
            $table->enum('role', [1,2,3,4])->nullable()->comment('1->Admin,2->Sub Admin,3->User,4->Fake User');
            $table->integer('estatus')->default(1)->comment('1->Active,2->Deactive,3->Deleted,4->Pending');
            $table->dateTime('last_login_date')->nullable();
            $table->dateTime('created_at')->default(\Carbon\Carbon::now());
            $table->dateTime('updated_at')->default(null)->onUpdate(\Carbon\Carbon::now());
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
