<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException) {
            return redirect()->route('login')
                ->with('error', 'انتهت صلاحية الجلسة أو تم إلغاء الطلب. يرجى تسجيل الدخول مرة أخرى.');
        }

        $name = $googleUser->getName() ?? $googleUser->getEmail() ?? 'user';
        $email = $googleUser->getEmail();

        $user = User::whereEmail($email)->whereGoogleId(null)->first();
        if ($user) {
            $user->update(
                [
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                ]
            );
        } else {
            $user = User::query()->updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $name,
                    'email' => $email,
                    'email_verified_at' => now(),
                ]
            );
        }

        Auth::login($user, true);

        return redirect()->intended(route('profile', absolute: false));
    }
}
