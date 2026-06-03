<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaction;

class UpdateAvailableBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'balance:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update available balance by settling transactions older than 5 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting available balance update (settling transactions)...');

        $cutoffDate = now()->subDays(5);

        // Find all PAID transactions that have not been settled, and paid_at is older than or equal to 5 days
        $transactions = Transaction::where('status', 'PAID')
            ->whereNull('settled_at')
            ->whereNotNull('paid_at')
            ->where('paid_at', '<=', $cutoffDate)
            ->get();

        $count = $transactions->count();

        if ($count === 0) {
            $this->info('No transactions found to settle (paid >= 5 days ago).');
            return Command::SUCCESS;
        }

        foreach ($transactions as $transaction) {
            $transaction->settled_at = now();
            $transaction->save();
        }

        $this->info("Successfully settled {$count} transaction(s) and updated available balance.");

        return Command::SUCCESS;
    }
}
