<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AdminSeeder::class);
        $this->call(StudentSeeder::class);
        $this->call(ClassSeeder::class);
        $this->call(ClassStudentSeeder::class);
        $this->call(ClassSemesterSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(TeacherSubjectSeeder::class);
        $this->call(ClassSemesterTeacherSubjectSeeder::class);
    }
}
