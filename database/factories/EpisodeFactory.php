<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    protected $model = Episode::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'episode_number' => fake()->numberBetween(1, 24),
            'title' => 'الحلقة '.fake()->numberBetween(1, 24),
            'description' => fake()->paragraph(),
            'duration' => fake()->numberBetween(20, 50),
            'poster_url' => 'thumbnails/ep-'.fake()->uuid().'.jpg',
            'file_url' => 'private/ep-'.fake()->uuid().'.mp4',
        ];
    }
}
