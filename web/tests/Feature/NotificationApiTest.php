<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Models\Notification;
use App\Models\User;

class NotificationApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();

        $response = $this->actingAs($this->user)->json('POST', route('follow', [
            'id' => $this->user2->id,
        ]));
    }

    /**
     * @test
     */
    public function 通知一覧正しいJSONを返す()
    {
        $response = $this->actingAs($this->user2)
                        ->json('GET', '/api/notification');
    
        $response->assertStatus(200)
                ->assertJsonFragment([
                    'visiter_id' => "1",
                    'visited_id' => "2",
                    'action' => 'follow',
                    'checked' => true,
                    'visiter' => [
                        'id' => $this->user->id,
                        'is_false_notification' => [
                            false, 0
                        ],
                        'is_followed' => true,
                        'is_following' => false,
                        'name' => $this->user->name,
                    ]
                ]);
    }

    /**
     * @test
     */
    public function followした際に通知が作成される()
    {
        $this->assertEquals(1, $this->user->followings()->count());
        $this->assertEquals(1, $this->user2->passive_notifications()->count());

        $this->assertDatabaseHas('notifications', [
            'visiter_id' => "1",
            'visited_id' => "2",
            'action' => 'follow',
            'checked' => false,
        ]);
    }

    /**
     * @test
     */
    public function follow中のユーザーが投稿した場合通知が作成される()
    {
        $data = [
            'user_id' => $this->user2->id,
            'content' => 'TestContent',
            'myid' => 'testid',
            'platform' => 'PS4',
            'legend' => 'レイス',
            'private' => false
        ];

        $this->actingAs($this->user2)
            ->json('POST', route('post.create'), $data);

        $this->actingAs($this->user)
            ->json('GET', '/api/notification');

        $this->assertEquals(1, $this->user->passive_notifications()->count());

        $this->assertDatabaseHas('notifications', [
            'visiter_id' => "2",
            'visited_id' => "1",
            'action' => 'post',
            'checked' => 1,
        ]);
    }

    /**
     * @test
     */
    public function 通知を既読にできること()
    {
        $response = $this->actingAs($this->user2)
                        ->json('GET', '/api/notification');

        $this->assertDatabaseHas('notifications', [
            'visiter_id' => "1",
            'visited_id' => "2",
            'action' => 'follow',
            'checked' => true,
        ]);
    }
}
