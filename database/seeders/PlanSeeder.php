<?php

namespace Database\Seeders;

use App\Enums\AccessLevel;
use App\Models\Plan;
use App\Services\PlanStripeSyncService;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * الخطط الرئيسية الثلاث: Basic، Pro، Premium.
     * بعد التشغيل أضف Stripe Price ID من لوحة الإدارة لكل خطة ثم احفظ للمزامنة.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'access_level' => AccessLevel::Basic,
                'sort_order' => 1,
                'stripe_price_id' => env('STRIPE_PRICE_BASIC'),
                'features' => [
                    'مشاهدة الأفلام والمسلسلات',
                    'جودة قياسية',
                    'شاشة واحدة',
                ],
            ],
            [
                'name' => 'Pro',
                'access_level' => AccessLevel::Pro,
                'sort_order' => 2,
                'stripe_price_id' => env('STRIPE_PRICE_PRO'),
                'features' => [
                    'كل ميزات Basic',
                    'جودة HD',
                    'شاشتان متزامنتان',
                    'تحميل للمشاهدة دون اتصال',
                ],
            ],
            [
                'name' => 'Premium',
                'access_level' => AccessLevel::Premium,
                'sort_order' => 3,
                'stripe_price_id' => env('STRIPE_PRICE_PREMIUM'),
                'features' => [
                    'كل ميزات Pro',
                    'جودة 4K UHD',
                    'عدد غير محدود من الشاشات',
                    'أولوية في الدعم الفني',
                    'محتوى حصري',
                ],
            ],
        ];

        foreach ($plans as $data) {
            $features = $data['features'];
            unset($data['features']);

            $plan = Plan::query()->firstOrNew(
                ['access_level' => $data['access_level']->value],
                [
                    'name' => $data['name'],
                    'price' => 0,
                    'description' => null,
                ]
            );

            if (blank($plan->name)) {
                $plan->name = $data['name'];
            }

            $plan->fill([
                'access_level' => $data['access_level'],
                'sort_order' => $data['sort_order'],
                'stripe_price_id' => $data['stripe_price_id'],
            ]);
            $plan->save();

            $plan->features()->delete();
            foreach ($features as $i => $title) {
                $plan->features()->create([
                    'title' => $title,
                    'sort_order' => $i,
                ]);
            }

            if (! empty($plan->stripe_price_id)) {
                app(PlanStripeSyncService::class)->syncOrFail($plan);
            }
        }
    }
}
