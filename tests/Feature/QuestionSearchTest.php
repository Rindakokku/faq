<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class QuestionSearchTest extends TestCase
{
    use DatabaseTransactions;

    private function getUser()
    {
        // Remove test user if exists
        \App\User::whereEmail('test@user.com')->forceDelete();

        $user = factory(\App\User::class)->create([
            'email' => 'test@user.com',
            'password' => bcrypt('test123')
        ]);

        return $user;

    }

    /**
     * Test question search using hashtags
     *
     * @return void
     */
    public function testSearch()
    {
        $question = factory(\App\Question::class)->make();
        $question->body = "test #asd #cool";
        $question->user()->associate($this->getUser());
        $question->save();
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=asd');
        $questions = json_decode($response->getContent(), TRUE);

        $this->assertEquals(count($questions), 1, "Expected 1 question. Found 0.");
        $this->assertEquals($questions[0]['body'], $question->body);
    }
}
