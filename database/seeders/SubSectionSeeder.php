<?php

namespace Database\Seeders;

use App\Enums\ContentType;
use App\Models\Section;
use App\Models\SubSection;
use Illuminate\Database\Seeder;

class SubSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private function getSlug(string $name): string
    {
        $slug = trim($name);
        $slug = mb_strtolower($slug, 'UTF-8');
        $slug = preg_replace('/[^\x{0600}-\x{06FF}a-z0-9]+/u', '-', $slug); // يسمح بالحروف العربية والأرقام
        $slug = trim($slug, '-');

        return $slug;
    }

    public function run(): void
    {

        $seriesSection = Section::query()->where('slug', $this->getSlug('المسلسلات'))->first();
        if ($seriesSection) {
            $seriesSubsections = [
                ['name' => 'مسلسلات عربية متجددة', 'slug' => 'series-arabic-ongoing', 'type' => 'series', 'order' => 1],
                ['name' => 'مسلسلات عربية مكتملة', 'slug' => 'series-arabic-completed', 'type' => 'series', 'order' => 2],
                ['name' => 'مسلسلات تركية متجددة', 'slug' => 'series-turkish-ongoing', 'type' => 'series', 'order' => 3],
                ['name' => 'مسلسلات تركية مكتملة', 'slug' => 'series-turkish-completed', 'type' => 'series', 'order' => 4],
                ['name' => 'مسلسلات أجنبية متجددة', 'slug' => 'series-foreign-ongoing', 'type' => 'series', 'order' => 5],
                ['name' => 'مسلسلات أجنبية مكتملة', 'slug' => 'series-foreign-completed', 'type' => 'series', 'order' => 6],
                ['name' => 'مسلسلات أجنبية متعددة الأجزاء', 'slug' => 'series-foreign-multipart', 'type' => 'series', 'order' => 7],
                ['name' => 'مسلسلات كورية متجددة', 'slug' => 'series-korean-ongoing', 'type' => 'series', 'order' => 8],
                ['name' => 'مسلسلات كورية', 'slug' => 'series-korean', 'type' => 'series', 'order' => 9],
                ['name' => 'مسلسلات أنمي متجددة', 'slug' => 'series-anime-ongoing', 'type' => 'series', 'order' => 10],
                ['name' => 'مسلسلات أنمي مكتملة', 'slug' => 'series-anime-completed', 'type' => 'series', 'order' => 11],
                ['name' => 'مسلسلات سورية', 'slug' => 'series-syrian', 'type' => 'series', 'order' => 12],
                ['name' => 'مسلسلات خليجية', 'slug' => 'series-gulf', 'type' => 'series', 'order' => 13],
                ['name' => 'مسلسلات يمنية', 'slug' => 'series-yemeni', 'type' => 'series', 'order' => 14],
                ['name' => 'مسلسلات وبرامج رمضان 2026', 'slug' => 'series-ramadan-2025', 'type' => 'series', 'order' => 19],
            ];

            foreach ($seriesSubsections as $data) {
                $data['slug'] = $this->getSlug($data['name']);
                SubSection::query()->firstOrCreate(
                    ['slug' => $data['slug']],
                    array_merge($data, ['section_id' => $seriesSection->id])
                );
            }
        }

        $moviesSection = Section::query()->where('slug', $this->getSlug('الافلام'))->first();
        if ($moviesSection) {
            $moviesSubsections = [
                ['name' => 'أفلام عربية', 'slug' => 'movies-arabic', 'type' => 'movie', 'order' => 1],
                ['name' => 'أفلام أجنبية', 'slug' => 'movies-foreign', 'type' => 'movie', 'order' => 2],
                ['name' => 'أفلام تركية', 'slug' => 'movies-turkish', 'type' => 'movie', 'order' => 3],
                ['name' => 'أفلام هندية', 'slug' => 'movies-indian', 'type' => 'movie', 'order' => 4],
                ['name' => 'أفلام آسيوية', 'slug' => 'movies-asian', 'type' => 'movie', 'order' => 5],
                ['name' => 'أفلام أنمي', 'slug' => 'movies-anime', 'type' => 'movie', 'order' => 6],
                ['name' => 'أفلام رعب', 'slug' => 'movies-horror', 'type' => 'movie', 'order' => 7],
                ['name' => 'أفلام مدبلجة', 'slug' => 'movies-dubbed', 'type' => 'movie', 'order' => 8],
                ['name' => 'أفلام متسلسلة', 'slug' => 'movies-series', 'type' => ContentType::MovieSerial, 'order' => 9],
                ['name' => 'أفلام مميزة ومطلوبة', 'slug' => 'movies-featured-requested', 'type' => 'movie', 'order' => 10],
            ];

            foreach ($moviesSubsections as $data) {
                $data['slug'] = $this->getSlug($data['name']);
                SubSection::query()->firstOrCreate(
                    ['slug' => $data['slug']],
                    array_merge($data, ['section_id' => $moviesSection->id])
                );
            }
        }

        $booksSection = Section::query()->where('slug', $this->getSlug('كتب و روايات'))->first();
        if ($booksSection) {
            $booksSubsections = [
                ['name' => 'كتب دينية', 'slug' => 'books-religious', 'type' => 'book', 'order' => 1],
                ['name' => 'كتب أدبية', 'slug' => 'books-literature', 'type' => 'book', 'order' => 2],
                ['name' => 'كتب علمية', 'slug' => 'books-scientific', 'type' => 'book', 'order' => 3],
                ['name' => 'كتب تاريخية', 'slug' => 'books-historical', 'type' => 'book', 'order' => 4],
            ];

            foreach ($booksSubsections as $data) {
                $data['slug'] = $this->getSlug($data['name']);
                SubSection::query()->firstOrCreate(
                    ['slug' => $data['slug']],
                    array_merge($data, ['section_id' => $booksSection->id])
                );
            }
        }
    }
}
