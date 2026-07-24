<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Resources\Contents\Resources\Seasons\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class SeasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaSection::make('معلومات الموسم')
                ->collapsible()
                    ->schema([
                        TextInput::make('season')
                            ->label('الموسم')
                            ->required()
                            ->unique(modifyRuleUsing: function (Unique $rule, $livewire, $operation, $record) {
                                if ($operation == 'create') {
                                    $content = $livewire->getOwnerRecord();
                                }
                                if ($operation == 'edit') {
                                    $content = $record->content;
                                }

                                return $rule->where('content_id', $content->id);
                            })
                            ->numeric()
                            ->live(onBlur: true)
                            ->default(1)
                            ->minValue(1)
                            ->afterStateUpdated(function (Set $set, Get $get) {
                                $set('title', 'الموسم ' . $get('season'));
                            })
                            ->helperText('رقم الموسم مثال: 1، 2، 3...'),
                        TextInput::make('title')
                            ->label('العنوان')
                            ->maxLength(255)
                            ->placeholder('مثال: الموسم 1')
                            ->dehydrated()
                            ->disabled()
                            ->default(function (Get $get) {
                                return 'الموسم ' . $get('season');
                            }),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        FileUpload::make('poster_url')
                            ->label('صورة الغلاف (البوستر)')
                            ->image()
                            ->imageEditor()
                            ->imageAspectRatio('2:3')
                            ->automaticallyCropImagesToAspectRatio()
                            ->automaticallyOpenImageEditorForAspectRatio()
                            ->getUploadedFileNameForStorageUsing(fn(Get $get, $file) => $get('title') . '.' . $file->getClientOriginalExtension())
                            ->directory(function ($livewire, Get $get, $record, $operation) {
                                if ($operation == 'create') {
                                    $content = $livewire->getOwnerRecord();
                                    $mainSectionName = $content->subsection->section->name ?? 'Unknown';
                                    $subSectionName = $content->subsection->name ?? 'Unknown';
                                    $contentTitle = $content->title ?? 'Unknown';
                                    $seasonTitle = $get('title');
                                } elseif ($operation == 'edit') {
                                    $content = $record->content;
                                    $mainSectionName = $content->subsection->section->name ?? 'Unknown';
                                    $subSectionName = $content->subsection->name ?? 'Unknown';
                                    $contentTitle = $content->title ?? 'Unknown';
                                    $seasonTitle = $record->title;
                                }

                                return 'البوسترات/' . $mainSectionName . '/' . $subSectionName . '/' . $contentTitle . '/' . $seasonTitle;
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
