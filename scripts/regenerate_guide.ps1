$ErrorActionPreference='Stop'
$colPath='FitArt_JPAS_API_Complete.postman_collection.json'
$guidePath='docs/fitart_api_testing_guide_final.md'
$col=Get-Content -Raw $colPath | ConvertFrom-Json
function FlattenFG($folder,$group,$items){$out=New-Object System.Collections.Generic.List[object];foreach($it in $items){if($it.item){if($folder -eq ''){$sub=FlattenFG $it.name '' $it.item}elseif($group -eq ''){$sub=FlattenFG $folder $it.name $it.item}else{$sub=FlattenFG $folder $group $it.item};foreach($s in $sub){$out.Add($s)}}elseif($it.request){$out.Add([pscustomobject]@{folder=$folder;group=$group;req=$it})}};return $out}
$all=FlattenFG '' '' $col.item
function RawUrl($u){if($u -is [string]){return $u};if($u.raw){return [string]$u.raw};return ''}
function PathFromRaw($raw){$p=$raw -replace '^\{\{base_url\}\}','' -replace '^https?://[^/]+','';if($p -notmatch '^/'){$p='/' + $p};return $p}
function Desc($method,$path){$base='';if($path -match '^/api/([^/?]+)'){$base=$Matches[1]};switch($method){'GET'{"Ambil data endpoint $base."};'POST'{"Buat/proses data pada endpoint $base."};'PUT'{"Update penuh data pada endpoint $base."};'PATCH'{"Update parsial data pada endpoint $base."};'DELETE'{"Hapus data pada endpoint $base."};default{'Eksekusi endpoint API.'}}}
$sb=New-Object System.Text.StringBuilder
$null=$sb.AppendLine('# FitArt API Testing Guide - FINAL COMPLETE (Single File)')
$null=$sb.AppendLine('')
$null=$sb.AppendLine('Dokumen ini disusun ulang total agar testing bisa dari satu file dan sinkron 1:1 dengan collection FitArt_JPAS_API_Complete.postman_collection.json.')
$null=$sb.AppendLine('')
$null=$sb.AppendLine('## 0) Setup Umum')
$null=$sb.AppendLine('- Base URL: http://localhost:8000')
$null=$sb.AppendLine('- Header private endpoint:')
$null=$sb.AppendLine('- Accept: application/json')
$null=$sb.AppendLine('- Content-Type: application/json (POST/PUT/PATCH)')
$null=$sb.AppendLine('- Authorization: Bearer {{token}}')
$null=$sb.AppendLine('')
$null=$sb.AppendLine('## 1) Urutan Jalankan di Postman (WAJIB)')
$null=$sb.AppendLine('1. POST /api/login')
$null=$sb.AppendLine('2. GET /api/me')
$null=$sb.AppendLine('3. Lookup: chart-of-accounts, team-members, product-groups, items, banks, invoice-types, master-item-products, clients')
$null=$sb.AppendLine('4. Setup: company/settings dan tax-series')
$null=$sb.AppendLine('5. Master CRUD: companies, clients, team-members, product-groups, products, items, master-item-products, banks, invoice-types, users')
$null=$sb.AppendLine('6. Sales: work-orders -> assign-team -> installations -> licenses -> stop-licenses')
$null=$sb.AppendLine('7. Finance: invoices -> invoice-items -> debit-credit-notes -> payments')
$null=$sb.AppendLine('8. Posting: posting/omzet -> posting/payments -> posting/payments/{postingPayment}/costs')
$null=$sb.AppendLine('9. Receivable: receivables -> receivables/process')
$null=$sb.AppendLine('10. Reports: semua /api/reports/*')
$null=$sb.AppendLine('11. Print: /api/print/invoices/batch, /api/print/invoices/{invoice}/pdf, /api/print/receipts/{posting_payment}/pdf')
$null=$sb.AppendLine('')
$null=$sb.AppendLine('## 2) Struktur Folder Collection')
foreach($f in $col.item){$null=$sb.AppendLine('- ' + $f.name)}
$null=$sb.AppendLine('')
$null=$sb.AppendLine('## 3) Step-by-Step Endpoint Lengkap (Request + Response + Penjelasan)')
$null=$sb.AppendLine('Total endpoint: ' + $all.Count)
$folderNames=@($col.item | ForEach-Object {$_.name})
foreach($folder in $folderNames){$null=$sb.AppendLine('');$null=$sb.AppendLine('### ' + $folder);$fEntries=@($all | Where-Object {$_.folder -eq $folder});$groups=@($fEntries.group | Sort-Object -Unique);foreach($g in $groups){$null=$sb.AppendLine('');$null=$sb.AppendLine('#### ' + $g);$entries=@($fEntries | Where-Object {$_.group -eq $g} | Sort-Object {$_.req.request.method},{RawUrl $_.req.request.url});foreach($e in $entries){$req=$e.req.request;$method=$req.method.ToUpper();$raw=RawUrl $req.url;$path=PathFromRaw $raw;$null=$sb.AppendLine('');$null=$sb.AppendLine('##### ' + $method + ' ' + $path);$null=$sb.AppendLine('Deskripsi: ' + (Desc $method $path));if(@('POST','PUT','PATCH') -contains $method){$body='{}';if($req.body -and $req.body.raw){$body=[string]$req.body.raw};$null=$sb.AppendLine('Request Body:');$null=$sb.AppendLine('```json');$null=$sb.AppendLine($body);$null=$sb.AppendLine('```')}else{$null=$sb.AppendLine('Request Body: none')};$succ='{"success": true, "data": []}';if($e.req.response){$s=@($e.req.response | Where-Object {$_.name -eq 'Success'} | Select-Object -First 1);if($s.Count -gt 0 -and $s[0].body){$succ=[string]$s[0].body}};$null=$sb.AppendLine('Response Sukses (contoh):');$null=$sb.AppendLine('```json');$null=$sb.AppendLine($succ);$null=$sb.AppendLine('```');$null=$sb.AppendLine('Response Error Validasi (contoh 422):');$null=$sb.AppendLine('```json');$null=$sb.AppendLine('{');$null=$sb.AppendLine('  "message": "The given data was invalid.",');$null=$sb.AppendLine('  "errors": {');$null=$sb.AppendLine('    "field": ["validation message"]');$null=$sb.AppendLine('  }');$null=$sb.AppendLine('}');$null=$sb.AppendLine('```')}}}
$null=$sb.AppendLine('')
$null=$sb.AppendLine('## 4) Error Matrix')
$null=$sb.AppendLine('- 401: token invalid / belum login')
$null=$sb.AppendLine('- 403: role tidak diizinkan')
$null=$sb.AppendLine('- 404: resource tidak ditemukan')
$null=$sb.AppendLine('- 409: konflik data')
$null=$sb.AppendLine('- 422: validasi input gagal')
$null=$sb.AppendLine('- 423: period terkunci')
$null=$sb.AppendLine('')
$null=$sb.AppendLine('## 5) Checklist Eksekusi Per Endpoint')
$null=$sb.AppendLine('1. Jalankan request sukses.')
$null=$sb.AppendLine('2. Jalankan 1 skenario validasi error (422).')
$null=$sb.AppendLine('3. Save response di Postman.')
$null=$sb.AppendLine('4. Lanjut endpoint berikutnya sesuai urutan folder.')
$sb.ToString() | Set-Content -Path $guidePath -Encoding UTF8
Write-Output ('GUIDE_OK endpoints=' + $all.Count)
