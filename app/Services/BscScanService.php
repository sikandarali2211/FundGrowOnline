<?php

namespace App\Services;


namespace App\Services;

use Illuminate\Support\Facades\Http;

class BscScanService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected int $chainId;

    public function __construct()
    {
        $this->baseUrl = config('services.bscscan.base_url', 'https://api.etherscan.io/v2/api');
        $this->apiKey  = config('services.bscscan.key');
        $this->chainId = (int) config('services.bscscan.chain_id', 56); // BSC mainnet
    }

    /**
     * Low-level request helper for Etherscan V2 (multichain)
     */
    protected function request(array $params)
    {
        $params = array_merge([
            'chainid' => $this->chainId,
            'apikey'  => $this->apiKey,
        ], $params);

        $resp = Http::retry(2, 300)->get($this->baseUrl, $params);
        if (!$resp->ok()) {
            throw new \Exception('Etherscan V2 HTTP error: ' . $resp->status());
        }

        $json    = $resp->json();
        $status  = $json['status']  ?? '0';
        $message = $json['message'] ?? '';

        if ((string)$status !== '1' && stripos($message, 'No transactions found') === false) {
            throw new \Exception('Etherscan V2 API error: ' . json_encode($json));
        }

        return $json['result'] ?? [];
    }

    /**
     * Native BNB tx list — V2
     */
    public function getNormalTx(string $address, int $page = 1, int $offset = 1000, string $sort = 'desc'): array
    {
        return $this->request([
            'module'     => 'account',
            'action'     => 'txlist',
            'address'    => $address,
            'startblock' => 0,
            'endblock'   => 99999999,
            'page'       => $page,
            'offset'     => $offset,
            'sort'       => $sort,
        ]);
    }

    /**
     * BEP-20 token transfers — V2
     */
    public function getTokenTx(string $address, int $page = 1, int $offset = 1000, string $sort = 'desc', ?string $contractAddress = null): array
    {
        $params = [
            'module'     => 'account',
            'action'     => 'tokentx',
            'address'    => $address,
            'startblock' => 0,
            'endblock'   => 99999999,
            'page'       => $page,
            'offset'     => $offset,
            'sort'       => $sort,
        ];
        if (!empty($contractAddress)) {
            $params['contractaddress'] = $contractAddress;
        }
        return $this->request($params);
    }

    /**
     * BNB balance (wei) — V2
     */
    public function getBalanceWei(string $address): string
    {
        $res = $this->request([
            'module'  => 'account',
            'action'  => 'balance',
            'address' => $address,
            'tag'     => 'latest',
        ]);
        return is_string($res) ? $res : (string)($res[0] ?? '0');
    }

    /**
     * Format integer by decimals (no bcmath needed)
     */
    public static function formatAmount(string $raw, int $decimals): string
    {
        $raw = preg_replace('/^\+/', '', (string)$raw) ?? '0';
        if ($decimals <= 0) return $raw;
        $pad  = str_pad($raw, $decimals + 1, '0', STR_PAD_LEFT);
        $int  = substr($pad, 0, -$decimals);
        $frac = rtrim(substr($pad, -$decimals), '0');
        return $frac === '' ? $int : ($int . '.' . $frac);
    }
}
