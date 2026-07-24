<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
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
        // الأقسام الرئيسية فقط
        $parentSections = [
            ['name' => 'المسلسلات', 'slug' => 'series', 'type' => 'series', 'order' => 1],
            ['name' => 'الافلام', 'slug' => 'movies', 'type' => 'movie', 'order' => 2],
            ['name' => 'كتب و روايات', 'slug' => 'books', 'type' => 'book', 'order' => 3],
        ];

        foreach ($parentSections as $sectionData) {
            $sectionData['slug'] = $this->getSlug($sectionData['name']);
            Section::query()->firstOrCreate(
                ['slug' => $sectionData['slug']],
                $sectionData
            );
        }
    }
}
