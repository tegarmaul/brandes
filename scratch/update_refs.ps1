Get-ChildItem -Path "resources", "app" -Recurse -Include *.php,*.blade.php | ForEach-Object {
    $c = Get-Content $_.FullName -Raw
    $new = $c -replace "resources/js/c-(admin|shared|user)", "resources/js/ui-component/c-`$1"
    if ($c -ne $new) {
        $new | Set-Content $_.FullName -NoNewline
    }
}
