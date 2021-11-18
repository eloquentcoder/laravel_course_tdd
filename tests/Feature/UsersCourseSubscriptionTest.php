<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Notifications\SubscribeCourse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UsersCourseSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_can_subscribe_to_test()
    {
        Notification::fake();

        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->assertFalse($user->isSubscribedToCourse($course));

        $response = $this->actingAs($user)->post(route('course.subscribe', $course));
        $response->assertRedirect(route('course.show', $course));

        $this->assertTrue($user->isSubscribedToCourse($course));

        Notification::assertSentTo($user, SubscribeCourse::class, function($notification) use ($course) {
            return $notification->course->id == $course->id;
        });

    }

    /** @test */
    public function check_if_a_user_is_subscribed_to_a_course()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create();

        $this->assertFalse($user->isSubscribedToCourse($course));
        $user->subscribeToCourse($course);
        $this->assertTrue($user->isSubscribedToCourse($course));
    }

    /** @test */
    public function users_cannot_subscribe_when_already_subscribed()
    {
        Notification::fake();

        $user = User::factory()->create();
        $course = Course::factory()->create();

        $user->subscribeToCourse($course);

        $response = $this->actingAs($user)->post(route('course.subscribe', $course));
        $response->assertStatus(302);
       
    }
}
