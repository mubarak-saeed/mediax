<?php

namespace Database\Factories;

use App\Enums\AccessLevel;
use App\Enums\ContentType;
use App\Models\Content;
use App\Models\SubSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContentFactory extends Factory
{
    protected $model = Content::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement(ContentType::cases());

        return [
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'type' => $type,
            'language' => 'Arabic',
            'release_year' => fake()->year(),
            'duration' => $type === ContentType::Book ? null : (string) fake()->numberBetween(60, 180),
            'poster_url' => 'thumbnails/'.fake()->uuid().'.jpg',
            'file_url' => 'private/'.fake()->uuid().($type === ContentType::Book ? '.pdf' : '.mp4'),
            'access_level_required' => fake()->randomElement(AccessLevel::cases()),
            'created_by' => User::factory(),
            'sub_section_id' => SubSection::factory(),
        ];
    }

    public function movie(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::Movie,
        ]);
    }

    public function series(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::Series,
        ]);
    }

    public function book(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => ContentType::Book,
            'duration' => null,
        ]);
    }
}
