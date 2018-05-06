<?php

namespace Tests\Feature;

use Tests\TestCase;

class AuthTest extends TestCase
{

    public function testLoginPost()
    {
        // Remove test user if exists
        \App\User::whereEmail('test@user.com')->forceDelete();

        // Create test user
        $user = factory(\App\User::class)->create([
            'email' => 'test@user.com',
            'password' => bcrypt('test123')
        ]);

        // Login user
        $this->visit(route('login'))
            ->type($user->email, 'email')
            ->type('manvi123', 'password')
            ->press('Login');
    }
}