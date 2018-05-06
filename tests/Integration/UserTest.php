<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    function testUserSave()
    {
        factory(\App\User::class, 3)->create();
        $this->assertEquals(3, User::all()->count());
    }
}
 