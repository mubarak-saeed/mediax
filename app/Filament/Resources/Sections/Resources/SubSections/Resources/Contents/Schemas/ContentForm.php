<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Schemas;

use App\Enums\AccessLevel;
use App\Enums\ContentLanguage;
use App\Enums\ContentType;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ContentForm
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        TextInput::make('title')
                            ->label('العنوان')
                            ->required()
                            ->placeholder('عنوان المحتوى')
                            ->columnSpan(2),
                        Select::make('type')
                            ->label('النوع')
                            ->options(function ($livewire, $operation, $record) {
                                $options = [
                                    ContentType::Movie->value => ContentType::Movie->getLabel(),
                                    ContentType::Series->value => ContentType::Series->getLabel(),
                                    ContentType::Book->value => ContentType::Book->getLabel(),
                                    ContentType::MovieSerial->value => ContentType::MovieSerial->getLabel(),
                                ];
                                if ($operation == 'create') {
                                    $type = $livewire->getOwnerRecord()->type;
                                }
                                if ($operation == 'edit') {
                                    $type = $record->subsection->type;
                                }
                                foreach ($options as $key => $value) {
                                    if ($key != $type && $key) {
                                        unset($options[$key]);
                                    }
                                }

                                return $options;
                            })
                            ->default(function ($livewire) {
                                return $livewire->getOwnerRecord()->type;
                            })

                            ->placeholder('اختر النوع')
                            ->required()
                            ->live(debounce: 250)
                            ->native(false),
                        Select::make('genres')
                            ->label('الأنواع الفنية')
                            ->helperText('يمكنك اختيار أكثر من نوع فني للمحتوى')
                            ->relationship('genres', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->native(false),
                        Select::make('language')
                            ->label('اللغة')
                            ->options(ContentLanguage::class)
                            ->searchable()
                            ->default(ContentLanguage::Arabic)
                            ->native(false),
                        TextInput::make('release_year')
                            ->label('سنة الإصدار')
                            ->numeric()
                            ->length(4)
                            ->minValue(1900)
                            ->maxValue(now()->year),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->hint('اختياري')
                            ->helperText(function ($livewire, $operation, $record) {
                                if ($operation == 'create') {
                                    $type = $livewire->getOwnerRecord()->type;
                                }
                                if ($operation == 'edit') {
                                    $type = $record->subsection->type;
                                }

                                return match ($type) {
                                    ContentType::Movie->value => ' وصفًا مختصرًا للفيلم.',
                                    ContentType::Series->value => ' وصفًا مختصرًا للمسلسل.',
                                    ContentType::Book->value => 'وصفًا مختصرًا للكتاب.',
                                    ContentType::MovieSerial->value => 'وصفًا مختصرًا للفلم.',
                                    default => 'وصفًا مختصرًا للمحتوى.',
                                };
                            })
                            ->maxLength(5000)
                            ->columnSpanFull(),
                        Select::make('access_level_required')
                            ->label('مستوى الوصول المطلوب')
                            ->options(fn () => [
                                AccessLevel::Free->value => AccessLevel::Free->getLabel(),
                                AccessLevel::Basic->value => AccessLevel::Basic->getLabel(),
                            ])
                            ->required()
                            ->native(false)
                            ->default(AccessLevel::Basic),
                        Toggle::make('is_featured')
                            ->label('محتوى مميز')
                            ->default(false),

                    ])
                    ->description('المعلومات الأساسية للمحتوى')
                    ->icon(Heroicon::InformationCircle)
                    ->iconColor('primary')
                    ->collapsible()
                    ->columns(2),
                Section::make('الصور والملفات')
                    ->collapsible()
                    ->icon(Heroicon::Photo)
                    ->iconColor('primary')
                    ->description('صورة الغلاف والملف المرتبط بالمحتوى')
                    ->schema([
                        FileUpload::make('poster_url')
                            ->label('صورة الغلاف (البوستر)')
                            ->image()
                            ->required()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(fn (Get $get, $file) => $get('title').'.'.$file->getClientOriginalExtension())
                            ->directory(function ($livewire, Get $get, $record, $operation) {
                                if ($operation == 'create') {
                                    $subSection = $livewire->getOwnerRecord();
                                    $mainSectionName = $subSection->section->name;
                                    $subSectionName = $subSection->name;
                                    $contentTitle = $get('title');
                                } elseif ($operation == 'edit') {
                                    $subSection = $record->subsection;
                                    $mainSectionName = $subSection->section->name;
                                    $subSectionName = $subSection->name;
                                    $contentTitle = $record->title;
                                }

                                return 'البوسترات/'.$mainSectionName.'/'.$subSectionName.'/'.$contentTitle;
                            })
                            ->columnSpanFull(),
                        FileUpload::make('file_url')
                            ->label('ملف المحتوى (فيديو/PDF)')
                            ->disk('local')
                            ->getUploadedFileNameForStorageUsing(fn (Get $get, $file) => $get('title').'.'.$file->getClientOriginalExtension())
                            ->directory(function ($livewire, Get $get, $record, $operation) {
                                if ($operation == 'create') {
                                    $subSection = $livewire->getOwnerRecord();
                                    $mainSectionName = $subSection->section->name;
                                    $subSectionName = $subSection->name;
                                    $contentTitle = $get('title');
                                } elseif ($operation == 'edit') {
                                    $subSection = $record->subsection;
                                    $mainSectionName = $subSection->section->name;
                                    $subSectionName = $subSection->name;
                                    $contentTitle = $record->title;
                                }

                                return 'الملفات/'.$mainSectionName.'/'.$subSectionName.'/'.$contentTitle;
                            })
                            ->hidden(fn (Get $get) => $get('type') == ContentType::Series->value || $get('type') == ContentType::MovieSerial->value)
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/webm',
                                'video/x-m4v',
                                'application/pdf',
                                'application/epub+zip',
                            ])
                            ->columnSpanFull(),
                    ]),

            ]);
    }
}
