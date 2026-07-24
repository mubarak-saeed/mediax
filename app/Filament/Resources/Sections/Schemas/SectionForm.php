<?php

namespace App\Filament\Resources\Sections\Schemas;

use App\Enums\ContentType;
use App\Enums\StatusEnum;
use App\Models\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaSection::make('معلومات القسم')
                ->collapsible()
                    ->schema([
                        TextInput::make('name')
                            ->label('اسم القسم')
                            ->required()
                            ->unique(table: Section::class, column: 'name', ignoreRecord: true)
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, callable $set) {
                                $slug = trim($state);
                                $slug = mb_strtolower($slug, 'UTF-8');
                                $slug = preg_replace('/[^\x{0600}-\x{06FF}a-z0-9]+/u', '-', $slug); // يسمح بالحروف العربية والأرقام
                                $slug = trim($slug, '-');

                                $set('slug', $slug);
                            }),
                        TextInput::make('slug')
                            ->label('الرابط (slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(table: Section::class, column: 'slug', ignoreRecord: true)
                            ->helperText('يُولَّد تلقائياً من الاسم إن تُرك فارغاً'),
                        FileUpload::make('poster_url')
                            ->label('صورة القسم')
                            ->image()
                            ->hidden()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(fn(Get $get, $file) => $get('name') . '.' . $file->getClientOriginalExtension())
                            ->directory(fn(Get $get) => 'البوسترات/' . $get('name')),
                        TextInput::make('order')
                            ->label('ترتيب العرض')
                            ->unique(table: Section::class, column: 'order', ignoreRecord: true)
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Select::make('type')
                            ->required()
                            ->label('نوع المحتوى في القسم')
                            ->options(function () {
                                $options = [
                                    ContentType::Movie->value => ContentType::Movie->getLabel(),
                                    ContentType::Series->value => ContentType::Series->getLabel(),
                                    ContentType::Book->value => ContentType::Book->getLabel(),
                                ];

                                return $options;
                            })
                            ->placeholder('اختر النوع')
                            ->native(false),

                        ToggleButtons::make('status')
                            ->label('الحالة')
                            ->inline()
                            ->options(StatusEnum::class)
                            ->default(StatusEnum::ACTIVE),
                    ])
                    ->columns(2),
            ]);
    }
}
