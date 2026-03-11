<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamEpisodeController extends Controller
{
    public function __invoke(Episode $episode): StreamedResponse|\Illuminate\Http\Response
    {
        if (! $episode->file_url) {
            abort(404);
        }

        $relativePath = str_replace('\\', '/', $episode->file_url);

        if (str_contains($relativePath, '..')) {
            abort(403);
        }

        $path = $this->resolveSafePath($relativePath);

        if (! $path || ! is_readable($path)) {
            abort(404);
        }

        $mime = $this->mimeForPath($path);

        return response()->stream(function () use ($path): void {
            $stream = fopen($path, 'r');
            if ($stream === false) {
                return;
            }
            while (! feof($stream)) {
                echo fread($stream, 8192);
                flush();
            }
            fclose($stream);
        }, 200, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.basename($path).'"',
        ]);
    }

    private function mimeForPath(string $path): string
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return match ($ext) {
            'mp4', 'm4v' => 'video/mp4',
            'webm' => 'video/webm',
            default => 'application/octet-stream',
        };
    }

    private function resolveSafePath(string $relativePath): ?string
    {
        $disk = Storage::disk('local');
        $storageRoot = realpath($disk->path(''));
        $resolvedPath = realpath($disk->path($relativePath));

        if (! $storageRoot || ! $resolvedPath) {
            return null;
        }

        $normalizedRoot = str_replace('\\', '/', $storageRoot);
        $normalizedPath = str_replace('\\', '/', $resolvedPath);

        if ($normalizedPath !== $normalizedRoot && ! str_starts_with($normalizedPath, $normalizedRoot.'/')) {
            return null;
        }

        return $resolvedPath;
    }
}
