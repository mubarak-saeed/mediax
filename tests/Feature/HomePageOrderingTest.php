<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Episode;
use App\Models\Section;
use App\Models\Season;
use App\Models\SubSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageOrderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_orders_series_by_latest_added_episode(): void
    {
        $section = Section::factory()->create([
            'name' => 'مسلسلات',
            'slug' => 'series',
            'type' => 'series',
            'status' => 'active',
            'order' => 1,
        ]);

        $subSection = SubSection::factory()->create([
            'section_id' => $section->id,
            'name' => 'مسلسلات عربية',
            'slug' => 'arabic-series',
            'type' => 'series',
            'order' => 1,
        ]);

        $olderSeries = Content::factory()->series()->create([
            'sub_section_id' => $subSection->id,
            'title' => 'مسلسل أقدم',
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        $newerSeries = Content::factory()->series()->create([
            'sub_section_id' => $subSection->id,
            'title' => 'مسلسل أحدث',
            'created_at' => now()->subDays(20),
            'updated_at' => now()->subDays(20),
        ]);

        $olderSeason = Season::factory()->create(['content_id' => $olderSeries->id, 'season' => '1']);
        $newerSeason = Season::factory()->create(['content_id' => $newerSeries->id, 'season' => '1']);

        Episode::factory()->create([
            'season_id' => $olderSeason->id,
            'title' => 'حلقة قديمة',
            'episode_number' => 1,
            'file_url' => 'private/old-episode.mp4',
            'created_at' => now()->subDays(5),
            'updated_at' => now()->subDays(5),
        ]);

        Episode::factory()->create([
            'season_id' => $newerSeason->id,
            'title' => 'حلقة أحدث',
            'episode_number' => 1,
            'file_url' => 'private/new-episode.mp4',
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder(['مسلسل أحدث', 'مسلسل أقدم'], false);
    }

    public function test_home_page_orders_non_series_by_latest_added_content(): void
    {
        $section = Section::factory()->create([
            'name' => 'أفلام',
            'slug' => 'movies',
            'type' => 'movie',
            'status' => 'active',
            'order' => 1,
        ]);

        $subSection = SubSection::factory()->create([
            'section_id' => $section->id,
            'name' => 'أفلام عربية',
            'slug' => 'arabic-movies',
            'type' => 'movie',
            'order' => 1,
        ]);

        Content::factory()->movie()->create([
            'sub_section_id' => $subSection->id,
            'title' => 'فيلم أقدم',
            'file_url' => 'private/old-movie.mp4',
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);

        Content::factory()->movie()->create([
            'sub_section_id' => $subSection->id,
            'title' => 'فيلم أحدث',
            'file_url' => 'private/new-movie.mp4',
            'created_at' => now()->subHour(),
            'updated_at' => now()->subHour(),
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder(['فيلم أحدث', 'فيلم أقدم'], false);
    }
}
