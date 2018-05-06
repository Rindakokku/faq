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
     * Test Case 1: One question with one hashtag and matching/non-matching hashtag search
     *
     * @return void
     */
    public function testCase1()
    {
        $question = factory(\App\Question::class)->make();
        $question->body = "test #asdq";
        $question->user()->associate($this->getUser());
        $question->save();

        // Matching hashtag search
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=asdq');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 1, "Expected 1 question. Found 0.");
        $this->assertEquals($questions[0]['body'], $question->body);

        // Non-matching hashtag search
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=asdd');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 0, "Expected 0 questions.");
    }

    /**
     * Test Case 2: One question with two hashtags and matching/non-matching hashtag search
     *
     * @return void
     */
    public function testCase2()
    {
        $question = factory(\App\Question::class)->make();
        $question->body = "test #asdq #lopl";
        $question->user()->associate($this->getUser());
        $question->save();

        // Matching single hashtag search
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=asdq');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 1, "Expected 1 question. Found 0.");
        $this->assertEquals($questions[0]['body'], $question->body);

        // Matching double hashtag search
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=asdq,lopl');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 1, "Expected 1 question. Found 0.");
        $this->assertEquals($questions[0]['body'], $question->body);

        // Non-matching double hashtag search
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=asdd,coolio');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 0, "Expected 0 questions.");
    }

    /**
     * Test Case 3: One question with no hashtags and hashtag search using existing words
     *
     * @return void
     */
    public function testCase3()
    {
        $question = factory(\App\Question::class)->make();
        $question->body = "test this please";
        $question->user()->associate($this->getUser());
        $question->save();

        // Hashtag search using existing words in question body
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=test,this,please');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 0, "Expected 0 questions.");
    }

    /**
     * Test Case 4: Two question with hashtags and matching hashtag search
     *
     * @return void
     */
    public function testCase4()
    {
        $question1 = factory(\App\Question::class)->make();
        $question1->body = "iopasd #thisq";
        $question1->user()->associate($this->getUser());
        $question1->save();

        $question2 = factory(\App\Question::class)->make();
        $question2->body = "asdasd #lopsd";
        $question2->user()->associate($this->getUser());
        $question2->save();

        // Hashtag search using existing words in question body
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=thisq,lopsd');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 2, "Expected 2 questions.");
        $this->assertEquals($questions[0]['body'], $question1->body);
        $this->assertEquals($questions[1]['body'], $question2->body);
    }

    /**
     * Test Case 5: Three questions with hashtags at different offsets in body (start, middle, end)
     *
     * @return void
     */
    public function testCase5()
    {
        $question1 = factory(\App\Question::class)->make();
        $question1->body = "#startyo asdfsd asd asdiw";
        $question1->user()->associate($this->getUser());
        $question1->save();

        $question2 = factory(\App\Question::class)->make();
        $question2->body = "asdasjj #midyo asdkajsdl ljkasd";
        $question2->user()->associate($this->getUser());
        $question2->save();

        $question3 = factory(\App\Question::class)->make();
        $question3->body = "asdlkjasd askdjasdk asdkjl #endyo";
        $question3->user()->associate($this->getUser());
        $question3->save();

        // Hashtag search using existing words in question body
        $response = $this->actingAs($this->getUser())->call('GET', '/questions/search?tags=startyo,midyo,endyo');
        $questions = json_decode($response->getContent(), TRUE);
        $this->assertEquals(count($questions), 3, "Expected 3 questions.");
        $this->assertEquals($questions[0]['body'], $question1->body);
        $this->assertEquals($questions[1]['body'], $question2->body);
        $this->assertEquals($questions[2]['body'], $question3->body);
    }
}
