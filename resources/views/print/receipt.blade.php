<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    table { width:100%; border-collapse: collapse; margin-top: 12px;}
    th, td { border:1px solid #333; padding:6px; }
    th { background:#eee; }
    .right { text-align:right; }
  </style>
</head>
<body>
  <h2>Receipt / Bukti Pembayaran</h2>

  <div>
    <strong>No:</strong> {{ $posting->number }}<br>
    <strong>Date:</strong> {{ \Illuminate\Support\Carbon::parse($posting->date)->format('Y-m-d') }}<br>
    <strong>Client:</strong> {{ $posting->client->name ?? '-' }}<br>
    <strong>Bank:</strong> {{ $posting->bank->name ?? '-' }}
  </div>

  <table>
    <thead>
      <tr>
        <th>Invoice</th>
        <th class="right">Amount</th>
        <th class="right">PPN</th>
        <th class="right">Paid</th>
      </tr>
    </thead>
    <tbody>
      @foreach($posting->invoices as $row)
      <tr>
        <td>{{ $row->invoice->number ?? '-' }}</td>
        <td class="right">{{ number_format($row->amount,2) }}</td>
        <td class="right">{{ number_format($row->ppn,2) }}</td>
        <td class="right">{{ number_format($row->amount + $row->ppn,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <h3 class="right">Total Paid: {{ number_format($posting->total_paid,2) }}</h3>
</body>
</html>