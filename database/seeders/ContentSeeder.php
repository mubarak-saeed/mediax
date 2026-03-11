<?php

namespace Database\Seeders;

use App\Enums\AccessLevel;
use App\Enums\ContentLanguage;
use App\Enums\ContentType;
use App\Models\Content;
use App\Models\Episode;
use App\Models\Genre;
use App\Models\Rating;
use App\Models\Section;
use App\Models\SubSection;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    private function getSlug(string $name): string
    {
        $slug = trim($name);
        $slug = mb_strtolower($slug, 'UTF-8');
        $slug = preg_replace('/[^\x{0600}-\x{06FF}a-z0-9]+/u', '-', $slug); // يسمح بالحروف العربية والأرقام
        $slug = trim($slug, '-');

        return $slug;
    }

    private const POSTERS = [
        'images/posters/poster1.jpeg',
        'images/posters/poster2.jpeg',
        'images/posters/poster3.jpeg',
        'images/posters/poster4.jpeg',
        'images/posters/poster5.jpeg',
        'images/posters/poster6.jpeg',
    ];

    private const default_video = 'default/default_video.mp4';

    private const default_book = 'default/default_book.pdf';

    public function run(): void
    {
        $user = User::query()->first();
        if (! $user) {
            $this->command->warn('أنشئ مستخدماً أولاً (مثلاً عبر User::factory()->create()).');

            return;
        }

        if (Section::query()->doesntExist()) {
            $this->call(SectionSeeder::class);
        }
        if (Genre::query()->doesntExist()) {
            $this->call(GenreSeeder::class);
        }
        $genres = Genre::query()->get();

        // جلب الأقسام الفرعية (sub_sections)
        $subSections = SubSection::query()->get()->keyBy('slug');

        $this->seedMovies($user, $subSections, $genres);
        $this->seedSeries($user, $subSections, $genres);
        $this->seedBooks($user, $subSections, $genres);
        $this->seedRatings($user);
    }

    private function randomViews(): int
    {
        return random_int(500, 40000);
    }

    private function poster(int $index): string
    {
        return self::POSTERS[$index % count(self::POSTERS)];
    }

    private function seedMovies(User $user, $subSections, $genres): void
    {
        // أفلام عربية (مستوحاة من منصات شاهد، وياك، نتفليكس عربي)
        $arabicMovies = [
            ['title' => 'رحلة إلى المجهول', 'description' => 'فيلم مغامرات مليء بالتحديات والمفاجآت في رحلة بحث عن الكنز المفقود.', 'release_year' => 2023, 'duration' => '120', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['أكشن', 'مغامرات']],
            ['title' => 'ظلال الماضي', 'description' => 'دراما إنسانية تعكس صراع الذكريات والغفران في قصة عائلية مؤثرة.', 'release_year' => 2024, 'duration' => '95', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['دراما', 'رومانسي']],
            ['title' => 'حب في القاهرة', 'description' => 'قصة حب رومانسية تدور في أزقة القاهرة القديمة بين شاب وفتاة من بيئتين مختلفتين.', 'release_year' => 2024, 'duration' => '98', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'سيرة الحب', 'description' => 'دراما ترصد حياة زوجين عبر أربعة عقود من الفرح والألم.', 'release_year' => 2022, 'duration' => '102', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما', 'رومانسي']],
            ['title' => 'ضحك حتى البكاء', 'description' => 'كوميديا سوداء عن مجموعة أصدقاء يقعون في مواقف كارثية في عطلة واحدة.', 'release_year' => 2024, 'duration' => '88', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي']],
            ['title' => 'ملوك الشمال', 'description' => 'دراما تاريخية عن صراع الممالك في حقبة التفتت والغزوات.', 'release_year' => 2022, 'duration' => '128', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'دراما']],
            ['title' => 'حكاية عتيقة', 'description' => 'دراما عائلية عن ثلاث أجيال في بيت واحد وذكريات لا تُنسى.', 'release_year' => 2022, 'duration' => '110', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما']],
            ['title' => 'صقور الصحراء', 'description' => 'فيلم حربي عن وحدة عسكرية في مهمة إنقاذ خلف خطوط العدو.', 'release_year' => 2023, 'duration' => '118', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['حربي', 'أكشن']],
            ['title' => 'المحقق ناصر', 'description' => 'فيلم بوليسي: محقق يطارد قاتلاً متسلسلاً في المدينة.', 'release_year' => 2024, 'duration' => '100', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['بوليسي', 'تشويق']],
            ['title' => 'أبطال الملعب', 'description' => 'دراما رياضية عن فريق كرة قدم من حي شعبي يحلم بالبطولة.', 'release_year' => 2022, 'duration' => '106', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رياضي', 'دراما']],
        ];

        // أفلام أجنبية (مستوحاة من نتفليكس، شاهد)
        $foreignMovies = [
            ['title' => 'ليلة في المتحف', 'description' => 'كوميديا عائلية عن حارس ليلي يكتشف أن المعروضات تنبض بالحياة ليلاً.', 'release_year' => 2022, 'duration' => '108', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي', 'مغامرات']],
            ['title' => 'البيت المسكون', 'description' => 'رعب نفسي في منزل قديم حيث تتفجر أسرار العائلة المظلمة.', 'release_year' => 2023, 'duration' => '112', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['رعب', 'تشويق']],
            ['title' => 'المحارب الأخير', 'description' => 'ملحمة أكشن في عالم ما بعد الكارثة يبحث فيها البطل عن آخر معقل للبشرية.', 'release_year' => 2023, 'duration' => '135', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'خيال علمي']],
            ['title' => 'الطريق إلى المجهول', 'description' => 'فيلم طريق ومغامرات عبر صحراء الجزيرة بحثاً عن مدينة مفقودة.', 'release_year' => 2021, 'duration' => '118', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['مغامرات', 'أكشن']],
            ['title' => 'سنة أولى حب', 'description' => 'روم كوميدي عن طالب جامعي يقع في الحب لأول مرة.', 'release_year' => 2024, 'duration' => '92', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'كوميدي']],
            ['title' => 'المهمة المستحيلة', 'description' => 'أكشن وتشويق: فريق نخبة لتنفيذ عملية سرية في قلب العدو.', 'release_year' => 2023, 'duration' => '122', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['أكشن', 'تشويق']],
            ['title' => 'عالم التنين', 'description' => 'فانتازيا: طفل يكتشف بوابة إلى عالم التنين والجن.', 'release_year' => 2024, 'duration' => '112', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['فانتازيا', 'مغامرات']],
            ['title' => 'الغزو القادم', 'description' => 'خيال علمي: غزو فضائي والبشر في معركة البقاء.', 'release_year' => 2024, 'duration' => '130', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'أكشن']],
            ['title' => 'رحلة إلى المريخ', 'description' => 'خيال علمي: أول بعثة بشرية مأهولة إلى المريخ.', 'release_year' => 2024, 'duration' => '125', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'مغامرات']],
            ['title' => 'قلب الشتاء', 'description' => 'رومانسي درامي في إطار ثلجي عن لقاء مصيري.', 'release_year' => 2022, 'duration' => '94', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
        ];

        // أفلام رعب
        $horrorMovies = [
            ['title' => 'منزل الأشباح', 'description' => 'رعب كلاسيكي عن عائلة تنتقل لمنزل مسكون.', 'release_year' => 2023, 'duration' => '98', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['رعب']],
            ['title' => 'الظلام الأبدي', 'description' => 'مجموعة أصدقاء يواجهون كابوسهم الحقيقي في الغابة.', 'release_year' => 2024, 'duration' => '105', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['رعب', 'تشويق']],
            ['title' => 'لعنة المرآة', 'description' => 'فتاة تكتشف أن المرآة القديمة تخفي وراءها شيئاً مرعباً.', 'release_year' => 2023, 'duration' => '92', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رعب']],
            ['title' => 'الساحرة', 'description' => 'رعب تاريخي عن قرية يسكنها الشر.', 'release_year' => 2022, 'duration' => '110', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['رعب', 'تاريخي']],
            ['title' => 'ليلة الصرخات', 'description' => 'فيلم رعب عن طلاب يقضون ليلة في مستشفى مهجور.', 'release_year' => 2024, 'duration' => '88', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رعب', 'تشويق']],
        ];

        // أفلام أنمي
        $animeMovies = [
            ['title' => 'سيف النار', 'description' => 'أنمي أكشن عن محارب يسعى لإنقاذ مملكته.', 'release_year' => 2024, 'duration' => '120', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'فانتازيا']],
            ['title' => 'روح الساموراي', 'description' => 'أنمي تاريخي عن رحلة ساموراي في اليابان القديمة.', 'release_year' => 2023, 'duration' => '115', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['أكشن', 'تاريخي']],
            ['title' => 'عالم الأحلام', 'description' => 'فانتازيا أنمي عن فتاة تدخل عالم أحلامها.', 'release_year' => 2024, 'duration' => '100', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['فانتازيا', 'مغامرات']],
            ['title' => 'معركة الآلهة', 'description' => 'أنمي ملحمي عن صراع بين الآلهة والبشر.', 'release_year' => 2023, 'duration' => '130', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'فانتازيا']],
            ['title' => 'حكاية شيرو', 'description' => 'قصة مؤثرة عن قط صغير وصداقته مع فتاة.', 'release_year' => 2022, 'duration' => '95', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['دراما', 'عائلي']],
        ];

        // أفلام هندية
        $indianMovies = [
            ['title' => 'حب في دلهي', 'description' => 'رومانسي هندي عن حب بين شاب وفتاة من طبقتين مختلفتين.', 'release_year' => 2024, 'duration' => '155', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'المحارب الهندي', 'description' => 'أكشن هندي ملحمي عن محارب يدافع عن قريته.', 'release_year' => 2023, 'duration' => '165', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تاريخي']],
            ['title' => 'رقصة المطر', 'description' => 'فيلم موسيقي هندي عن راقصة تحلم بالشهرة.', 'release_year' => 2024, 'duration' => '145', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'ملك الجريمة', 'description' => 'فيلم جريمة عن عالم المافيا في مومباي.', 'release_year' => 2023, 'duration' => '158', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تشويق']],
            ['title' => 'قصة حب سينمائية', 'description' => 'رومانسي كوميدي عن ممثل يقع في حب معجبة.', 'release_year' => 2024, 'duration' => '140', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'كوميدي']],
        ];

        // أفلام تركية (مستوحاة من شاهد، وياك)
        $turkishMovies = [
            ['title' => 'حب في إسطنبول', 'description' => 'رومانسي تركي عن حب يولد بين جارين.', 'release_year' => 2024, 'duration' => '115', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'الوادي الأخير', 'description' => 'أكشن تركي عن فريق خاص في مهمة خطيرة.', 'release_year' => 2023, 'duration' => '125', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تشويق']],
            ['title' => 'قلوب مجروحة', 'description' => 'دراما تركية عن عائلة تمر بأزمات صعبة.', 'release_year' => 2024, 'duration' => '110', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['دراما', 'رومانسي']],
            ['title' => 'أرض العثمانيين', 'description' => 'تاريخي تركي عن فترة الدولة العثمانية.', 'release_year' => 2022, 'duration' => '145', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'أكشن']],
        ];

        // أفلام عربية 2025 (مستوحاة من عروض المنصات)
        $arabic2025Movies = [
            ['title' => 'ليلة العيد', 'description' => 'كوميديا عائلية في ليلة العيد تتحول إلى سلسلة مواقف مضحكة.', 'release_year' => 2025, 'duration' => '95', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي', 'عائلي']],
            ['title' => 'شهر العسل', 'description' => 'رومانسي كوميدي عن زوجين في شهر العسل يواجهان مفاجآت لا تتوقعانها.', 'release_year' => 2025, 'duration' => '100', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'كوميدي']],
        ];

        // أفلام أجنبية 2025
        $foreign2025Movies = [
            ['title' => 'المختبر السري', 'description' => 'خيال علمي: تجارب سرية تخرج عن السيطرة وتهدد البشرية.', 'release_year' => 2025, 'duration' => '118', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'تشويق']],
            ['title' => 'الاختفاء الكبير', 'description' => 'تشويق: قرية كاملة تختفي دون أثر والمحققون في حيرة.', 'release_year' => 2025, 'duration' => '105', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['تشويق', 'دراما']],
        ];

        // أفلام آسيوية (مستوحاة من منصات وياك، شاهد)
        $asianMovies = [
            ['title' => 'سيف الساموراي', 'description' => 'أكشن آسيوي عن محارب يبحث عن الانتقام في اليابان القديمة.', 'release_year' => 2023, 'duration' => '125', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تاريخي']],
            ['title' => 'حديقة الرياح', 'description' => 'دراما آسيوية عن عائلة تكتشف أسراراً في حديقة الأجداد.', 'release_year' => 2024, 'duration' => '112', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما', 'رومانسي']],
        ];

        // أفلام مدبلجة (مستوحاة من شاهد، وياك)
        $dubbedMovies = [
            ['title' => 'البطل الخارق', 'description' => 'أكشن مدبلج: بطل خارق يواجه شراً يهدد المدينة.', 'release_year' => 2023, 'duration' => '128', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['أكشن', 'فانتازيا']],
            ['title' => 'حب في نيويورك', 'description' => 'رومانسي مدبلج عن لقاء مصادف يغير حياة شاب وفتاة.', 'release_year' => 2024, 'duration' => '98', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'كوميدي']],
        ];

        // أفلام متسلسلة (سلسلة أفلام)
        $seriesMovies = [
            ['title' => 'المحارب', 'description' => ' سلسلة أفلام المحارب في عالم ما بعد الكارثة.', 'release_year' => 2022, 'type' => ContentType::MovieSerial, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'خيال علمي']],
            ['title' => 'هاري بوتر', 'description' => 'الجزء الثاني من سلسلة افلام هاري.', 'release_year' => 2024, 'type' => ContentType::MovieSerial, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'خيال علمي']],
        ];

        // أفلام مميزة ومطلوبة
        $featuredRequestedMovies = [
            ['title' => 'المدينة المفقودة', 'description' => 'مغامرات في البحث عن مدينة أسطورية مخفية في الصحراء.', 'release_year' => 2024, 'duration' => '122', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['مغامرات', 'أكشن']],
            ['title' => 'الطبيب المعجزة', 'description' => 'دراما عن طبيب يضحي بكل شيء لإنقاذ مرضى قرية نائية.', 'release_year' => 2023, 'duration' => '108', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما']],
        ];

        // أفلام هندية 2025 (مستوحاة من وياك، شاهد)
        $indian2025Movies = [
            ['title' => 'رقصة الحب', 'description' => 'رومانسي هندي عن راقصة تحلم بالشهرة والحب في مومباي.', 'release_year' => 2025, 'duration' => '148', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'المحارب الهندي 2', 'description' => 'أكشن هندي ملحمي: المحارب يعود للدفاع عن قريته من غزو جديد.', 'release_year' => 2025, 'duration' => '165', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تاريخي']],
        ];

        // إدراج الأفلام العربية
        $arabicSection = $subSections->get($this->getSlug('أفلام عربية'));
        foreach ($arabicMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $arabicSection?->id);
        }

        // إدراج الأفلام الأجنبية
        $foreignSection = $subSections->get($this->getSlug('أفلام أجنبية'));
        foreach ($foreignMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $foreignSection?->id);
        }

        // إدراج أفلام الرعب
        $horrorSection = $subSections->get($this->getSlug('أفلام رعب'));
        foreach ($horrorMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $horrorSection?->id);
        }

        // إدراج أفلام الأنمي
        $animeSection = $subSections->get($this->getSlug('أفلام أنمي'));
        foreach ($animeMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $animeSection?->id);
        }

        // إدراج الأفلام الهندية
        $indianSection = $subSections->get($this->getSlug('أفلام هندية'));
        foreach ($indianMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $indianSection?->id);
        }

        // إدراج الأفلام التركية
        $turkishSection = $subSections->get($this->getSlug('أفلام تركية'));
        foreach ($turkishMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $turkishSection?->id);
        }

        // أفلام عربية 2025، أجنبية 2025، هندية 2025، آسيوية، مدبلجة، متسلسلة، مميزة
        $arabic2025Section = $subSections->get($this->getSlug('أفلام عربية 2025'));
        foreach ($arabic2025Movies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $arabic2025Section?->id);
        }
        $foreign2025Section = $subSections->get($this->getSlug('أفلام أجنبية 2025'));
        foreach ($foreign2025Movies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $foreign2025Section?->id);
        }
        $asianSection = $subSections->get($this->getSlug('أفلام آسيوية'));
        foreach ($asianMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $asianSection?->id);
        }
        $dubbedSection = $subSections->get($this->getSlug('أفلام مدبلجة'));
        foreach ($dubbedMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $dubbedSection?->id);
        }
        $seriesMoviesSection = $subSections->get($this->getSlug('أفلام متسلسلة'));
        foreach ($seriesMovies as $i => $data) {
            $data['type'] = ContentType::MovieSerial;
            $this->createMovie($data, $i, $user, $genres, $seriesMoviesSection?->id);
        }
        $featuredRequestedSection = $subSections->get($this->getSlug('أفلام مميزة ومطلوبة'));
        foreach ($featuredRequestedMovies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $featuredRequestedSection?->id);
        }
        $indian2025Section = $subSections->get($this->getSlug('أفلام هندية 2025'));
        foreach ($indian2025Movies as $i => $data) {
            $this->createMovie($data, $i, $user, $genres, $indian2025Section?->id);
        }
    }

    private function createMovie(array $data, int $index, User $user, $genres, ?int $subSectionId): void
    {
        $type = $data['type'] ?? ContentType::Movie;
        $genreNames = $data['genre_names'];
        unset($data['genre_names']);
        unset($data['access_level_required']);
        $data['duration'] = null;
        $data['file_size'] = null;
        $content = Content::query()->firstOrCreate(
            ['title' => $data['title']],
            array_merge($data, [
                'type' => $type,
                'language' => ContentLanguage::Arabic,
                'poster_url' => $this->poster($index),
                'file_url' => self::default_video,
                'created_by' => $user->id,
                'sub_section_id' => $subSectionId,
                'views_count' => $this->randomViews(),
            ])
        );
        $this->attachGenres($content, $genres, $genreNames);
    }

    private function seedSeries(User $user, $subSections, $genres): void
    {
        // مسلسلات عربية مكتملة
        $arabicCompletedSeries = [
            ['title' => 'أسوار الزمن', 'description' => 'مسلسل تاريخي يروي أحداث حقبة مهمة من تاريخ المنطقة بقالب درامي مشوق.', 'release_year' => 2023, 'duration' => '24 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 12], ['season' => '2', 'title' => 'الموسم الثاني', 'episodes' => 12]]],
            ['title' => 'عائلة ونص', 'description' => 'كوميديا عائلية يومية عن أب وأم وأطفالهم في مواقف مضحكة.', 'release_year' => 2022, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 30]]],
            ['title' => 'قلب المدينة', 'description' => 'دراما رومانسية في إطار عصري عن شباب المدينة وحبهم وطموحاتهم.', 'release_year' => 2024, 'duration' => '20 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 20]]],
            ['title' => 'سلالة النار', 'description' => 'تاريخي درامي عن سقوط مملكة وصعود أخرى في حقبة الحروب.', 'release_year' => 2022, 'duration' => '24 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'دراما', 'أكشن'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 12], ['season' => '2', 'title' => 'الموسم الثاني', 'episodes' => 12]]],
            ['title' => 'غداً نلتقي', 'description' => 'رومانسي درامي عن لقاء بعد سنوات وذكريات لم تُنسى.', 'release_year' => 2023, 'duration' => '15 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 15]]],
            ['title' => 'بيت الجدة', 'description' => 'عائلي كوميدي: أطفال يقضون الصيف عند جدة في القرية.', 'release_year' => 2023, 'duration' => '20 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['عائلي', 'كوميدي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 20]]],
        ];

        // مسلسلات عربية متجددة
        $arabicOngoingSeries = [
            ['title' => 'شفرات الظلام', 'description' => 'مسلسل تشويق وجريمة يتتبع تحقيقات محقق في سلسلة جرائم غامضة.', 'release_year' => 2024, 'duration' => '16 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['تشويق', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 8]]],
            ['title' => 'فريق النخبة', 'description' => 'أكشن: وحدة خاصة تواجه أخطر المهام في المنطقة.', 'release_year' => 2023, 'duration' => '12 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تشويق'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 6]]],
            ['title' => 'خط النار', 'description' => 'حربي: جنود في جبهة حرب وحكاياتهم الإنسانية.', 'release_year' => 2022, 'duration' => '14 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['حربي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 7]]],
            ['title' => 'سيرة حياة', 'description' => 'سيرة ذاتية درامية لشخصية عربية مؤثرة عبر العقود.', 'release_year' => 2024, 'duration' => '16 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['سيرة ذاتية', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 8]]],
        ];

        // مسلسلات أجنبية مكتملة
        $foreignCompletedSeries = [
            ['title' => 'الليل الطويل', 'description' => 'رعب وتشويق: سكان بلدة صغيرة يكتشفون أن الليل لا ينتهي.', 'release_year' => 2023, 'duration' => '10 حلقات', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['رعب', 'تشويق'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 10]]],
            ['title' => 'الوهم', 'description' => 'تشويق نفسي: طبيب نفسي يكتشف أن مريضه قد لا يكون من هذا العالم.', 'release_year' => 2024, 'duration' => '8 حلقات', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['تشويق', 'رعب'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 8]]],
            ['title' => 'المختبر السري', 'description' => 'خيال علمي: تجارب سرية تخرج عن السيطرة وتهدد البشرية.', 'release_year' => 2024, 'duration' => '10 حلقات', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'تشويق'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 10]]],
            ['title' => 'محطة النهاية', 'description' => 'بوليسي: محققون يتتبعون شبكة إجرامية في المدينة.', 'release_year' => 2023, 'duration' => '18 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['بوليسي', 'تشويق'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 18]]],
            ['title' => 'الاختفاء الكبير', 'description' => 'تشويق: قرية كاملة تختفي دون أثر والمحققون في حيرة.', 'release_year' => 2023, 'duration' => '10 حلقات', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['تشويق', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 10]]],
        ];

        // مسلسلات أجنبية متجددة
        $foreignOngoingSeries = [
            ['title' => 'مملكة الظلال', 'description' => 'فانتازيا: ممالك متصارعة وسحر قديم يتحكم بالمصير.', 'release_year' => 2023, 'duration' => '24 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['فانتازيا', 'مغامرات'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 12], ['season' => '2', 'title' => 'الموسم الثاني', 'episodes' => 6]]],
            ['title' => 'الفضاء البعيد', 'description' => 'خيال علمي: طاقم مركبة فضائية يواجهون المجهول.', 'release_year' => 2024, 'duration' => '12 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'مغامرات'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 6]]],
            ['title' => 'الغابة', 'description' => 'رعب: مجموعة ضائعة في غابة لا نهاية لها.', 'release_year' => 2023, 'duration' => '8 حلقات', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['رعب', 'تشويق'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 4]]],
        ];

        // مسلسلات تركية مكتملة
        $turkishCompletedSeries = [
            ['title' => 'أميرة الصحراء', 'description' => 'تاريخي رومانسي عن أميرة وقائد جيش في زمن الحروب.', 'release_year' => 2022, 'duration' => '28 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'رومانسي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 14], ['season' => '2', 'title' => 'الموسم الثاني', 'episodes' => 14]]],
            ['title' => 'حب وطموح', 'description' => 'رومانسي درامي عن شابين يلتقيان في الجامعة ومسارات الحياة.', 'release_year' => 2024, 'duration' => '25 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 25]]],
            ['title' => 'قلوب الورد', 'description' => 'دراما تركية عن قصة حب معقدة بين عائلتين.', 'release_year' => 2023, 'duration' => '35 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 35]]],
        ];

        // مسلسلات تركية متجددة
        $turkishOngoingSeries = [
            ['title' => 'وادي الذئاب', 'description' => 'أكشن تركي عن مافيا وعمليات سرية.', 'release_year' => 2024, 'duration' => '20 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['أكشن', 'تشويق'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 10]]],
            ['title' => 'العاصفة', 'description' => 'دراما تركية عن صراعات عائلة ثرية.', 'release_year' => 2024, 'duration' => '15 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما', 'رومانسي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 7]]],
        ];

        // مسلسلات كورية
        $koreanSeries = [
            ['title' => 'حب في سيول', 'description' => 'رومانسي كوري عن قصة حب بين مديرة ومدير تنفيذي.', 'release_year' => 2024, 'duration' => '16 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 16]]],
            ['title' => 'لعبة القوة', 'description' => 'إثارة كوري عن عالم الأعمال والمنافسة.', 'release_year' => 2023, 'duration' => '20 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تشويق', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 20]]],
            ['title' => 'أغنية الحب', 'description' => 'رومانسي موسيقي عن مغني ومعجبة.', 'release_year' => 2024, 'duration' => '12 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'كوميدي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 12]]],
        ];

        // مسلسلات أنمي مكتملة
        $animeCompletedSeries = [
            ['title' => 'محاربو النور', 'description' => 'أنمي أكشن عن فريق من المحاربين يحمون العالم.', 'release_year' => 2023, 'duration' => '24 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['أكشن', 'فانتازيا'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 24]]],
            ['title' => 'عالم الغد', 'description' => 'وثائقي درامي: سلسلة عن مستقبل التكنولوجيا والحياة.', 'release_year' => 2022, 'duration' => '12 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['وثائقي', 'خيال علمي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 12]]],
            ['title' => 'أبناء النور', 'description' => 'فانتازيا: أطفال يكتشفون أنهم من سلالة سحرية.', 'release_year' => 2024, 'duration' => '16 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['فانتازيا', 'عائلي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 16]]],
        ];

        // مسلسلات أنمي متجددة
        $animeOngoingSeries = [
            ['title' => 'ظل القمر', 'description' => 'أنمي فانتازيا عن ساحر يكتشف قواه.', 'release_year' => 2024, 'duration' => '12 حلقة', 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['فانتازيا', 'أكشن'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 6]]],
            ['title' => 'أبطال المدرسة', 'description' => 'أنمي مدرسي عن طلاب بقوى خارقة.', 'release_year' => 2024, 'duration' => '24 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['أكشن', 'كوميدي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 12]]],
        ];

        // مسلسلات رمضان 2025
        $ramadanSeries = [
            ['title' => 'ليالي رمضان', 'description' => 'دراما رمضانية عائلية عن عائلة كبيرة في رمضان.', 'release_year' => 2025, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['دراما', 'عائلي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 15]]],
            ['title' => 'كوميديا المحلة', 'description' => 'كوميديا رمضانية يومية في المحلة.', 'release_year' => 2025, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 15]]],
            ['title' => 'بوابة الشام', 'description' => 'دراما شامية رمضانية تاريخية.', 'release_year' => 2025, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['تاريخي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 15]]],
        ];

        // مسلسلات أخرى (سورية، خليجية، يمنية، هندية، إيرانية، كارتون، إسلامية وتاريخية) — مستوحاة من شاهد، وياك
        $otherSeries = [
            ['title' => 'باب الحارة', 'description' => 'دراما سورية عن الحارة الشامية وحكايات أهلها.', 'release_year' => 2023, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['دراما', 'تاريخي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 30]], 'subsection' => $this->getSlug('مسلسلات سورية')],
            ['title' => 'مسلسل خليجي', 'description' => 'دراما خليجية عائلية عن قيم المجتمع الخليجي.', 'release_year' => 2024, 'duration' => '20 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 20]], 'subsection' => $this->getSlug('مسلسلات خليجية')],
            ['title' => 'كوميديا السوق', 'description' => 'كوميديا يومية في سوق شعبي وشخصياته المتنوعة.', 'release_year' => 2021, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي', 'عائلي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 30]], 'subsection' => $this->getSlug('مسلسلات يمنية')],
            ['title' => 'حب في دلهي', 'description' => 'مسلسل هندي رومانسي مدبلج عن قصة حب بين شاب وفتاة من طبقتين مختلفتين.', 'release_year' => 2024, 'duration' => '24 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 24]], 'subsection' => $this->getSlug('مسلسلات هندية')],
            ['title' => 'الولي والعالم', 'description' => 'مسلسل إيراني درامي عن رحلة عالم في طريقه إلى المعرفة.', 'release_year' => 2023, 'duration' => '20 حلقة', 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما', 'تاريخي'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 20]], 'subsection' => $this->getSlug('مسلسلات إيرانية')],
            ['title' => 'مغامرات نورة', 'description' => 'مسلسل كارتون عائلي عن مغامرات فتاة صغيرة ورفاقها في عالم الخيال.', 'release_year' => 2023, 'duration' => '26 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['عائلي', 'مغامرات'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 26]], 'subsection' => $this->getSlug('مسلسلات كارتون')],
            ['title' => 'قصة الإسلام', 'description' => 'مسلسل إسلامي تاريخي يروي سيرة النبي محمد صلى الله عليه وسلم وصحابته.', 'release_year' => 2022, 'duration' => '30 حلقة', 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['تاريخي', 'سيرة ذاتية'], 'seasons' => [['season' => '1', 'title' => 'الموسم الأول', 'episodes' => 30]], 'subsection' => $this->getSlug('مسلسلات إسلامية')],
        ];

        // إدراج المسلسلات
        $arabicCompletedSection = $subSections->get($this->getSlug('مسلسلات عربية مكتملة'));
        foreach ($arabicCompletedSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $arabicCompletedSection?->id);
        }

        $arabicOngoingSection = $subSections->get($this->getSlug('مسلسلات عربية متجددة'));
        foreach ($arabicOngoingSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $arabicOngoingSection?->id);
        }

        $foreignCompletedSection = $subSections->get($this->getSlug('مسلسلات أجنبية مكتملة'));
        foreach ($foreignCompletedSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $foreignCompletedSection?->id);
        }

        $foreignOngoingSection = $subSections->get($this->getSlug('مسلسلات أجنبية متجددة'));
        foreach ($foreignOngoingSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $foreignOngoingSection?->id);
        }

        $turkishCompletedSection = $subSections->get($this->getSlug('مسلسلات تركية مكتملة'));
        foreach ($turkishCompletedSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $turkishCompletedSection?->id);
        }

        $turkishOngoingSection = $subSections->get($this->getSlug('مسلسلات تركية متجددة'));
        foreach ($turkishOngoingSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $turkishOngoingSection?->id);
        }

        $koreanSection = $subSections->get($this->getSlug('مسلسلات كورية'));
        foreach ($koreanSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $koreanSection?->id);
        }

        $animeCompletedSection = $subSections->get($this->getSlug('مسلسلات أنمي مكتملة'));
        foreach ($animeCompletedSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $animeCompletedSection?->id);
        }

        $animeOngoingSection = $subSections->get($this->getSlug('مسلسلات أنمي متجددة'));
        foreach ($animeOngoingSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $animeOngoingSection?->id);
        }

        $ramadanSection = $subSections->get($this->getSlug('مسلسلات وبرامج رمضان 2025'));
        foreach ($ramadanSeries as $i => $data) {
            $this->createSeries($data, $i, $user, $genres, $ramadanSection?->id);
        }

        // مسلسلات أخرى
        foreach ($otherSeries as $i => $data) {
            $subsectionSlug = $data['subsection'] ?? null;
            unset($data['subsection']);
            $subSectionId = $subsectionSlug ? $subSections->get($subsectionSlug)?->id : null;
            $this->createSeries($data, $i, $user, $genres, $subSectionId);
        }
    }

    private function createSeries(array $data, int $index, User $user, $genres, ?int $subSectionId): void
    {
        $genreNames = $data['genre_names'];
        $seasonsData = $data['seasons'];
        unset($data['genre_names'], $data['seasons']);
        // حقل المدة للمسلسلات في قاعدة البيانات يُستخدم كثواني لملف الفيديو،
        // بينما هنا "24 حلقة" إلخ. لذا نتركه فارغاً لتجنّب أخطاء التحويل.
        $data['duration'] = null;
        $data['file_size'] = null;
        unset($data['access_level_required']);

        $content = Content::query()->firstOrCreate(
            ['title' => $data['title']],
            array_merge($data, [
                'type' => ContentType::Series,
                'language' => ContentLanguage::Arabic,
                'poster_url' => $this->poster($index),
                'file_url' => self::default_video,
                'created_by' => $user->id,
                'sub_section_id' => $subSectionId,
                'views_count' => $this->randomViews(),
            ])
        );

        $this->attachGenres($content, $genres, $genreNames);

        foreach ($seasonsData as $s) {
            $season = $content->seasons()->firstOrCreate(
                ['season' => $s['season']],
                ['title' => $s['title'], 'description' => null, 'poster_url' => $content->poster_url]
            );
            for ($ep = 1; $ep <= $s['episodes']; $ep++) {
                Episode::query()->firstOrCreate(
                    ['season_id' => $season->id, 'episode_number' => $ep],
                    ['title' => "الحلقة {$ep}", 'description' => null, 'duration' => null, 'poster_url' => $content->poster_url, 'file_url' => self::default_video]
                );
            }
        }
    }

    private function seedBooks(User $user, $subSections, $genres): void
    {
        // كتب دينية
        $religiousBooks = [
            ['title' => 'رياض الصالحين', 'description' => 'كتاب ديني يحتوي على أحاديث نبوية مختارة.', 'release_year' => 2022, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['دراما']],
            ['title' => 'تفسير ابن كثير', 'description' => 'تفسير القرآن الكريم.', 'release_year' => 2021, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['وثائقي']],
            ['title' => 'صحيح البخاري', 'description' => 'أصح كتاب بعد كتاب الله.', 'release_year' => 2020, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['تاريخي']],
            ['title' => 'الرحيق المختوم', 'description' => 'سيرة النبي محمد صلى الله عليه وسلم.', 'release_year' => 2023, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['سيرة ذاتية', 'تاريخي']],
        ];

        // كتب أدبية
        $literatureBooks = [
            ['title' => 'حديقة الأسرار', 'description' => 'رواية تحكي قصة عائلة تكتشف أسراراً دفينة في حديقة المنزل القديم.', 'release_year' => 2022, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'ليالي القاهرة', 'description' => 'مجموعة قصصية ترسم وجوه المدينة في الليل من حب وخيبة وأمل.', 'release_year' => 2023, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['دراما', 'رومانسي']],
            ['title' => 'سفينة الأشباح', 'description' => 'رعب: رحلة بحرية على سفينة مهجورة لا يعرف ركابها من يقودها.', 'release_year' => 2023, 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['رعب', 'تشويق']],
            ['title' => 'الملكة المختفية', 'description' => 'تاريخي روائي عن ملكة حكمت ثم اختفت دون أثر في كتب التاريخ.', 'release_year' => 2022, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'دراما']],
            ['title' => 'ضحكة القدر', 'description' => 'كوميديا سوداء عن رجل يجد أن كل خطأ في حياته يقوده إلى الثراء.', 'release_year' => 2024, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['كوميدي']],
            ['title' => 'وراء الجدار', 'description' => 'تشويق: عائلة تسمع أصواتاً من الجدار الفاصل بينهم وبين الجيران.', 'release_year' => 2023, 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['تشويق', 'رعب']],
            ['title' => 'أول الحب', 'description' => 'رومانسي: قصة أول حب في الجامعة وأصداءه بعد عشرين عاماً.', 'release_year' => 2022, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
            ['title' => 'مملكة السحر', 'description' => 'فانتازيا: مملكة قديمة وسحر منسياً يعود للظهور.', 'release_year' => 2024, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['فانتازيا', 'مغامرات']],
            ['title' => 'ليالي الرياض', 'description' => 'دراما رومانسية في العاصمة السعودية وحياة الشباب.', 'release_year' => 2022, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['رومانسي', 'دراما']],
        ];

        // كتب علمية
        $scientificBooks = [
            ['title' => 'مدينة المستقبل', 'description' => 'رواية خيال علمي تصور حياة البشر في مدينة فضائية بعد مئة عام.', 'release_year' => 2024, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'مغامرات']],
            ['title' => 'المتحولون', 'description' => 'خيال علمي: بشر يتحولون إلى كائنات جديدة بعد وباء غامض.', 'release_year' => 2024, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'تشويق']],
            ['title' => 'الغزو الخفي', 'description' => 'خيال علمي: كائنات تتسلل إلى أجساد البشر دون أن يدروا.', 'release_year' => 2024, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'رعب']],
            ['title' => 'مغامرات في الفضاء', 'description' => 'خيال علمي للأطفال: رحلة إلى القمر والمريخ.', 'release_year' => 2024, 'access_level_required' => AccessLevel::Free, 'is_featured' => false, 'genre_names' => ['خيال علمي', 'عائلي']],
        ];

        // كتب تاريخية
        $historicalBooks = [
            ['title' => 'صيف بغداد', 'description' => 'دراما إنسانية في بغداد الثمانينيات عبر عيون طفل.', 'release_year' => 2021, 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['دراما', 'تاريخي']],
            ['title' => 'أسرار التاريخ', 'description' => 'وثائقي تاريخي: حقائق مخفية عن حقب من تاريخ المنطقة.', 'release_year' => 2021, 'access_level_required' => AccessLevel::Basic, 'is_featured' => false, 'genre_names' => ['وثائقي', 'تاريخي']],
            ['title' => 'الحرب الباردة', 'description' => 'حربي تشويق: جواسيس في زمن الحرب الباردة.', 'release_year' => 2021, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['حربي', 'تشويق']],
            ['title' => 'ملكة الصحراء', 'description' => 'تاريخي رومانسي: ملكة بدوية تدافع عن قبيلتها وحبها.', 'release_year' => 2023, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['تاريخي', 'رومانسي']],
            ['title' => 'جنود الظل', 'description' => 'حربي: مذكرات جندي من جبهة الحرب إلى العودة.', 'release_year' => 2021, 'access_level_required' => AccessLevel::Premium, 'is_featured' => false, 'genre_names' => ['حربي', 'دراما']],
        ];

        // إدراج الكتب
        $religiousSection = $subSections->get($this->getSlug('كتب دينية'));
        foreach ($religiousBooks as $i => $data) {
            $this->createBook($data, $i, $user, $genres, $religiousSection?->id);
        }

        $literatureSection = $subSections->get($this->getSlug('كتب أدبية'));
        foreach ($literatureBooks as $i => $data) {
            $this->createBook($data, $i, $user, $genres, $literatureSection?->id);
        }

        $scientificSection = $subSections->get($this->getSlug('كتب علمية'));
        foreach ($scientificBooks as $i => $data) {
            $this->createBook($data, $i, $user, $genres, $scientificSection?->id);
        }

        $historicalSection = $subSections->get($this->getSlug('كتب تاريخية'));
        foreach ($historicalBooks as $i => $data) {
            $this->createBook($data, $i, $user, $genres, $historicalSection?->id);
        }
    }

    private function createBook(array $data, int $index, User $user, $genres, ?int $subSectionId): void
    {
        $genreNames = $data['genre_names'];
        unset($data['genre_names']);
        unset($data['access_level_required']);

        $content = Content::query()->firstOrCreate(
            ['title' => $data['title']],
            array_merge($data, [
                'type' => ContentType::Book,
                'language' => ContentLanguage::Arabic,
                'duration' => null,
                'file_size' => null,
                'poster_url' => $this->poster($index),
                'file_url' => self::default_book,
                'created_by' => $user->id,
                'sub_section_id' => $subSectionId,
                'views_count' => $this->randomViews(),
            ])
        );
        $this->attachGenres($content, $genres, $genreNames);
    }

    private function seedRatings(User $user): void
    {
        $contentIds = Content::query()->inRandomOrder()->limit(20)->pluck('id');
        foreach ($contentIds as $contentId) {
            Rating::query()->firstOrCreate(
                ['user_id' => $user->id, 'content_id' => $contentId],
                ['rating' => random_int(1, 5)]
            );
        }
    }

    private function attachGenres(Content $content, $genres, array $names): void
    {
        $ids = $genres->whereIn('name', $names)->pluck('id');
        $content->genres()->syncWithoutDetaching($ids);
    }
}
