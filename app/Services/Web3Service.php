<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Web3Service
{
    protected string $bscRpcUrl;
    protected string $usdtContractAddress;
    protected string $adminPrivateKey;
    protected string $adminAddress;
    protected int $chainId;
    protected int $gasLimit;
    protected string $gasPrice;

    public function __construct()
    {
        $this->bscRpcUrl = config('services.bscscan.rpc_url', 'https://bsc-dataseed.binance.org/');
        $this->usdtContractAddress = '0x55d398326f99059fF775485246999027B3197955'; // USDT BEP-20
        $this->adminPrivateKey = config('services.bscscan.admin_private_key');
        $this->adminAddress = config('services.bscscan.admin_address');
        $this->chainId = 56; // BSC Mainnet
        $this->gasLimit = 100000;
        $this->gasPrice = '5000000000'; // 5 Gwei
    }

    /**
     * Get current gas price from BSC network
     */
    public function getCurrentGasPrice(): string
    {
        try {
            $response = Http::post($this->bscRpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_gasPrice',
                'params' => [],
                'id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['result'] ?? $this->gasPrice;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get gas price: ' . $e->getMessage());
        }

        return $this->gasPrice;
    }

    /**
     * Get USDT balance of an address
     */
    public function getUSDTBalance(string $address): string
    {
        try {
            $response = Http::post($this->bscRpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_call',
                'params' => [
                    [
                        'to' => $this->usdtContractAddress,
                        'data' => '0x70a08231' . $this->padAddress($address)
                    ],
                    'latest'
                ],
                'id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $balance = $data['result'] ?? '0x0';
                return $this->hexToDecimal($balance);
            }
        } catch (\Exception $e) {
            Log::error('Failed to get USDT balance: ' . $e->getMessage());
        }

        return '0';
    }

    /**
     * Check if admin has sufficient USDT balance
     */
    public function hasSufficientBalance(string $amount): bool
    {
        $balance = $this->getUSDTBalance($this->adminAddress);
        return bccomp($balance, $amount, 18) >= 0;
    }

    /**
     * Get nonce for admin address
     */
    public function getNonce(string $address): string
    {
        try {
            $response = Http::post($this->bscRpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionCount',
                'params' => [$address, 'latest'],
                'id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['result'] ?? '0x0';
            }
        } catch (\Exception $e) {
            Log::error('Failed to get nonce: ' . $e->getMessage());
        }

        return '0x0';
    }

    /**
     * Create USDT transfer transaction
     */
    public function createTransferTransaction(string $toAddress, string $amount): array
    {
        try {
            $nonce = $this->getNonce($this->adminAddress);
            $gasPrice = $this->getCurrentGasPrice();
            
            // Convert amount to wei (USDT has 18 decimals)
            $amountWei = $this->decimalToHex($amount, 18);
            
            // Create transfer function call data
            $transferData = '0xa9059cbb' . $this->padAddress($toAddress) . $amountWei;
            
            $transaction = [
                'from' => $this->adminAddress,
                'to' => $this->usdtContractAddress,
                'value' => '0x0',
                'gas' => '0x' . dechex($this->gasLimit),
                'gasPrice' => $gasPrice,
                'nonce' => $nonce,
                'data' => $transferData,
                'chainId' => '0x' . dechex($this->chainId)
            ];

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Failed to create transfer transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sign transaction with admin private key
     */
    public function signTransaction(array $transaction): string
    {
        try {
            // This is a simplified version. In production, you would use a proper
            // Web3 PHP library or implement proper ECDSA signing
            $transactionHash = hash('sha256', json_encode($transaction));
            
            // For security, this should be done with proper cryptographic libraries
            // and the private key should never be stored in plain text
            Log::warning('Transaction signing implemented for demo purposes only');
            
            return $transactionHash;
        } catch (\Exception $e) {
            Log::error('Failed to sign transaction: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send signed transaction to BSC network
     */
    public function sendTransaction(string $signedTransaction): array
    {
        try {
            $response = Http::post($this->bscRpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_sendRawTransaction',
                'params' => ['0x' . $signedTransaction],
                'id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'transactionHash' => $data['result'] ?? null,
                    'response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Failed to send transaction',
                    'response' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to send transaction: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get transaction receipt
     */
    public function getTransactionReceipt(string $txHash): ?array
    {
        try {
            $response = Http::post($this->bscRpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionReceipt',
                'params' => [$txHash],
                'id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['result'] ?? null;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get transaction receipt: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if transaction is confirmed
     */
    public function isTransactionConfirmed(string $txHash): bool
    {
        $receipt = $this->getTransactionReceipt($txHash);
        return $receipt && ($receipt['status'] ?? '0x0') === '0x1';
    }

    /**
     * Utility: Pad address to 32 bytes
     */
    private function padAddress(string $address): string
    {
        $address = str_replace('0x', '', $address);
        return str_pad($address, 64, '0', STR_PAD_LEFT);
    }

    /**
     * Utility: Convert decimal to hex with specified decimals
     */
    private function decimalToHex(string $decimal, int $decimals): string
    {
        $multiplier = bcpow('10', (string)$decimals);
        $wei = bcmul($decimal, $multiplier, 0);
        return '0x' . str_pad(dechex($wei), 64, '0', STR_PAD_LEFT);
    }

    /**
     * Utility: Convert hex to decimal
     */
    private function hexToDecimal(string $hex): string
    {
        $hex = str_replace('0x', '', $hex);
        $decimal = '0';
        $length = strlen($hex);
        
        for ($i = 0; $i < $length; $i++) {
            $decimal = bcmul($decimal, '16');
            $decimal = bcadd($decimal, (string)hexdec($hex[$i]));
        }
        
        return $decimal;
    }

    /**
     * Get BSC network status
     */
    public function getNetworkStatus(): array
    {
        try {
            $response = Http::post($this->bscRpcUrl, [
                'jsonrpc' => '2.0',
                'method' => 'eth_blockNumber',
                'params' => [],
                'id' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'blockNumber' => $data['result'] ?? '0x0',
                    'network' => 'BSC Mainnet'
                ];
            }
        } catch (\Exception $e) {
            Log::error('Failed to get network status: ' . $e->getMessage());
        }

        return [
            'success' => false,
            'error' => 'Failed to connect to BSC network'
        ];
    }
}
