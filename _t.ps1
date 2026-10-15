param([string]$mode)
$jar = "$env:TEMP\kody_jar_t.txt"
Remove-Item $jar -ErrorAction SilentlyContinue
curl.exe -s -c $jar -b $jar "http://localhost/kody/_as_user.php?id=32&mode=$mode"; ""
function opts($html, $name) {
  $m = [regex]::Match($html, '<select[^>]*name="' + $name + '"[^>]*>([\s\S]*?)</select>')
  if (!$m.Success) { $m = [regex]::Match($html, "<select[^>]*name='" + $name + "'[^>]*>([\s\S]*?)</select>") }
  if (!$m.Success) { return "$name=<none>" }
  $o = [regex]::Matches($m.Groups[1].Value, '<option([^>]*)value=[''"]?(\d+)') | ForEach-Object { $s = if ($_.Groups[1].Value -match 'selected') { '*' } else { '' }; "$($_.Groups[2].Value)$s" }
  "$name=[" + ($o -join ',') + "]"
}
foreach ($u in 'sales.php?q=sale','pos_barcode.php','add_voucher.php?t=recive','pos_time.php') {
  $h = curl.exe -s -c $jar -b $jar "http://localhost/kody/$u"
  $h = $h -join "`n"
  $err = ([regex]::Matches($h, '(Fatal error|Warning</b>|Uncaught)[^<]{0,150}') | % Value) -join ' | '
  "== $u $err"
  (opts $h 'acc2_id'), (opts $h 'store_id'), (opts $h 'fund_id'), (opts $h 'fund_account'), (opts $h 'price_list') -join '  '
}
