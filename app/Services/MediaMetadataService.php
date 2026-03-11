<?php

namespace App\Services;

class MediaMetadataService
{
    /**
     * تنسيق مدة بالثواني إلى نص عربي منسق مثل:
     * - "45 ثانية"
     * - "1 دقيقة 30 ثانية"
     * - "1 ساعة 5 دقائق 10 ثوانٍ"
     */
    public function formatDurationToHoursMinutes(?int $seconds): string
    {
        if ($seconds === null || $seconds <= 0) {
            return '';
        }

        $hours = intdiv($seconds, 3600);
        $remainingSeconds = $seconds % 3600;
        $minutes = intdiv($remainingSeconds, 60);
        $remainingSeconds = $remainingSeconds % 60;

        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours.' ساعة';
        }

        if ($minutes > 0) {
            $parts[] = $minutes.' دقيقة';
        }

        if ($remainingSeconds > 0 || empty($parts)) {
            $parts[] = $remainingSeconds.' ثانية';
        }

        return implode(' ', $parts);
    }

    /**
     * قراءة مدة ملف فيديو (بالثواني) باستخدام getID3.
     *
     * @return int|null عدد الثواني أو null إن تعذّر القراءة
     */
    public function getVideoDurationMinutes(string $absolutePath): ?int
    {
        if (! is_file($absolutePath)) {
            return null;
        }

        try {
            // استخدام حزمة getID3 من Composer لقراءة بيانات الوسائط
            $getID3 = new \getID3;
            $info = $getID3->analyze($absolutePath);

            if (! isset($info['playtime_seconds'])) {
                return null;
            }

            $seconds = (float) $info['playtime_seconds'];

            if ($seconds <= 0) {
                return null;
            }

            return (int) round($seconds);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * تنسيق حجم ملف (بالبايت) إلى نص مقروء مثل "2.3 MB".
     */
    public function formatFileSize(?int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return sprintf('%.1f %s', $bytes, $units[$i]);
    }
}
