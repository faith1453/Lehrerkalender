<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStudentExamTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_exam_task', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_exam_id');
            $table->unsignedInteger('exam_task_id');
            $table->foreign('student_exam_id')->references('id')->on('student_exams');
            $table->foreign('exam_task_id')->references('id')->on('exam_tasks');
            $table->decimal('points', 6, 2);
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
        Schema::dropIfExists('student_exam_task');
    }
}
