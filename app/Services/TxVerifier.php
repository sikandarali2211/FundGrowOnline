<?php

namespace App\Services;

class TxVerifier
{
    public function __construct(protected BscScanService $scan) {}

    /**
     * Verify a USDT(BEP-20) incoming tx to admin by txHash.
     * Returns normalized data or null if not found/invalid.
     */
    public function verifyUsdtIncoming(string $txHash, string $adminAddress): ?array
    {
        $admin = strtolower($adminAddress);
        // Pull latest token transfers of admin and search for this hash
        $page = 1; $offset = 200; $sort = 'desc';
        $list = $this->scan->getTokenTx($admin, $page, $offset, $sort);

        $usdt = strtolower('0x55d398326f99059fF775485246999027B3197955');

        foreach ($list as $tx) {
            if (strcasecmp($tx['hash'] ?? '', $txHash) !== 0) continue;
            if (strtolower($tx['contractAddress'] ?? '') !== $usdt) return null;
            if (strtolower($tx['to'] ?? '') !== $admin) return null;

            $dec = (int)($tx['tokenDecimal'] ?? 18);
            return [
                'hash'         => $tx['hash'],
                'from'         => strtolower($tx['from'] ?? ''),
                'to'           => strtolower($tx['to'] ?? ''),
                'amount_raw'   => (string)($tx['value'] ?? '0'),
                'amount'       => BscScanService::formatAmount((string)($tx['value'] ?? '0'), $dec), // USDT
                'timestamp'    => (int)($tx['timeStamp'] ?? 0),
            ];
        }
        return null;
    }
}
