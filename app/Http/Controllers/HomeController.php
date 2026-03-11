<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * الصفحة الرئيسية للموقع (محتوى مميز + صفوف الأقسام الفرعية).
 */
class HomeController extends Controller
{
    /**
     * عرض الصفحة الرئيسية (يتم تحميل المحتوى عبر Livewire في home.blade.php).
     */
    public function index(): View
    {
        return view('home');
    }
}
