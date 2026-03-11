<?php

namespace App\Http\Middleware;

use App\Models\Content;
use App\Models\Episode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * يضمن أن المستخدم مسجّل دخول وقادر على الوصول للمحتوى (مجاني للجميع، أو مدفوع حسب مستوى الاشتراك).
 */
class SubscribeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        $content = $this->resolveContentFromRoute($request);
        if (! $content) {
            return redirect()->route('profile')
                ->with('status', 'يجب الاشتراك لمشاهدة أو قراءة المحتوى.');
        }

        if ($user->canAccessContent($content)) {
            return $next($request);
        }

        $requiredLevel = $content->access_level_required->getLabel();

        return redirect()->route('profile')
            ->with('status', "هذا المحتوى يتطلب اشتراكاً بخطة {$requiredLevel} أو أعلى. يمكنك ترقية خطتك من الأسفل.");
    }

    private function resolveContentFromRoute(Request $request): ?Content
    {
        $content = $request->route('content');
        if ($content instanceof Content) {
            return $content;
        }

        $episode = $request->route('episode');
        if ($episode instanceof Episode) {
            return $episode->season?->content;
        }

        if (is_numeric($content)) {
            return Content::find($content);
        }

        return null;
    }
}
