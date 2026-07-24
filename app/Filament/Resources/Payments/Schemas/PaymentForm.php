<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('stripe_invoice_id')
                    ->default(null),
                TextInput::make('stripe_payment_intent_id')
                    ->default(null),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Select::make('plan_id')
                    ->relationship('plan', 'name')
                    ->required(),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('currency')
                    ->default(null),
                TextInput::make('payment_method')
                    ->required(),
                Select::make('payment_status')
                    ->options(PaymentStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('transaction_id')
                    ->required(),
                DateTimePicker::make('paid_at')
                    ->required(),
            ]);
    }
}
