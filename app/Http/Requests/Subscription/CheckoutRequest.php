<?php

namespace App\Http\Requests\Subscription;

use App\Models\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plan' => [
                'required',
                'integer',
                Rule::exists(Plan::class, 'id'),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $plan = Plan::find($value);
                    if (! $plan || ! $plan->stripe_price_id) {
                        $fail('الخطة المختارة غير متاحة للاشتراك.');
                    }
                },
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'plan.required' => 'يجب اختيار خطة اشتراك.',
            'plan.exists' => 'الخطة المختارة غير متاحة.',
        ];
    }
}
