<?php

namespace App\Policies;

use App\Course;
use App\PlatformUser;


class CoursePolicy
{

    public function show(PlatformUser $user, Course $course)
    {
        return $user->platform_id === $course->platform_id;
    }

    public function update(PlatformUser $user, Course $course)
    {
        return $user->platform_id === $course->platform_id;
    }

    public function delete(PlatformUser $user, Course $course)
    {
        return $user->platform_id === $course->platform_id;
    }

}
