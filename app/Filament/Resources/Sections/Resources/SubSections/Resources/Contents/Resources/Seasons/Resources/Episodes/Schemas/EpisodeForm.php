<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Resources\Episodes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class EpisodeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaSection::make('معلومات الحلقة')
                    ->schema([
                        TextInput::make('episode_number')
                            ->label('رقم الحلقة')
                            ->required()
                            ->numeric()
                            ->unique(modifyRuleUsing: function (Unique $rule, $livewire, $operation, $record) {
                                if ($operation == 'create') {
                                    $season = $livewire->getOwnerRecord();
                                }
                                if ($operation == 'edit') {
                                    $season = $record->season;
                                }

                                return $rule->where('season_id', $season->id);
                            })
                            ->default(1)
                            ->live(onBlur: true)
                            ->minValue(1)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $set('title', 'الحلقة ' . $get('episode_number'));
                            })
                            ->helperText('مثال: 01، 02، 03...'),
                        TextInput::make('title')
                            ->label('العنوان')
                            ->dehydrated()
                            ->disabled()
                            ->default(function (Get $get) {
                                return 'الحلقة ' . $get('episode_number');
                            })
                            ->placeholder('عنوان الحلقة'),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                SchemaSection::make('الملفات')
                    ->schema([
                        FileUpload::make('poster_url')
                            ->label('صورة الغلاف')
                            ->label('صورة الغلاف (البوستر)')
                            ->image()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(fn(Get $get, $file) => $get('title') . '.' . $file->getClientOriginalExtension())
                            ->directory(function ($livewire, $record, $operation) {
                                if ($operation == 'create') {
                                    $season = $livewire->getOwnerRecord();
                                    $content = $season->content;
                                    $mainSectionName = $content->subsection->section->name ?? 'Unknown';
                                    $subSectionName = $content->subsection->name ?? 'Unknown';
                                    $contentTitle = $content->title ?? 'Unknown';
                                    $seasonTitle = $season->title;
                                } elseif ($operation == 'edit') {
                                    $season = $record->season;
                                    $content = $season->content;
                                    $mainSectionName = $content->subsection->section->name ?? 'Unknown';
                                    $subSectionName = $content->subsection->name ?? 'Unknown';
                                    $contentTitle = $content->title ?? 'Unknown';
                                    $seasonTitle = $season->title;
                                }

                                return 'البوسترات/' . $mainSectionName . '/' . $subSectionName . '/' . $contentTitle . '/' . $seasonTitle;
                            })
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        FileUpload::make('file_url')
                            ->label('ملف الحلقة (فيديو)')
                            ->disk('local')
                            ->getUploadedFileNameForStorageUsing(fn(Get $get, $file) => $get('title') . '.' . $file->getClientOriginalExtension())
                            ->directory(function ($livewire, $record, $operation) {
                                if ($operation == 'create') {
                                    $season = $livewire->getOwnerRecord();
                                    $content = $season->content;
                                    $mainSectionName = $content->subsection->section->name ?? 'Unknown';
                                    $subSectionName = $content->subsection->name ?? 'Unknown';
                                    $contentTitle = $content->title ?? 'Unknown';
                                    $seasonTitle = $season->title;
                                } elseif ($operation == 'edit') {
                                    $season = $record->season;
                                    $content = $season->content;
                                    $mainSectionName = $content->subsection->section->name ?? 'Unknown';
                                    $subSectionName = $content->subsection->name ?? 'Unknown';
                                    $contentTitle = $content->title ?? 'Unknown';
                                    $seasonTitle = $season->title;
                                }

                                return 'الملفات/' . $mainSectionName . '/' . $subSectionName . '/' . $contentTitle . '/' . $seasonTitle;
                            })
                            ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/x-m4v', 'video/x-ms-wmv'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
