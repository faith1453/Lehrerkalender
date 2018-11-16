<?php

namespace App\Http\Controllers;

use App\Models\ClassSemester;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\SchoolClass;
use App\Models\SemesterTeacherSubject;
use App\Models\Student;
use App\Models\StudentExam;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class APIController extends Controller
{
    static $timeslotMapping = [
        '07:45:00' => 1,
        '08:30:00' => 2,
        '09:35:00' => 3,
        '10:20:00' => 4,
        '11:25:00' => 5,
        '12:10:00' => 6,
        '13:15:00' => 7,
        '14:00:00' => 8,
        '15:05:00' => 9,
        '15:50:00' => 10
    ];

    public function createNewTeacher(Request $request) : Response
    {
        $user = $request->user();
        if(empty($user) || !$user->is_admin) {
            return response('No permission', 403);
        }
        $fillData = $request->all();
        $fillData['password'] = bcrypt($fillData['password']);
        $teacher = new Teacher($fillData);
        try {
            if ($teacher->save()) {
                return response($teacher->id);
            }
        } catch (QueryException $e) {
            // Do some logging here if necessary
        }
        return response('Failed', 400);
    }

    public function createNewStudent(Request $request) : Response
    {
        $user = $request->user();
        if(empty($user) || !$user->is_admin) {
            return response('No permission', 403);
        }
        $student = new Student($request->all());
        try {
            if(!$student->save()) {
                return response('Failed', 400);
            }
            if($request->has('class_id')) {
                $class = SchoolClass::find($request->get('class_id'));
                if(!empty($class)) {
                    $guest_period_start = $request->get('guest_period_start', null);
                    $guest_period_end = $request->get('guest_period_end', null);
                    $pivotData = [];
                    if(!($guest_period_start === null) && !($guest_period_end === null)) {
                        $pivotData['guest_period_start'] = Carbon::parse($guest_period_start);
                        $pivotData['guest_period_end'] = Carbon::parse($guest_period_end);
                    }
                    $student->classes()->attach($class, $pivotData);
                }
            }
            return response($student->id);
        } catch (QueryException $e) {
            // Do some logging here if necessary
        }
        return response('Failed', 400);
    }

    public function createNewSchoolClass(Request $request) : Response
    {
        $user = $request->user();
        if(empty($user) || !$user->is_admin) {
            return response('No permission', 403);
        }
        $schoolClass = new SchoolClass($request->all());
        try {
            if($schoolClass->save()) {
                return response($schoolClass->id);
            }
        } catch (QueryException $e) {
            // Do some logging here if necessary
        }
        return response('Failed', 400);
    }

    public function getClasses(Request $request) : Response
    {
        /** @var Teacher $teacher */
        $teacher = $request->user();
        $semesterTeacherSubjects = $teacher->semesterTeacherSubjects()->get();
        $classes = [];
        /** @var SemesterTeacherSubject $semesterTeacherSubject */
        foreach($semesterTeacherSubjects as $semesterTeacherSubject) {
            $classes[] = $semesterTeacherSubject->classSemester->schoolClass->toArray();
        }
        return response(json_encode(collect($classes)->unique()));
    }

    public function getSubjects(Request $request) : Response
    {
        /** @var Teacher $teacher */
        $teacher = $request->user();
        $subjects = $teacher->subjects->toArray();
        return response(json_encode($subjects));
    }

    public function getLessons(Request $request, $year = null, $week = null) : Response
    {
        /** @var Teacher $teacher */
        $teacher = $request->user();;
        $processedLessons = [];
        foreach($teacher->semesterTeacherSubjects as $semesterTeacherSubject) {
            $lessonsQuery = $semesterTeacherSubject->lessons()->with(['semesterTeacherSubject.classSemester.schoolClass', 'semesterTeacherSubject.subjects']);
            $oneWeek = new \DateInterval('P7D');
            if($year === null || $week === null) {
                $referenceDate = Carbon::now()->startOfWeek();
            } else {
                $referenceDate = Carbon::now()->setISODate($year, $week)->startOfWeek();
            }
            $referenceFutureDate = Carbon::parse($referenceDate)->add($oneWeek);
            $lessonsQuery->where('start', '>=', $referenceDate)
                ->where('end', '<=', $referenceFutureDate);
            /** @var Lesson $lesson */
            foreach($lessonsQuery->get() as $lesson) {
                $startMapping = static::$timeslotMapping[$lesson->start->format('H:i:s')];
                $endMapping = static::$timeslotMapping[$lesson->end->format('H:i:s')];
                $lesson->startNumber = $startMapping;
                $lesson->endNumber = $endMapping;
                $lesson->class_id = $lesson->semesterTeacherSubject->classSemester->class_id;
                $lesson->subject_id = $lesson->semesterTeacherSubject->subjects()->first()->id;
                $processedLessons[] = $lesson;
            }
        }
        $processedLessonsCollection = collect($processedLessons);
        $groupedLessons = $processedLessonsCollection->groupBy(function($processedLesson) {
            return $processedLesson->start->dayOfWeek;
        });
        $finalGroupedLessons = [];
        foreach($groupedLessons as $index => $lessonGroup) {
            $finalGroupedLessons[$index] = [];
            foreach($lessonGroup as $lesson) {
                $finalGroupedLessons[$index][$lesson->startNumber] = $lesson->toArray();
            }
        }
        return response(json_encode($finalGroupedLessons));
    }

    public function saveLesson(Request $request) : Response
    {
        $lessonData = $request->get('lesson', []);
        if(array_key_exists('id', $lessonData)) {
            $lesson = Lesson::find($lessonData['id']);
        } else {
            $lesson = new Lesson();
        }
        $lesson->topic = $lessonData['topic'];
        $startTime = new Carbon();
        $startTime->setISODate($request->get('year'), $request->get('week'), $request->get('day'));
        $startTime->setTimeFromTimeString(array_search($lessonData['startNumber'], static::$timeslotMapping));
        $lesson->start = $startTime;
        $endTime = new Carbon();
        $endTime->setISODate($request->get('year'), $request->get('week'), $request->get('day'));
        $endTime->setTimeFromTimeString(array_search($lessonData['endNumber'], static::$timeslotMapping));
        $lesson->end = $endTime;
        /** @var Teacher $teacher */
        $teacher = $request->user();
        $subjectId = $lessonData['subject_id'];
        $classId = $lessonData['class_id'];
        /** @var Builder $semesterQuery */
        $semesterTeacherSubject = SemesterTeacherSubject::whereHas('subjects', function($query) use ($subjectId) {
            $query->where('subjects.id', '=', $subjectId);
        })
            ->whereHas('teachers', function($query) use ($teacher) {
            $query->where('teachers.id', '=', $teacher->id);
        })
            ->whereHas('classSemester', function($query) use ($classId, $startTime) {
            $query->where('class_id', '=', $classId)
                ->where('start', '<', $startTime->format('Y-m-d'))
                ->where('end', '>', $startTime->format('Y-m-d'));
        })->first();
        $lesson->semesterTeacherSubject()->associate($semesterTeacherSubject);
        if($lesson->save()) {
            /** @var SchoolClass $class */
            $class = SchoolClass::find($classId);
            $lesson->students()->sync($class->students()->get());
            return response($lesson->id);
        }
        return response('Failure', 400);
    }

    public function getLessonDates(Request $request, $classId) {
        /** @var Teacher $teacher */
        $teacher = $request->user();
        /** @var SchoolClass $schoolClass */
        $schoolClass = SchoolClass::find($classId);
        $students = $schoolClass->students;
        $validLessons = collect();
        foreach($students as $student) {
            $validLessons = $validLessons->merge($student->lessons()->whereHas('semesterTeacherSubject', function($query) use ($teacher) {
                $query->whereHas('teachers', function($query) use ($teacher) {
                    $query->where('teachers.id', '=', $teacher->id);
                });
            })->get());
        }
        return response(json_encode($validLessons->unique('id')->map(function($lesson) {return $lesson->toArray();})->sortBy('start')));
    }

    public function getStudents($classId) {
        /** @var SchoolClass $schoolClass */
        $schoolClass = SchoolClass::find($classId);
        return response(json_encode($schoolClass->students->toArray()));
    }

    public function getClassLessonGrades(Request $request, $classId) {
        /** @var Teacher $teacher */
        $teacher = $request->user();
        /** @var SchoolClass $schoolClass */
        $schoolClass = SchoolClass::find($classId);
        $students = $schoolClass->students;
        $validLessons = collect();
        foreach($students as $student) {
            $validLessons = $validLessons->merge($student->lessons()->whereHas('semesterTeacherSubject', function($query) use ($teacher) {
                $query->whereHas('teachers', function($query) use ($teacher) {
                    $query->where('teachers.id', '=', $teacher->id);
                });
            })->get());
        }
        return response(json_encode($validLessons->pluck('pivot')->groupBy(['lesson_id', 'student_id'])->toArray()));
    }

    public function saveGrades(Request $request) {
        $gradesData = collect($request->get('grades', []))->flatten(2);
        foreach($gradesData as $gradeData) {
            DB::table('lesson_student')
                ->where('student_id', '=', $gradeData['student_id'])
                ->where('lesson_id', '=', $gradeData['lesson_id'])
                ->update(['grade' => $gradeData['grade']]);
        }
    }

    public function newExam(Request $request, $classId) {
        /** @var Teacher $teacher */
        $teacher = $request->user();

        /** @var SemesterTeacherSubject $applicableSemesterTeacherSubject */
        $applicableSemesterTeacherSubject = $teacher->semesterTeacherSubjects()->whereHas('classSemester', function($query) use ($classId) {
            $query->where('class_id', '=', $classId);
        })->first();

        $class = $applicableSemesterTeacherSubject->classSemester->schoolClass;

        $exam = new Exam();
        $exam->name = 'Neue Klausur '.Carbon::now()->format('Y-m-d h:i:s');
        $exam->max_points = 0;
        $exam->semesterTeacherSubject()->associate($applicableSemesterTeacherSubject);
        $exam->save();

        foreach($class->students as $student) {
            $studentExam = new StudentExam();
            $studentExam->exam()->associate($exam);
            $studentExam->student()->associate($student);
            $studentExam->save();
        }
    }

    public function getExams(Request $request, $classId) {
        /** @var Teacher $teacher */
        $teacher = $request->user();

        /** @var SemesterTeacherSubject $applicableSemesterTeacherSubject */
        $applicableSemesterTeacherSubject = $teacher->semesterTeacherSubjects()->whereHas('classSemester', function($query) use ($classId) {
            $query->where('class_id', '=', $classId);
        })->first();

        $exams = $applicableSemesterTeacherSubject->exams()->with('tasks', 'studentExams', 'studentExams.tasks', 'studentExams.student')->get();

        return response(json_encode($exams->toArray()));
    }
}