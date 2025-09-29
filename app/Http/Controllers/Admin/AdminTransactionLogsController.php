<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BscScanService;
use App\Models\PaymentTransaction; // optional (if you store gateway tx here)
use App\Models\Transaction;        // optional (your internal tx table)
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AdminTransactionLogsController extends Controller
{
    protected BscScanService $bsc;
    protected string $adminAddress;

    public function __construct(BscScanService $bsc)
    {
        $this->bsc = $bsc;
        $this->adminAddress = strtolower(config('services.bscscan.admin_address', '0x3Bb750C42f9B80CbEd7003c004eaeAdc76c9b4Fd'));
    }

    public function index(Request $request)
    {
        $address   = strtolower($request->get('address', $this->adminAddress));
        $type      = $request->get('type', 'all');      // all|bnb|token
        $direction = $request->get('direction', 'all'); // all|sent|received
        $perPage   = (int) $request->get('perPage', 25);
        $page      = (int) $request->get('page', 1);

        $cacheTtl  = 20;

        // Balance
        $balanceWei = Cache::remember("bsc:balance:$address", $cacheTtl, fn () => $this->bsc->getBalanceWei($address));
        $balanceBNB = BscScanService::formatAmount($balanceWei, 18);

        // Blockchain txs
        $normal = ($type === 'token') ? [] : Cache::remember("bsc:normal:$address", $cacheTtl, fn () => $this->bsc->getNormalTx($address, 1, 1000, 'desc'));
        $token  = ($type === 'bnb')   ? [] : Cache::remember("bsc:token:$address",  $cacheTtl, fn () => $this->bsc->getTokenTx($address, 1, 1000, 'desc'));

        // --------- DB matching by tx hash (optional if you store tx hashes) ----------
        $allTxHashes = [];
        foreach ($normal as $tx) $allTxHashes[] = strtolower($tx['hash']);
        foreach ($token  as $tx) $allTxHashes[] = strtolower($tx['hash']);
        $allTxHashes = array_values(array_unique($allTxHashes));

        $dbTransactions = collect();
        $dbNormalTransactions = collect();

        if (class_exists(PaymentTransaction::class)) {
            $ptx = PaymentTransaction::with('user:id,name,email')->get();
            foreach ($ptx as $row) {
                $h = strtolower($row->transaction_hash ?? '');
                if ($h !== '' && in_array($h, $allTxHashes, true)) {
                    $dbTransactions->put($h, $row);
                }
            }
        }

        if (class_exists(Transaction::class)) {
            $trx = Transaction::with('user:id,name,email')->get();
            foreach ($trx as $row) {
                $h = strtolower($row->tx_hash ?? '');
                if ($h !== '' && in_array($h, $allTxHashes, true)) {
                    $dbNormalTransactions->put($h, $row);
                }
            }
        }

        // --------- Fallback: sender address -> user (users.wallet_address) ----------
        // collect all FROM addresses
        $fromAddresses = [];
        foreach ($normal as $tx) if (!empty($tx['from'])) $fromAddresses[] = strtolower($tx['from']);
        foreach ($token  as $tx) if (!empty($tx['from'])) $fromAddresses[] = strtolower($tx['from']);
        $fromAddresses = array_values(array_unique($fromAddresses));

        // fetch all users with wallet_address
        $addressUserMap = [];
        $users = User::whereNotNull('wallet_address')->get(['id','name','email','wallet_address']);
        foreach ($users as $u) {
            $wa = strtolower(trim($u->wallet_address ?? ''));
            if ($wa !== '') $addressUserMap[$wa] = $u;
        }

        // --------- Build unified rows ----------
        $rows = [];

        // BNB txs
        foreach ($normal as $tx) {
            $from = strtolower($tx['from'] ?? '');
            $to   = strtolower($tx['to'] ?? '');
            $txHashLower = strtolower($tx['hash']);

            // 1) try by db hash
            $dbTx = $dbTransactions->get($txHashLower) ?? $dbNormalTransactions->get($txHashLower);
            if ($dbTx && $dbTx->user) {
                $userInfo = [
                    'user_id'    => $dbTx->user_id,
                    'user_name'  => $dbTx->user->name ?? 'N/A',
                    'user_email' => $dbTx->user->email ?? 'N/A',
                ];
            } else {
                // 2) fallback by sender address
                $u = $addressUserMap[$from] ?? null;
                $userInfo = $u ? [
                    'user_id'    => $u->id,
                    'user_name'  => $u->name,
                    'user_email' => $u->email,
                ] : [
                    'user_id'    => null,
                    'user_name'  => 'Unknown',
                    'user_email' => 'N/A',
                ];
            }

            $rows[] = [
                'hash'          => $tx['hash'],
                'timestamp'     => (int)($tx['timeStamp'] ?? 0),
                'date'          => Carbon::createFromTimestamp((int)($tx['timeStamp'] ?? 0))->toDateTimeString(),
                'from'          => $from,
                'to'            => $to,
                'direction'     => $from === $address ? 'sent' : 'received',
                'tokenSymbol'   => 'BNB',
                'tokenDecimal'  => 18,
                'amount_raw'    => $tx['value'] ?? '0',
                'amount'        => BscScanService::formatAmount((string)($tx['value'] ?? '0'), 18),
                'status'        => (($tx['isError'] ?? '0') === '0') ? 'Success' : 'Failed',
                'type'          => 'bnb',
                'user_id'       => $userInfo['user_id'],
                'user_name'     => $userInfo['user_name'],
                'user_email'    => $userInfo['user_email'],
            ];
        }

        // Token txs
        foreach ($token as $tx) {
            $dec  = (int)($tx['tokenDecimal'] ?? 18);
            $from = strtolower($tx['from'] ?? '');
            $to   = strtolower($tx['to'] ?? '');
            $txHashLower = strtolower($tx['hash']);

            $dbTx = $dbTransactions->get($txHashLower) ?? $dbNormalTransactions->get($txHashLower);
            if ($dbTx && $dbTx->user) {
                $userInfo = [
                    'user_id'    => $dbTx->user_id,
                    'user_name'  => $dbTx->user->name ?? 'N/A',
                    'user_email' => $dbTx->user->email ?? 'N/A',
                ];
            } else {
                $u = $addressUserMap[$from] ?? null;
                $userInfo = $u ? [
                    'user_id'    => $u->id,
                    'user_name'  => $u->name,
                    'user_email' => $u->email,
                ] : [
                    'user_id'    => null,
                    'user_name'  => 'Unknown',
                    'user_email' => 'N/A',
                ];
            }

            $rows[] = [
                'hash'          => $tx['hash'],
                'timestamp'     => (int)($tx['timeStamp'] ?? 0),
                'date'          => Carbon::createFromTimestamp((int)($tx['timeStamp'] ?? 0))->toDateTimeString(),
                'from'          => $from,
                'to'            => $to,
                'direction'     => $from === $address ? 'sent' : 'received',
                'tokenSymbol'   => $tx['tokenSymbol'] ?? 'TOKEN',
                'tokenDecimal'  => $dec,
                'contract'      => $tx['contractAddress'] ?? null,
                'amount_raw'    => $tx['value'] ?? '0',
                'amount'        => BscScanService::formatAmount((string)($tx['value'] ?? '0'), $dec),
                'status'        => 'Success',
                'type'          => 'token',
                'user_id'       => $userInfo['user_id'],
                'user_name'     => $userInfo['user_name'],
                'user_email'    => $userInfo['user_email'],
            ];
        }

        // filters
        if ($direction !== 'all') {
            $rows = array_values(array_filter($rows, fn ($r) => $r['direction'] === $direction));
        }
        if ($type !== 'all') {
            $rows = array_values(array_filter($rows, fn ($r) => $r['type'] === $type));
        }

        // sort + paginate
        usort($rows, fn ($a, $b) => $b['timestamp'] <=> $a['timestamp']);
        $total = count($rows);
        $items = array_slice($rows, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $items, $total, $perPage, $page,
            ['path' => route('admin.transactionlog.index')]
        );

        return view('admin.transcationlog.index', [
            'address'      => $address,
            'balanceBNB'   => $balanceBNB,
            'transactions' => $paginator,
            'type'         => $type,
            'direction'    => $direction,
            'perPage'      => $perPage,
        ]);
    }
}
