@extends('dashboard.layouts.app')

@push('css')
<style>
    .qr-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .qr-box { display: flex; justify-content: center; margin-bottom: 0.75rem; }
    .qr-box img, .qr-box canvas { border-radius: 8px; }
    .table-label {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e1a16;
        margin-bottom: 0.4rem;
    }
    .qr-url {
        font-size: 0.6rem;
        color: #bbb;
        word-break: break-all;
    }
    @media print {
        .no-print { display: none !important; }
        .col-6 { page-break-inside: avoid; }
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h2 class="mb-0">🍽️ Table QR Codes</h2>
    <button class="btn btn-dark" onclick="window.print()">🖨️ Print All</button>
</div>

<div class="row g-4">
    @foreach($tables as $tableNumber => $url)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2">
            <div class="qr-card">
                <div class="qr-box">
                    <div id="qr-{{ $tableNumber }}"></div>
                </div>
                <div class="table-label">Table {{ $tableNumber }}</div>
                <div class="qr-url">{{ $url }}</div>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    const tables = @json($tables);

    Object.entries(tables).forEach(([tableNumber, url]) => {
        new QRCode(document.getElementById('qr-' + tableNumber), {
            text: url,
            width: 150,
            height: 150,
            colorDark: '#1e1a16',
            colorLight: '#ffffff',
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endpush