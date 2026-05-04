<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color:#111; }
    .title { text-align:center; font-size:18px; font-weight:bold; margin:8px 0 14px; }
    .company { text-align:center; margin-bottom:10px; }
    .meta { width:100%; margin-bottom:10px; }
    .meta td { padding:3px 4px; vertical-align:top; }
    .section-title { font-weight:bold; margin-top:12px; }
    table { width:100%; border-collapse: collapse; margin-top: 10px;}
    th, td { border:1px solid #333; padding:5px; }
    th { background:#eee; }
    .right { text-align:right; }
    .no-border td { border:none; padding:2px 0; }
    .notes { margin-top:10px; }
  </style>
</head>
<body>
  <div class="company">
    <div><strong>{{ $setting->company_name ?? 'PT. INTEGRASI MEDIA NUSANTARA' }}</strong></div>
    <div>{{ $setting->address ?? '' }}</div>
  </div>
  <div class="title">
    {{ optional($invoice->invoiceType)->is_license ? 'INVOICE LICENSE' : 'INVOICE PRODUK / NON LICENSE' }}
  </div>

  <table class="meta">
    <tr>
      <td width="14%"><strong>Jenis Invoice</strong></td>
      <td width="36%">{{ $invoice->invoiceType->code ?? '' }} / {{ $invoice->invoiceType->name ?? '' }}</td>
      <td width="14%"><strong>No. BM/KM</strong></td>
      <td width="36%">{{ $invoice->invoice_bm_km ?? '-' }}</td>
    </tr>
    <tr>
      <td><strong>Tanggal</strong></td>
      <td>{{ optional($invoice->date)->format('d-m-Y') }}</td>
      <td><strong>Tanggal BM/KM</strong></td>
      <td>{{ optional($invoice->invoice_bm_km_date)->format('d-m-Y') }}</td>
    </tr>
    <tr>
      <td><strong>No. Invoice</strong></td>
      <td>{{ $invoice->number }}</td>
      <td><strong>Mode Invoice</strong></td>
      <td>{{ $invoice->invoice_mode ?? 'NORMAL' }}</td>
    </tr>
    <tr>
      <td><strong>Kode Klien</strong></td>
      <td>{{ $invoice->client->code ?? '-' }} / {{ $invoice->client->name ?? '-' }}</td>
      <td><strong>Jatuh Tempo</strong></td>
      <td>{{ optional($invoice->due_date)->format('d-m-Y') }}</td>
    </tr>
    <tr>
      <td><strong>Alamat</strong></td>
      <td colspan="3">{{ $invoice->client_address ?: ($invoice->client->address ?? '-') }}</td>
    </tr>
    <tr>
      <td><strong>Jenis Faktur</strong></td>
      <td>{{ $invoice->tax_type ?? '-' }}</td>
      <td><strong>Instansi</strong></td>
      <td>{{ $invoice->instance ?? '-' }}</td>
    </tr>
    <tr>
      <td><strong>No. F. Pajak</strong></td>
      <td>{{ $invoice->tax_number ?? '-' }}</td>
      <td><strong>Tgl. F. Pajak</strong></td>
      <td>{{ optional($invoice->tax_date)->format('d-m-Y') }}</td>
    </tr>
  </table>

  <table>
    <thead>
      <tr>
        <th>Kd Item</th>
        <th>Nama Item</th>
        <th>Keterangan</th>
        <th class="right">Qty</th>
        <th>Satuan</th>
        <th class="right">Harga</th>
        <th class="right">Bruto</th>
      </tr>
    </thead>
    <tbody>
      @foreach($invoice->items as $it)
      <tr>
        <td>{{ $it->item_code }}</td>
        <td>{{ $it->item_name }}</td>
        <td>{{ $it->description }}</td>
        <td class="right">{{ $it->qty }}</td>
        <td>{{ $it->unit }}</td>
        <td class="right">{{ number_format($it->price, 2) }}</td>
        <td class="right">{{ number_format($it->bruto, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <table>
    <tr><td class="right"><strong>Jumlah</strong></td><td class="right">{{ number_format($invoice->bruto,2) }}</td></tr>
    <tr><td class="right"><strong>Discount</strong></td><td class="right">{{ number_format($invoice->discount,2) }}</td></tr>
    <tr><td class="right"><strong>DPP</strong></td><td class="right">{{ number_format($invoice->dpp,2) }}</td></tr>
    <tr><td class="right"><strong>PPN</strong></td><td class="right">{{ number_format($invoice->ppn,2) }}</td></tr>
    <tr><td class="right"><strong>DP</strong></td><td class="right">{{ number_format($invoice->dp,2) }}</td></tr>
    <tr><td class="right"><strong>Lain-lain</strong></td><td class="right">{{ number_format($invoice->other,2) }}</td></tr>
    <tr><td class="right"><strong>Total</strong></td><td class="right">{{ number_format($invoice->total,2) }}</td></tr>
  </table>

  <div class="notes">
    <div><strong>Keterangan:</strong> {{ $invoice->description }}</div>
    <div><strong>Note Faktur Pajak:</strong> {{ $invoice->tax_note ?: '-' }}</div>
    <div><strong>Note Invoice / Nota:</strong> {{ $invoice->invoice_note ?: '-' }}</div>
    <div><strong>Kop Surat Lama:</strong> {{ $invoice->use_old_letterhead ? 'YA' : 'TIDAK' }}</div>
    <div><strong>Lunas tanpa posting pembayaran:</strong> {{ $invoice->without_payment_posting ? 'YA' : 'TIDAK' }}</div>
    <div><strong>TT dan Stempel:</strong> {{ $invoice->stamp_and_signature ? 'YA' : 'TIDAK' }}</div>
  </div>
</body>
</html>
