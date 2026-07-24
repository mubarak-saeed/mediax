<?php

namespace App\Providers;

use App\Listeners\SyncPaymentFromStripeInvoice;
use App\Models\Content;
use App\Models\Episode;
use App\Models\Season;
use App\Models\Section;
use App\Models\User;
use App\Observers\ContentObserver;
use App\Observers\EpisodeObserver;
use App\Observers\SeasonObserver;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\PaginationMode;
use Filament\Tables\Table;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Events\WebhookReceived;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        Paginator::useTailwind();

        View::composer('layouts.app', function ($view): void {
            $view->with('navSections', Section::query()->whereStatus('active')->orderBy('order')->limit(3)->get());
        });

        Cashier::useCustomerModel(User::class);
        Event::listen(WebhookReceived::class, SyncPaymentFromStripeInvoice::class);

        Content::observe(ContentObserver::class);
        Episode::observe(EpisodeObserver::class);
        Season::observe(SeasonObserver::class);

        $this->configureFilamentActions();
        $this->configureFilamentForms();
        $this->configureFilamentSchemas();
        $this->configureFilamentTables();
        $this->configureFilamentInfolists();
    }

    /**
     * إعداد حدود الطلبات (Rate Limiting) لحماية التطبيق.
     */
    private function configureRateLimiting(): void
    {

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('email') . $request->ip());
        });

        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()?->id ?: $request->ip());
        });
    }

    /**
     * إعدادات عامة لأزرار وإجراءات Filament.
     */
    private function configureFilamentActions(): void
    {
        CreateAction::configureUsing(function (CreateAction $action): void {
            $action
                ->modal()
                ->modalWidth(Width::TwoExtraLarge);
        });
    }

    /**
     * إعدادات عامة لمكونات النماذج (لوحة التحكم عربية).
     */
    private function configureFilamentForms(): void
    {

        Select::configureUsing(function (Select $select): void {
            $select->native(false);
        });

        Field::configureUsing(function (Field $field) {
            $field->validationMessages([
                'required' => 'هذا الحقل مطلوب!',
                'unique' => ':attribute موجود بالفعل !',
                'numeric' => ':attribute يجب أن يكون رقم !',
                'digits' => ':attribute يجب أن يكون :digits !',
                'min' => ':attribute يجب أن يكون أكبر من :min !',
                'max' => ':attribute يجب أن يكون أصغر من :max !',
            ]);
        });

        DateTimePicker::configureUsing(function (DateTimePicker $picker): void {
            $picker->displayFormat('Y-m-d h:i A')
                ->closeOnDateSelection()
                ->seconds(false);
        });

        DatePicker::configureUsing(function (DatePicker $picker): void {
            $picker->displayFormat('Y-m-d');
        });

        FileUpload::configureUsing(function (FileUpload $upload): void {
            $upload->maxSize(4 * 1024 * 1024 * 1024) // 4GB
                
                ;
        });
    }

    /**
     * إعدادات عامة لمكونات الـ Schema (أقسام النماذج).
     */
    private function configureFilamentSchemas(): void
    {
        SchemaSection::configureUsing(function (SchemaSection $section): void {
            $section->columns(['default' => 1, 'sm' => 2])
                ->columnSpan(fn($operation) => $operation === 'create' ? 'full' : null);
        });
    }

    /**
     * إعدادات عامة للجداول والأعمدة.
     */
    private function configureFilamentTables(): void
    {
        Table::configureUsing(function (Table $table): void {
            $table
                ->deferColumnManager(false)
                ->deferFilters(false)
                ->paginationMode(PaginationMode::Default)
                ->searchDebounce('750ms')
                ->scrollToTopOnPageChange()
                ->defaultSort('created_at', 'desc')
                ->reorderableColumns()
                ->persistFiltersInSession()
                ->persistColumnsInSession()
                ->filtersLayout(FiltersLayout::Dropdown)
                ->paginationPageOptions([10, 25, 50, 100])
                ->defaultPaginationPageOption(25)
                ->defaultDateTimeDisplayFormat('Y-m-d h:i A')
                ->defaultTimeDisplayFormat('H:i')
                ->defaultNumberLocale('en')
                ->defaultDateDisplayFormat('Y-m-d')
                ->emptyStateHeading('لا توجد سجلات')
                ->emptyStateDescription('ابدأ بإضافة سجل جديد');
        });

        TextColumn::configureUsing(function (TextColumn $column): void {
            $column
                ->toggleable()
                ->disabledClick()
                ;
        });

        ImageColumn::configureUsing(function (ImageColumn $column): void {
            $column->disk('public')
            
                ->placeholder('—');
        });
    }

    /**
     * إعدادات عامة لعناصر Infolist (صفحات العرض).
     */
    private function configureFilamentInfolists(): void
    {
        TextEntry::configureUsing(function (TextEntry $entry): void {
            $entry->placeholder('—');
        });
    }
}
