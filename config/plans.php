<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans (Stripe Price IDs)
    |--------------------------------------------------------------------------
    |
    | Define plans used for checkout. Each plan must have a Stripe Price ID
    | (price_xxx). Create products and prices in Stripe Dashboard, then add
    | the price IDs here. Name and description are for display only.
    |
    */

    'plans' => [
        'monthly' => [
            'name' => 'شهري',
            'name_en' => 'Monthly',
            'description' => 'اشتراك شهري — مشاهدة وقراءة كل المحتوى.',
            'price_id' => env('STRIPE_PRICE_MONTHLY'),
            'interval' => 'month',
            'amount' => 9.99,
            'currency' => 'sar',
        ],
        'yearly' => [
            'name' => 'سنوي',
            'name_en' => 'Yearly',
            'description' => 'اشتراك سنوي — وفر مع دفع واحد سنوياً.',
            'price_id' => env('STRIPE_PRICE_YEARLY'),
            'interval' => 'year',
            'amount' => 89.99,
            'currency' => 'sar',
        ],
    ],

];
