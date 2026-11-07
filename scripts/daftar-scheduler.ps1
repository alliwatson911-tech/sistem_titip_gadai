# Mendaftarkan Task Scheduler Windows agar `php artisan schedule:run` berjalan setiap menit.
# Jalankan sekali di PowerShell (Run as Administrator). Hapus dengan: Unregister-ScheduledTask -TaskName "linygadai-scheduler" -Confirm:$false
# Ubah $php dan $proyek bila lokasinya berbeda.

$php    = "C:\XAMPP\php\php.exe"
$proyek = "C:\XAMPP\htdocs\linygadai"

$aksi    = New-ScheduledTaskAction -Execute $php -Argument "artisan schedule:run" -WorkingDirectory $proyek
$pemicu  = New-ScheduledTaskTrigger -Once -At (Get-Date) -RepetitionInterval (New-TimeSpan -Minutes 1)
$setelan = New-ScheduledTaskSettingsSet -StartWhenAvailable -MultipleInstances IgnoreNew -ExecutionTimeLimit (New-TimeSpan -Minutes 10)

Register-ScheduledTask -TaskName "linygadai-scheduler" -Action $aksi -Trigger $pemicu -Settings $setelan -Description "linygadai: kedaluwarsa pengajuan, status gadai, pengingat email, cadangan" -Force
Write-Host "Terdaftar. Periksa dengan: C:\XAMPP\php\php.exe artisan sistem:periksa"
