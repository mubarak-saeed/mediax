<?php

namespace Tests\Feature;

use App\Enums\AccessLevel;
use App\Models\Content;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_watch_page(): void
    {
        $content = Content::factory()->movie()->create();

        $response = $this->get(route('watch.show', $content));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_read_page(): void
    {
        $content = Content::factory()->book()->create();

        $response = $this->get(route('read.show', $content));

        $response->assertRedirect(route('login'));
    }

    public function test_user_without_subscription_is_redirected_from_watch(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $content = Content::factory()->movie()->create(['access_level_required' => AccessLevel::Basic]);

        $response = $this->actingAs($user)->get(route('watch.show', $content));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('status');
    }

    public function test_user_without_subscription_is_redirected_from_read(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $content = Content::factory()->book()->create(['access_level_required' => AccessLevel::Basic]);

        $response = $this->actingAs($user)->get(route('read.show', $content));

        $response->assertRedirect(route('profile'));
        $response->assertSessionHas('status');
    }

    public function test_admin_can_access_watch_without_subscription(): void
    {
        /** @var User $admin */
        $admin = User::factory()->admin()->create();
        $content = Content::factory()->movie()->create([
            'file_url' => 'test/video.mp4',
            'access_level_required' => AccessLevel::Premium,
        ]);

        $response = $this->actingAs($admin)->get(route('watch.show', $content));

        $response->assertOk();
    }

    public function test_user_can_access_free_content_without_subscription(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $content = Content::factory()->book()->create([
            'access_level_required' => AccessLevel::Free,
            'file_url' => 'test/book.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('read.show', $content));

        $response->assertOk();
    }

    public function test_home_page_is_accessible(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_content_detail_page_is_accessible(): void
    {
        $content = Content::factory()->movie()->create();

        $response = $this->get(route('content.show', $content));

        $response->assertStatus(200);
    }

    public function test_search_page_is_accessible(): void
    {
        $response = $this->get(route('search', ['q' => 'test']));

        $response->assertStatus(200);
    }

    public function test_plans_page_is_accessible(): void
    {
        $response = $this->get(route('subscription.plans'));

        $response->assertStatus(200);
    }
}
