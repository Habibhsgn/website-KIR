<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran - {{ $orderId ?? 'N/A' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f7f9fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .status-settlement {
            background-color: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }

        .status-pending {
            background-color: #fffbeb;
            color: #92400e;
            border: 2px solid #f59e0b;
        }

        .status-failed {
            background-color: #fee2e2;
            color: #991b1b;
            border: 2px solid #f87171;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5 text-center">

                        <h1 class="h3 fw-bold text-dark mb-3">Status Transaksi</h1>
                        <p class="text-muted mb-4">Informasi status pembayaran Midtrans Anda.</p>

                        @php
                            $statusClass = '';
                            $statusText = strtoupper($status ?? 'UNKNOWN');
                            $icon = '❓';

                            if (in_array($statusText, ['SETTLEMENT', 'SUCCESS', 'CAPTURE'])) {
                                $statusClass = 'status-settlement';
                                $statusText = 'BERHASIL';
                                $icon = '✅';
                            } elseif ($statusText === 'PENDING') {
                                $statusClass = 'status-pending';
                                $statusText = 'MENUNGGU PEMBAYARAN';
                                $icon = '⏳';
                            } elseif (in_array($statusText, ['FAILED', 'CANCEL', 'EXPIRE'])) {
                                $statusClass = 'status-failed';
                                $statusText = 'GAGAL / KADALUARSA';
                                $icon = '❌';
                            }
                        @endphp

                        <!-- STATUS CARD -->
                        <div class="p-4 rounded-3 mb-4 {{ $statusClass }}">
                            <div class="display-4 mb-2">{{ $icon }}</div>
                            <h5 class="fw-bold">{{ $statusText }}</h5>
                        </div>

                        <!-- DETAIL INFO -->
                        <div class="bg-light rounded-3 p-3 text-start">
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-secondary fw-medium">Order ID:</span>
                                <span class="fw-semibold text-dark">{{ $orderId ?? 'N/A' }}</span>
                            </div>

                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="text-secondary fw-medium">Status Transaksi:</span>
                                <span class="fw-semibold text-dark">{{ strtoupper($status ?? 'N/A') }}</span>
                            </div>

                            @if (!empty($nomorAntrian) && $nomorAntrian !== '-')
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="text-secondary fw-medium">Nomor Antrian:</span>
                                    <span class="fw-semibold text-dark">{{ $nomorAntrian ?? 'N/A' }}</span>
                                </div>
                            @endif

                            @if (!empty($tanggalKunjungan) && $tanggalKunjungan !== '-')
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="text-secondary fw-medium">Tanggal Kunjungan:</span>
                                    <span class="fw-semibold text-dark">{{ $tanggalKunjungan ?? 'N/A' }}</span>
                                </div>
                            @endif


                            <p class="small text-muted fst-italic mb-0">
                                {{ $message ?? 'Tidak ada pesan spesifik.' }}
                            </p>
                        </div>

                        <!-- BUTTON -->
                        <div class="mt-4">
                            <a href="/" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
                                Kembali ke Beranda
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
