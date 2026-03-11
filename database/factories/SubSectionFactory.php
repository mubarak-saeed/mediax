<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubSection>
 */
class SubSectionFactory extends Factory
{
    protected $model = SubSection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $slug = Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999);

        return [
            'section_id' => Section::factory(),
            'name' => $name,
            'slug' => $slug,
            'type' => fake()->randomElement(['movie', 'series', 'book']),
            'order' => fake()->numberBetween(0, 50),
        ];
    }
}
