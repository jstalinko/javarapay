<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Services\TripayService;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SyncTripayPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:tripay-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync payment methods from Tripay and download icons to local storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Tripay payment methods sync...');

        $tripay = new TripayService();
        $response = $tripay->channels();

        if (isset($response['success']) && !$response['success']) {
            $this->error('Failed to fetch channels from Tripay: ' . ($response['message'] ?? 'Unknown error'));
            return Command::FAILURE;
        }

        // Handle case where channels() might return the response array directly or nested
        $channels = $response['data'] ?? [];

        if (empty($channels)) {
            $this->warn('No payment channels found in the response.');
            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar(count($channels));
        $bar->start();

        foreach ($channels as $channel) {
            $code = $channel['code'] ?? '';
            $iconUrl = $channel['icon_url'] ?? '';
            
            if (!$code) continue;

            // Download and save image
            $imagePath = null;
            if ($iconUrl) {
                try {
                    $imageContents = Http::get($iconUrl)->body();
                    $filename = 'payment-methods/' . Str::slug($code) . '.png';
                    Storage::disk('public')->put($filename, $imageContents);
                    $imagePath = 'storage/' . $filename;
                } catch (\Exception $e) {
                    $this->warn("\nFailed to download icon for {$code}: " . $e->getMessage());
                }
            }

            PaymentMethod::updateOrCreate(
                ['method_code' => $code],
                [
                    'group' => $channel['group'] ?? null,
                    'method_name' => $channel['name'] ?? $code,
                    'gateway' => 'tripay',
                    'image' => $imagePath,
                    'is_qris' => Str::contains(strtoupper($code), 'QRIS'),
                    'min_amount' => $channel['minimum_amount'] ?? null,
                    'max_amount' => $channel['maximum_amount'] ?? null,
                    'fee_percent' => $channel['total_fee']['percent'] ?? 0,
                    'fee_flat' => $channel['total_fee']['flat'] ?? 0,
                    'active' => $channel['active'] ?? true,
                ]
            );

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nSync completed successfully!");

        return Command::SUCCESS;
    }
}
