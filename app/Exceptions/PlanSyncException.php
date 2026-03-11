<?php

namespace App\Exceptions;

use Exception;

/**
 * استثناء عند فشل مزامنة الخطة من Stripe (اتصال، تفرد الاسم، إلخ).
 */
class PlanSyncException extends Exception
{
    public static function uniqueName(): self
    {
        return new self('اسم الخطة مُستخدم في خطة أخرى. غيّر الاسم في Stripe أو في الخطة الأخرى.');
    }

    public static function stripeError(string $message): self
    {
        return new self('فشل الاتصال بـ Stripe: '.$message);
    }
}
