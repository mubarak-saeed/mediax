<?php

namespace App\Console\Commands;

use App\Models\Plan;
use App\Services\PlanStripeSyncService;
use Illuminate\Console\Command;

class SyncStripePlansCommand extends Command
{
    protected $signature = 'plans:sync-stripe
                            {--plan= : Sync only a specific plan ID}
                            {--force : Sync even if plan has no stripe_price_id}';

    protected $description = 'Sync plan data (name, price, currency, interval) from Stripe';

    public function handle(PlanStripeSyncService $syncService): int
    {
        $planId = $this->option('plan');
        $force = (bool) $this->option('force');

        $query = Plan::query();
        if ($planId) {
            $query->where('id', $planId);
        }

        $plans = $query->get();

        if ($plans->isEmpty()) {
            $this->warn('No plans found.');

            return self::SUCCESS;
        }

        $synced = 0;
        $failed = 0;

        foreach ($plans as $plan) {
            if (empty($plan->stripe_price_id) && ! $force) {
                $this->line("  <fg=yellow>Skip</> Plan #{$plan->id} ({$plan->name}): no stripe_price_id");

                continue;
            }

            try {
                $syncService->sync($plan);
                $synced++;
                $this->line("  <fg=green>✓</> Plan #{$plan->id} ({$plan->name}): {$plan->price} {$plan->currency}/{$plan->interval_label}");
            } catch (\Throwable $e) {
                $failed++;
                $this->error("  ✗ Plan #{$plan->id} ({$plan->name}): {$e->getMessage()}");
            }
        }

        $this->newLine();
        $this->info("Sync completed: {$synced} synced, {$failed} failed.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
