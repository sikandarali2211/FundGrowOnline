@extends('layouts.user')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- ===== My Referral Details (as table) ===== --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">My Referral Details</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <tbody>
                                <tr>
                                    <th style="width: 220px;">Name</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $user->phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Referral Code</th>
                                    <td>
                                        <span id="refCodeText" class="me-2">{{ $user->referral_code }}</span>
                                        <button class="btn btn-sm btn-outline-secondary" type="button" onclick="copyFrom('refCodeText')">
                                            Copy
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Your Referral Link</th>
                                    <td class="text-wrap">
                                        <span id="refLinkText" class="me-2 d-inline-block" style="max-width:100%; word-break:break-all;">
                                            {{ $referralUrl }}
                                        </span>
                                        <button class="btn btn-sm btn-primary" type="button" onclick="copyFrom('refLinkText')">
                                            Copy Link
                                        </button>
                                        <!-- <div class="small text-muted mt-2">
                                            Is link ko share karein. Jo bhi is link se register karega, aapka <strong>referred_by</strong> set ho jayega.
                                        </div> -->
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ===== (Optional) My Direct Referrals table ===== --}}
            @isset($directs)
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Direct Referrals</h5>
                    <small class="opacity-75">Total: {{ $directs->total() }}</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Joined</th>
                                    <th>Used Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($directs as $idx => $u)
                                <tr>
                                    <td>{{ $directs->firstItem() + $idx }}</td>
                                    <td>{{ $u->name }}</td>
                                    <td>{{ $u->email }}</td>
                                    <td>{{ $u->phone }}</td>
                                    <td>{{ $u->created_at?->format('d M Y, h:i A') }}</td>
                                    <td>{{ $u->referral }}</td> {{-- register time par jis code se aaye the --}}
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">No direct referrals yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="p-3">
                        {{ $directs->links() }}
                    </div>
                </div>
            </div>
            @endisset

        </div>
    </div>
</div>

<script>
    function copyFrom(id) {
        const txt = document.getElementById(id)?.innerText?.trim() ?? '';
        if (!txt) return alert('Nothing to copy');
        navigator.clipboard.writeText(txt)
            .then(() => alert('Copied!'))
            .catch(() => {
                // Fallback
                const sel = window.getSelection();
                const range = document.createRange();
                range.selectNodeContents(document.getElementById(id));
                sel.removeAllRanges();
                sel.addRange(range);
                document.execCommand('copy');
                sel.removeAllRanges();
                alert('Copied!');
            });
    }
</script>
@endsection