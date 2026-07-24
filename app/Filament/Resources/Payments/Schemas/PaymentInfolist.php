<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('stripe_invoice_id')
                    ->placeholder('-'),
                TextEntry::make('stripe_payment_intent_id')
                    ->placeholder('-'),
                TextEntry::make('user.name')
                    ->label('User'),
                TextEntry::make('plan.name')
                    ->label('Plan'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('currency')
                    ->placeholder('-'),
                TextEntry::make('payment_method'),
                TextEntry::make('payment_status')
                    ->badge(),
                TextEntry::make('transaction_id'),
                TextEntry::make('paid_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
