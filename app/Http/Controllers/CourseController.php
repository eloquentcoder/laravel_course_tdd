<?php

namespace App\Http\Controllers;
;
use App\Models\User;
use App\Models\Course;
use App\Notifications\SubscribeCourse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function show()
    {
        
    }

    public function subscribe(Course $course)
    {
        $user = User::find(auth()->id());
        $user->subscribeToCourse($course);
        $user->isSubscribedToCourse($course);

        $user->notify(new SubscribeCourse($course));

        return redirect()->route('course.show', $course);
    }
}
