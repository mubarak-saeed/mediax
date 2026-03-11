<?php

namespace Database\Factories;

use App\Models\Content;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    protected $model = Season::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = fake()->numberBetween(1, 5);

        return [
            'content_id' => Content::factory()->series(),
            'season' => (string) $number,
            'title' => "الموسم {$number}",
            'description' => fake()->paragraph(),
            'poster_url' => 'thumbnails/season-'.fake()->uuid().'.jpg',
        ];
    }
}
