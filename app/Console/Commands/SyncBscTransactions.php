<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BscScanService;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Arr;

class SyncBscTransactions extends Command
{
    protected $signature = 'tx:sync-bsc {--address=} {--limit=500}';
    protected $description = 'Fetch latest BNB & BEP20 transfers for admin wallet and store NEW transactions in DB';

    protected BscScanService $bsc;

    public function __construct(BscScanService $bsc)
    {
        parent::__construct();
        $this->bsc = $bsc;
    }

    public function handle(): int
    {
        // 1) Admin wallet address
        $adminAddress = strtolower(
            $this->option('address')
            ?: config('services.bscscan.admin_address')
            ?: (string) User::where('utype','ADM')->whereNotNull('wallet_address')->value('wallet_address')
        );

        if (!$adminAddress) {
            $this->error('Admin wallet address not configured. Set services.bscscan.admin_address or pass --address=');
            return Command::FAILURE;
        }

        $perPage = (int) $this->option('limit') ?: 500;

        // 2) Last stored block to avoid re-inserts
        $lastBlock = (int) (Transaction::where('to_address', $adminAddress)->max('block_number')
                     ?? Transaction::max('block_number') ?? 0);

        // Thoda overlap to be safe
        $minBlock = max(0, $lastBlock - 5);

        $this->info("Admin: {$adminAddress}");
        $this->info("Scanning from block: {$minBlock} (last in DB: {$lastBlock})");

        // 3) Pull latest on-chain tx (BNB native + Token transfers)
        // NOTE: BscScanService returns DESC order; we will filter > $minBlock
        $normal = $this->bsc->getNormalTx($adminAddress, 1, $perPage, 'desc');   // native BNB
        $token  = $this->bsc->getTokenTx($adminAddress, 1, $perPage, 'desc');    // BEP20 transfers

        $inserted = 0; $skipped = 0;

        // -------- BNB Native Tx --------
        foreach ($normal as $tx) {
            $block     = (int) ($tx['blockNumber'] ?? 0);
            $hash      = strtolower($tx['hash'] ?? '');
            $from      = strtolower($tx['from'] ?? '');
            $to        = strtolower($tx['to'] ?? '');
            $valueRaw  = (string) ($tx['value'] ?? '0');      // wei
            $isError   = (string) ($tx['isError'] ?? '0');
            $status    = $isError === '0' ? 'confirmed' : 'failed';
            $direction = $from === $adminAddress ? 'send' : 'receive';

            // Sirf naye blocks / received txs store karna (zarurat ho to send bhi rakhein)
            if ($block <= $minBlock) { $skipped++; continue; }
            if (!$hash) { $skipped++; continue; }

            // Do not store empty "to" or weird tx
            if (!$to) { $skipped++; continue; }

            // Find user by sender wallet (optional)
            $userId = optional(User::where('wallet_address', $from)->first())->id;

            // Amount (BNB 18 decimals)
            $amount = (float) \App\Services\BscScanService::formatAmount($valueRaw, 18);

            // Skip if already exists
            if (Transaction::where('tx_hash', $hash)->exists()) { $skipped++; continue; }

            Transaction::create([
                'user_id'        => $userId,
                'tx_hash'        => $hash,
                'from_address'   => $from,
                'to_address'     => $to,
                'amount'         => $amount,
                'token_address'  => null,
                'token_symbol'   => 'BNB',
                'status'         => $status,
                'block_number'   => $block,
                'type'           => $direction, // 'send' | 'receive'
                'transaction_data' => json_encode($tx),
                'confirmed_at'   => $status === 'confirmed' ? now() : null,
            ]);
            $inserted++;
        }

        // -------- BEP20 Token Tx --------
        foreach ($token as $tx) {
            $block    = (int) ($tx['blockNumber'] ?? 0);
            $hash     = strtolower($tx['hash'] ?? '');
            $from     = strtolower($tx['from'] ?? '');
            $to       = strtolower($tx['to'] ?? '');
            $raw      = (string) ($tx['value'] ?? '0');
            $dec      = (int) ($tx['tokenDecimal'] ?? 18);
            $symbol   = (string) ($tx['tokenSymbol'] ?? 'TOKEN');
            $contract = (string) ($tx['contractAddress'] ?? null);

            if ($block <= $minBlock) { $skipped++; continue; }
            if (!$hash) { $skipped++; continue; }

            // Sirf woh tx jo admin wallet par receive huay (aap chaahein to both direction rakh lein)
            $direction = $from === $adminAddress ? 'send' : 'receive';
            if ($direction !== 'receive') { $skipped++; continue; }

            $userId = optional(User::where('wallet_address', $from)->first())->id;
            $amount = (float) \App\Services\BscScanService::formatAmount($raw, $dec);

            if (Transaction::where('tx_hash', $hash)->exists()) { $skipped++; continue; }

            Transaction::create([
                'user_id'        => $userId,
                'tx_hash'        => $hash,
                'from_address'   => $from,
                'to_address'     => $to,
                'amount'         => $amount,
                'token_address'  => $contract,
                'token_symbol'   => $symbol,
                'status'         => 'confirmed',
                'block_number'   => $block,
                'type'           => $direction, // receive
                'transaction_data' => json_encode($tx),
                'confirmed_at'   => now(),
            ]);
            $inserted++;
        }

        $this->info("Inserted: {$inserted}, Skipped: {$skipped}");
        return Command::SUCCESS;
    }
}
