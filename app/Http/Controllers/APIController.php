<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class APIController extends Controller
{
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
}