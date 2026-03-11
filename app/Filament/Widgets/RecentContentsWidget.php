<?php

namespace App\Filament\Widgets;

use App\Enums\ContentType;
use App\Enums\UserRole;
use App\Filament\Resources\Sections\SectionResource;
use App\Models\Content;
use Filament\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseTableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentContentsWidget extends BaseTableWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Content::query()
                    ->with(['subsection.section', 'creator'])
                    ->latest()
            )
            ->heading('أحدث المحتوى')
            ->description('آخر المحتويات المضافة إلى المنصة')
            ->columns([
                ImageColumn::make('poster_url')
                    ->label('الغلاف')
                    ->circular(false),
                TextColumn::make('title')
                    ->label('العنوان')
                    ->limit(35)
                    ->sortable()
                    ->disabledClick(false)
                    ->searchable(),
                TextColumn::make('type')
                    ->label('النوع')
                    ->badge()
                    ->formatStateUsing(fn (ContentType $state) => $state->getLabel())
                    ->color(fn (ContentType $state): string => match ($state) {
                        ContentType::Movie => 'info',
                        ContentType::Series => 'success',
                        ContentType::Book => 'warning',
                        ContentType::MovieSerial => 'info',
                    }),
                TextColumn::make('subsection.name')
                    ->label('القسم الفرعي')
                    ->placeholder('—'),
                TextColumn::make('views_count')
                    ->label('المشاهدات')
                    ->numeric(),
                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->since()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('أنشئ بواسطة')
                    ->placeholder('—'),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->filters([
                SelectFilter::make('type')->label('النوع')->options(ContentType::class),
                SelectFilter::make('creator')
                    ->label('أنشئ بواسطة')
                    ->searchable()
                    ->preload()
                    ->relationship(
                        'creator',
                        'name',
                        fn (Builder $query) => $query->whereIn('role', [UserRole::Admin, UserRole::SuperAdmin]),
                    ),
                SelectFilter::make('subsection')->label('القسم الفرعي')->relationship('subsection', 'name')->searchable()->preload(),

            ])
            ->recordUrl(fn (Content $record): string => $record->getAdminEditUrl())
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(5)
            ->headerActions([
                Action::make('viewAll')
                    ->label('عرض الكل')
                    ->url(SectionResource::getUrl('index'))
                    ->icon('heroicon-o-squares-plus'),
            ]);
    }
}
