<?php

namespace App\Models;

use App\Enums\AccessLevel;
use App\Enums\UserRole;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

/**
 * مستخدم المنصة: يدعم الأدوار (admin/user) والاشتراك عبر Cashier.
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \App\Enums\UserRole $role
 * @property string|null $google_id
 */
class User extends Authenticatable implements FilamentUser
{
    use Billable;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * مدفوعات المستخدم.
     *
     * @return HasMany<Payment, $this>
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * مفضلات المستخدم.
     *
     * @return HasMany<Favorite, $this>
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * تقييمات المستخدم.
     *
     * @return HasMany<Rating, $this>
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /** هل يمكن للمستخدم الدخول إلى لوحة Filament؟ (أدمن أو مدير نظام) */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    /** هل المستخدم أدمن؟ */
    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::Admin, UserRole::SuperAdmin], true);
    }

    /** هل المستخدم مدير النظام (Super admin)؟ */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    /**
     * هل للمستخدم اشتراك فعّال (أي خطة)؟ يُستخدم للوصول للمحتوى وعرض الأزرار.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions->contains(fn (Subscription $s): bool => $s->valid());
    }

    /**
     * أول اشتراك فعّال (لأي نوع خطة). يُستخدم لعرض الخطة الحالية في الملف الشخصي وخطط الاشتراك.
     */
    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions->first(fn (Subscription $s): bool => $s->valid());
    }

    /**
     * الخطة الحالية للمستخدم (حسب اشتراكه النشط). تُستخدم لعرض اسم الخطة والتحقق من صلاحية التحميل.
     */
    public function currentPlan(): ?Plan
    {
        $sub = $this->currentSubscription();
        if (! $sub) {
            return null;
        }
        $item = $sub->items->first();
        $stripePriceId = $item?->stripe_price;
        if (! $stripePriceId) {
            return null;
        }

        return Plan::query()->where('stripe_price_id', $stripePriceId)->first();
    }

    /**
     * هل يمكن للمستخدم تحميل الملفات؟ الأدمن دائماً؛ المشتركون بخطة أعلى من الأساسية فقط (برو/بريميوم).
     */
    public function canDownload(): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $plan = $this->currentPlan();

        return $plan && $plan->access_level !== AccessLevel::Basic;
    }

    /**
     * هل يمكن للمستخدم الوصول لمحتوى معيّن؟
     * - الأدمن: دائماً
     * - المحتوى المجاني: لجميع المستخدمين المسجّلين
     * - المحتوى المدفوع: يتطلب اشتراكاً بخطة مستوى وصولها >= مستوى المحتوى المطلوب
     */
    public function canAccessContent(Content $content): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        if ($content->access_level_required === AccessLevel::Free) {
            return true;
        }

        $plan = $this->currentPlan();
        if (! $plan) {
            return false;
        }

        return $plan->access_level->allowsAccessTo($content->access_level_required);
    }
}
