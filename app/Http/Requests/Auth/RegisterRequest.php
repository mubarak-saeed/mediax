<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'أدخل بريداً إلكترونياً صالحاً.',
            'email.unique' => 'هذا البريد مستخدم مسبقاً.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب أن تكون 10 أحرف على الأقل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.letters' => 'كلمة المرور يجب أن تحتوي على أحرف.',
            'password.mixed' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير.',
            'password.numbers' => 'كلمة المرور يجب أن تحتوي على أرقام.',
            'password.symbols' => 'كلمة المرور يجب أن تحتوي على رموز.',
            'password.uncompromised' => 'كلمة المرور مستخدمة سابقاً في تسريبات بيانات، اختر كلمة أقوى.',
        ];
    }
}
