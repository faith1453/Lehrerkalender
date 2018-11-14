<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassSemesterTeacherSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_semester_teacher_subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('teacher_subject_id');
            $table->unsignedInteger('class_semester_id');
            $table->foreign('teacher_subject_id')->references('id')->on('teacher_subject');
            $table->foreign('class_semester_id')->references('id')->on('class_semesters');
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
        Schema::dropIfExists('class_semester_teacher_subjects');
    }
}
