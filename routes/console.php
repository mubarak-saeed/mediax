<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| مزامنة Stripe المجدولة (نسخة احتياطية عند فشل الـ webhooks)
|--------------------------------------------------------------------------
| تُنفّذ كل 6 ساعات لمزامنة الخطط والمدفوعات والاشتراكات من Stripe.
| تتخطى التنفيذ إذا لم يكن STRIPE_SECRET مُعداً (مثلاً في بيئة التطوير).
*/
Schedule::command('stripe:sync')
    ->everySixHours()
    ->when(fn () => filled(config('cashier.secret')))
    ->withoutOverlapping(30)
    ->runInBackground();
