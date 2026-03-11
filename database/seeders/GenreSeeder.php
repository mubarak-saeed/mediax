<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        $genres = [
            'أكشن',
            'دراما',
            'كوميدي',
            'رعب',
            'رومانسي',
            'تاريخي',
            'خيال علمي',
            'مغامرات',
            'تشويق',
            'وثائقي',
            'عائلي',
            'بوليسي',
            'حربي',
            'فانتازيا',
            'رياضي',
            'سيرة ذاتية',
        ];

        foreach ($genres as $name) {
            Genre::query()->firstOrCreate(['name' => $name]);
        }
    }
}
