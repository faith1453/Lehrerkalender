<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\SchoolClass;
use App\Models\SemesterTeacherSubject;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        return response(json_encode($classes));
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
        if(array_key_exists($lessonData, 'id')) {
            $lesson = Lesson::find($lessonData['id']);
        } else {
            $lesson = new Lesson();
        }
        $lesson->fill($lessonData);
        if($lesson->save()) {
            return response($lesson->id);
        }
        return response('Failure', 400);
    }
}