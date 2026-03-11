<?php

namespace App\Console\Commands;

use App\Actions\Subscription\SyncUserSubscriptionFromStripe;
use App\Models\User;
use App\Services\PaymentStripeSyncService;
use Illuminate\Console\Command;
use Laravel\Cashier\Cashier;

class StripeSyncCommand extends Command
{
    protected $signature = 'stripe:sync
                            {--plans : Sync plans from Stripe}
                            {--payments : Sync payments from Stripe}
                            {--subscriptions : Sync user subscriptions from Stripe}
                            {--all : Sync everything (default)}
                            {--limit=100 : Max invoices per customer (for payments)}
                            {--details : Show details of each new payment}';

    protected $description = 'Sync data from Stripe (plans, payments, subscriptions)';

    public function handle(PaymentStripeSyncService $paymentSync): int
    {
        $plans = $this->option('plans') || $this->option('all');
        $payments = $this->option('payments') || $this->option('all');
        $subscriptions = $this->option('subscriptions') || $this->option('all');

        if (! $plans && ! $payments && ! $subscriptions) {
            $plans = $payments = $subscriptions = true;
        }

        if ($plans) {
            $this->info('Syncing plans...');
            $this->call('plans:sync-stripe');
            $this->newLine();
        }

        if ($payments) {
            $this->syncPayments($paymentSync);
            $this->newLine();
        }

        if ($subscriptions) {
            $this->info('Syncing subscriptions...');
            $users = User::query()->whereNotNull('stripe_id')->get();
            $synced = 0;
            foreach ($users as $user) {
                if (app(SyncUserSubscriptionFromStripe::class)->sync($user)) {
                    $synced++;
                }
            }
            $this->info("Subscriptions synced for {$synced} users.");
        }

        $this->info('Stripe sync completed.');

        return self::SUCCESS;
    }

    private function syncPayments(PaymentStripeSyncService $syncService): void
    {
        $this->info('Syncing payments...');

        $limit = (int) $this->option('limit');
        $verbose = (bool) $this->option('details');
        $users = User::query()->whereNotNull('stripe_id')->get();

        if ($users->isEmpty()) {
            $this->warn('No users connected to Stripe (stripe_id).');

            return;
        }

        $synced = 0;
        $totalInvoices = 0;
        $skipped = 0;

        foreach ($users as $user) {
            try {
                $invoices = Cashier::stripe()->invoices->all([
                    'customer' => $user->stripe_id,
                    'status' => 'paid',
                    'limit' => $limit,
                    'expand' => ['data.lines.data.price', 'data.lines.data.pricing'],
                ]);

                foreach ($invoices->data as $invoice) {
                    $totalInvoices++;
                    $arr = method_exists($invoice, 'toArray') ? $invoice->toArray() : (array) $invoice;
                    $result = $syncService->syncFromInvoiceArray($arr);
                    if ($result !== null) {
                        $synced++;
                        if ($verbose) {
                            $this->line('  + Paid: '.$result->id.' — '.$result->amount.' '.strtoupper($result->currency ?? ''));
                        }
                    } else {
                        $skipped++;
                    }
                }
            } catch (\Throwable $e) {
                $this->error('Error for user '.$user->id.' ('.$user->email.'): '.$e->getMessage());
            }
        }

        $this->table(
            ['Description', 'Number'],
            [
                ['Paid invoices from Stripe', $totalInvoices],
                ['New payments recorded', $synced],
                ['Skipped (already exists or does not match plan)', $skipped],
            ]
        );

        if ($totalInvoices > 0 && $synced === 0 && $skipped > 0) {
            $this->warn('No new payments were recorded. Check that each plan has stripe_price_id matching Stripe.');
        }
    }
}
