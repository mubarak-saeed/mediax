<?php

namespace App\Http\Controllers;

use App\Enums\ContentType;
use App\Models\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReadController extends Controller
{
    public function show(Content $content): View|RedirectResponse
    {
        if ($content->type !== ContentType::Book) {
            return redirect()->route('content.show', $content);
        }

        if (! $content->file_url) {
            return redirect()->route('content.show', $content)
                ->with('status', 'الملف غير متوفر حالياً.');
        }

        $streamUrl = str_starts_with($content->file_url, 'http')
            ? $content->file_url
            : \Illuminate\Support\Facades\URL::temporarySignedRoute(
                'stream',
                now()->addHour(),
                ['content' => $content->id]
            );

        return view('read.show', [
            'content' => $content,
            'streamUrl' => $streamUrl,
        ]);
    }
}
