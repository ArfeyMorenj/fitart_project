$ErrorActionPreference='Stop'
$path='docs/fitart_api_testing_guide_final.md'
$text=Get-Content -Raw $path

function Fill-Value($key,$value){
  if($null -eq $value){ return $value }
  if($value -is [string]){
    if($value -ne ''){ return $value }
    $k=$key.ToLower()
    if($k -match 'email'){ return 'finance@fitart.co.id' }
    if($k -match 'website|url'){ return 'https://fitart.co.id' }
    if($k -match 'phone|telp|telepon'){ return '021-1234567' }
    if($k -match 'fax'){ return '021-1234568' }
    if($k -match 'npwp'){ return '01.234.567.8-901.000' }
    if($k -match 'npkp|nppkp'){ return 'PKP-001' }
    if($k -match 'city|kota'){ return 'Jakarta' }
    if($k -match 'address|alamat'){ return 'Jl. Jend. Sudirman No. 100, Jakarta' }
    if($k -match 'name|nama'){ return 'PT FitArt Technology' }
    if($k -match 'code|kode'){ return 'CMP001' }
    if($k -match 'position|jabatan'){ return 'Manager Finance' }
    if($k -match 'status'){ return 'active' }
    if($k -match 'period'){ return '2026-03' }
    if($k -match 'date|tanggal'){ return '2026-03-09' }
    if($k -match 'number|nomor'){ return '001' }
    if($k -match 'note|notes|keterangan|description'){ return 'Data pengujian API' }
    return 'SAMPLE'
  }
  if($value -is [System.Management.Automation.PSCustomObject]){
    $h=[ordered]@{}
    foreach($p in $value.PSObject.Properties){ $h[$p.Name] = Fill-Value $p.Name $p.Value }
    return [pscustomobject]$h
  }
  if($value -is [hashtable]){
    $h=[ordered]@{}
    foreach($k in $value.Keys){ $h[$k] = Fill-Value $k $value[$k] }
    return $h
  }
  if($value -is [System.Array]){
    $arr=@()
    foreach($i in $value){ $arr += ,(Fill-Value $key $i) }
    return $arr
  }
  return $value
}

$pattern='Request Body:\s*\r?\n```json\r?\n([\s\S]*?)\r?\n```'
$new=[regex]::Replace($text,$pattern,{ param($m)
  $json=$m.Groups[1].Value.Trim()
  try {
    if($json -eq '' -or $json -eq '{}'){ return $m.Value }
    $obj=$json | ConvertFrom-Json
    $filled=Fill-Value '' $obj
    $out=$filled | ConvertTo-Json -Depth 100
    return ('Request Body:' + "`r`n" + '```json' + "`r`n" + $out + "`r`n" + '```')
  } catch {
    return $m.Value
  }
})
Set-Content -Path $path -Value $new -Encoding UTF8
Write-Output 'UPDATED_REQUEST_BODIES'
