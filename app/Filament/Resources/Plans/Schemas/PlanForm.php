<?php

namespace App\Filament\Resources\Plans\Schemas;

use App\Enums\AccessLevel;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as SchemaSection;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                SchemaSection::make('معلومات الخطة')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('مثال: الخطة الأساسية'),
                        TextInput::make('price')
                            ->label('السعر')
                            ->required()
                            ->numeric()
                            ->default(0.0)
                            ->minValue(0)
                            ->prefix('$'),
                        Textarea::make('description')
                            ->label('الوصف')
                            ->rows(4)
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Select::make('access_level')
                            ->label('مستوى الوصول')
                            ->options(AccessLevel::class)
                            ->default(AccessLevel::Free)
                            ->required()
                            ->native(false),
                        TextInput::make('sort_order')
                            ->label('ترتيب العرض')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(2),
                SchemaSection::make('إعدادات Stripe')
                    ->description('معرفات Stripe للمزامنة مع بوابة الدفع')
                    ->schema([
                        TextInput::make('stripe_product_id')
                            ->label('Stripe Product ID'),
                        TextInput::make('stripe_price_id')
                            ->label('Stripe Price ID'),
                        Select::make('currency')
                            ->label('العملة')
                            ->options(\App\Enums\Currency::class)
                            ->default(\App\Enums\Currency::Usd)
                            ->native(false),
                        Select::make('interval')
                            ->label('الفترة')
                            ->options(\App\Enums\PlanInterval::class)
                            ->placeholder('اختر الفترة')
                            ->native(false),
                        TextInput::make('interval_count')
                            ->label('عدد الفترات')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                    ])
                    ->columns(2)
                    ->collapsed(),
                SchemaSection::make('ميزات الخطة')
                    ->schema([
                        Repeater::make('features')
                            ->relationship()
                            ->schema([
                                TextInput::make('title')
                                    ->label('الميزة')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('sort_order')
                                    ->label('الترتيب')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0),
                            ])
                            ->defaultItems(0)
                            ->columnSpanFull()
                            ->reorderable()
                            ->collapsible(),
                    ]),
            ]);
    }
}
