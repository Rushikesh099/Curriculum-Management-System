<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Scheme;
use App\Models\SchemeLevel;
use App\Models\Course;

class CourseCreditValidationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_audit_course_requires_credits_and_must_be_at_least_one()
    {
        // create a scheme and level (non-audit) to attach course
        $scheme = Scheme::create([
            'programme_name' => 'Test Prog',
            'programme_code' => 'TP01',
            'year' => 2026,
        ]);

        $level = SchemeLevel::create([
            'scheme_id' => $scheme->id,
            'level_name' => 'Level 1',
            'is_audit' => false,
            'courses_offered' => 1,
            'th' => 0,
            'tu' => 0,
            'pr' => 0,
            'total_hours' => 0,
            'total_credits' => 3,
            'marks' => 0,
        ]);

        $course = Course::create([
            'scheme_id' => $scheme->id,
            'scheme_level_id' => $level->id,
            'programme_code' => $scheme->programme_code,
            'course_code' => 'C01',
            'course_title' => 'Course One',
            'credits' => 2,
            'total_hours' => 0,
            'marks' => 0,
            'type' => 'compulsory',
            'is_audit' => 0,
        ]);

        // attempt update with missing credits
        $response = $this->post(route('courses.update', $course->id), [
            'course_code' => 'C01',
            'course_title' => 'Course One',
            // omit credits field entirely
        ]);
        $response->assertSessionHasErrors('credits');

        // attempt update with zero credits
        $response = $this->post(route('courses.update', $course->id), [
            'course_code' => 'C01',
            'course_title' => 'Course One',
            'credits' => 0,
        ]);
        $response->assertSessionHasErrors('credits');

        // valid update (include other required fields to avoid DB errors)
        $response = $this->post(route('courses.update', $course->id), [
            'course_code' => 'C01',
            'course_title' => 'Course One Updated',
            'credits' => 3,
            'total_hours' => 0,
            'marks' => 0,
            'type' => 'compulsory',
            // fill exam breakdown fields so the controller doesn't write null
            'theory_hours' => 0,
            'theory_marks' => 0,
            'test_marks' => 0,
            'pr_marks' => 0,
            'or_marks' => 0,
            'tw_marks' => 0,
        ]);
        // debug: make sure redirect and session are correct
        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');

        $course->refresh();
        
        // if the credits did not update, dump DB for debugging
        if ($course->credits !== 3) {
            dump('after update credits in DB: '.$course->credits);
            dump(Course::find($course->id));
        }

        $this->assertEquals(3, $course->credits);
    }

    /** @test */
    public function non_audit_scheme_store_requires_credits_and_minimum()
    {
        // set up scheme and non-audit level
        $scheme = Scheme::create([
            'programme_name' => 'Test Prog 2',
            'programme_code' => 'TP02',
            'year' => 2026,
        ]);
        $level = SchemeLevel::create([
            'scheme_id' => $scheme->id,
            'level_name' => 'Level X',
            'is_audit' => false,
            'courses_offered' => 1,
            'th' => 0,
            'tu' => 0,
            'pr' => 0,
            'total_hours' => 0,
            // we'll ask for 2 credits below, so set limit accordingly
            'total_credits' => 2,
            'marks' => 0,
        ]);

        // missing credits should fail
        $payload = ['courses' => [0 => ['course_code' => 'C1','course_title'=>'T1']]];
        $this->post(route('scheme.storeCourses', ['scheme' => $scheme->id, 'level' => $level->id]), $payload)
             ->assertSessionHasErrors('courses.0.credits');

        // credits less than 1 should fail
        $payload['courses'][0]['credits'] = 0;
        $this->post(route('scheme.storeCourses', ['scheme' => $scheme->id, 'level' => $level->id]), $payload)
             ->assertSessionHasErrors('courses.0.credits');

        // valid credits should succeed and persist
        $payload['courses'][0]['credits'] = 2;
        $this->post(route('scheme.storeCourses', ['scheme' => $scheme->id, 'level' => $level->id]), $payload)
             ->assertSessionHasNoErrors();
        $record = Course::where('scheme_level_id',$level->id)->first();
        $this->assertNotNull($record);
        $this->assertEquals(2,$record->credits);
    }

    /** @test */
    public function audit_course_hides_credits_and_always_saves_zero()
    {
        // create audit scheme level / course
        $scheme = Scheme::create([
            'programme_name' => 'Audit Prog',
            'programme_code' => 'AP01',
            'year' => 2026,
        ]);

        $level = SchemeLevel::create([
            'scheme_id' => $scheme->id,
            'level_name' => 'Audit Level',
            'is_audit' => true,
            'courses_offered' => 1,
            'th' => 0,
            'tu' => 0,
            'pr' => 0,
            'total_hours' => 0,
            'total_credits' => 0,
            'marks' => 0,
        ]);

        $course = Course::create([
            'scheme_id' => $scheme->id,
            'scheme_level_id' => $level->id,
            'programme_code' => $scheme->programme_code,
            'course_code' => 'A01',
            'course_title' => 'Audit Course',
            'credits' => 0,
            'total_hours' => 0,
            'marks' => 0,
            'type' => 'compulsory',
            'is_audit' => 1,
        ]);

        // editing without credits field should succeed
        $response = $this->post(route('courses.update', $course->id), [
            'course_code' => 'A01',
            'course_title' => 'Audit Course Updated',
            // no credits passed
        ]);
        $response->assertSessionHasNoErrors();
        $course->refresh();
        $this->assertEquals(0, $course->credits);

        // now test scheme-level storeCourses behaviour
        $payload = [
            'courses' => [
                0 => [
                    'course_code' => 'B1',
                    'course_title' => 'Something',
                    // no credits field for audit level
                ],
            ],
        ];

        $this->post(route('scheme.storeCourses', ['scheme' => $scheme->id, 'level' => $level->id]), $payload)
             ->assertSessionHasNoErrors();

        $fresh = Course::where('scheme_level_id', $level->id)->first();
        $this->assertNotNull($fresh);
        $this->assertEquals(0, $fresh->credits);
    }
}
