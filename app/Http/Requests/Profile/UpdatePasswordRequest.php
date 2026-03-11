<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
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
        $user = $this->user();
        $hasPassword = ! empty($user->password);

        $currentPasswordRules = [
            Rule::requiredIf($hasPassword),
            'nullable',
            'string',
        ];
        if ($hasPassword) {
            $currentPasswordRules[] = 'current_password';
        }

        return [
            'current_password' => $currentPasswordRules,
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(10)->letters()->mixedCase()->numbers()->symbols()->uncompromised(),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => 'كلمة المرور الحالية مطلوبة.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.required' => 'كلمة المرور الجديدة مطلوبة.',
            'password.min' => 'كلمة المرور الجديدة يجب أن تكون 10 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.letters' => 'كلمة المرور الجديدة يجب أن تحتوي على أحرف.',
            'password.mixed' => 'كلمة المرور الجديدة يجب أن تحتوي على حرف كبير وحرف صغير.',
            'password.numbers' => 'كلمة المرور الجديدة يجب أن تحتوي على أرقام.',
            'password.symbols' => 'كلمة المرور الجديدة يجب أن تحتوي على رموز.',
            'password.uncompromised' => 'كلمة المرور الجديدة مستخدمة سابقاً في تسريبات بيانات، اختر كلمة أقوى.',
        ];
    }
}
