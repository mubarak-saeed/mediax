<?php

namespace App\Filament\Resources\Sections\Resources\SubSections\Schemas;

use App\Enums\ContentType;
use App\Models\SubSection;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class SubSectionForm
{
    public static function configure(Schema $schema): Schema
    {

        return $schema
            ->components([
                SchemaSection::make('معلومات القسم الفرعي')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->trim()
                            ->unique(table: SubSection::class, column: 'name', ignoreRecord: true)
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
                            ->unique(table: SubSection::class, column: 'slug', ignoreRecord: true)
                            ->helperText('يُولَّد تلقائياً من الاسم'),
                        Select::make('type')
                            ->required()
                            ->label('نوع المحتوى')
                            ->options(function ($livewire, $operation, $record) {

                                $options = [
                                    ContentType::Movie->value => ContentType::Movie->getLabel(),
                                    ContentType::Series->value => ContentType::Series->getLabel(),
                                    ContentType::Book->value => ContentType::Book->getLabel(),
                                    ContentType::MovieSerial->value => ContentType::MovieSerial->getLabel(),
                                ];
                                if ($operation == 'create') {
                                    $sectionType = $livewire->getOwnerRecord()->type;
                                }
                                if ($operation == 'edit') {
                                    $sectionType = $record->type;
                                }
                                foreach ($options as $key => $value) {
                                    if ($key != $sectionType) {
                                        unset($options[$key]);
                                    }
                                }

                                return $options;
                            })
                            ->default(fn ($livewire) => $livewire->getOwnerRecord()->type)
                            // ->disabledOn('edit')
                            ->placeholder('اختر النوع')
                            ->native(false),
                        TextInput::make('order')
                            ->label('ترتيب العرض')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        FileUpload::make('poster_url')
                            ->label('صورة القسم الفرعي')
                            ->image()
                            ->hidden()
                            ->imageEditor()
                            ->getUploadedFileNameForStorageUsing(fn (Get $get, $file) => $get('name').'.'.$file->getClientOriginalExtension())
                            ->directory(function ($livewire, Get $get, $record, $operation) {
                                if ($operation == 'create') {
                                    $section = $livewire->getOwnerRecord();
                                    $mainSectionName = $section->name;
                                    $subSectionName = $get('name');
                                } elseif ($operation == 'edit') {
                                    $section = $record->section;
                                    $mainSectionName = $section->name;
                                    $subSectionName = $record->name;
                                }

                                return 'البوسترات/'.$mainSectionName.'/'.$subSectionName;
                            }),
                    ])
                    ->columns(2),
            ]);
    }
}
