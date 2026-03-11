<?php

namespace App\Services;

use App\Models\Content;
use App\Models\Episode;
use Illuminate\Support\Facades\DB;

/**
 * تسجيل المشاهدات مرة واحدة فقط لكل مستخدم أو جلسة.
 */
class ContentViewService
{
    /**
     * تسجيل مشاهدة محتوى (فيلم) أو حلقة — تزيد العداد فقط إذا لم يُشاهد سابقاً.
     */
    public function recordView(Content $content, ?Episode $episode = null): bool
    {
        $user = auth('web')->user();
        $sessionId = $user ? null : session()->getId();

        $episodeId = $episode?->id;

        if ($user) {
            $query = DB::table('content_views')
                ->where('user_id', $user->id)
                ->where('content_id', $content->id);
            $exists = $episodeId
                ? $query->where('episode_id', $episodeId)->exists()
                : $query->whereNull('episode_id')->exists();
        } else {
            if (empty($sessionId)) {
                return false;
            }
            $query = DB::table('content_views')
                ->whereNull('user_id')
                ->where('session_id', $sessionId)
                ->where('content_id', $content->id);
            $exists = $episodeId
                ? $query->where('episode_id', $episodeId)->exists()
                : $query->whereNull('episode_id')->exists();
        }

        if ($exists) {
            return false;
        }

        DB::table('content_views')->insertOrIgnore([
            'user_id' => $user?->id,
            'session_id' => $sessionId,
            'content_id' => $content->id,
            'episode_id' => $episodeId,
            'viewed_at' => now(),
        ]);

        $content->increment('views_count');
        if ($episode) {
            $episode->increment('views_count');
        }

        return true;
    }
}
