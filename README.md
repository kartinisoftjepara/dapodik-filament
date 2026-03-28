# README — sidu/dapodik-filament

**Plugin Filament untuk `sidu/dapodik-sync`.**
Menyediakan Dashboard Widget, Halaman Konfigurasi, dan Tombol Sinkronisasi berbasis UI siap pakai.

> Paket ini adalah addon opsional. Paket inti adalah `sidu/dapodik-sync`.

---

## Prasyarat

```bash
composer require sidu/dapodik-sync
composer require sidu/dapodik-filament
```

---

## Instalasi di AppPanelProvider

```php
use Sidu\DapodikFilament\DapodikSyncPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            DapodikSyncPlugin::make(),

            // Opsional: nonaktifkan widget atau halaman settings
            // DapodikSyncPlugin::make()->withoutWidget(),
            // DapodikSyncPlugin::make()->withoutSettingsPage(),
        ]);
}
```

---

## Fitur yang Tersedia

### 1. Widget Dashboard (`DapodikStatusWidget`)
Otomatis muncul di halaman Dashboard Filament. Menampilkan:
- Statistik jumlah Siswa, PTK, dan Rombel
- Status dan waktu sinkronisasi terakhir
- Tombol **"Sinkronkan Semua"** dan tombol sync per entitas
- Tabel riwayat 5 sinkronisasi terbaru

Widget ini melakukan **polling otomatis setiap 15 detik** untuk memperbarui status.

### 2. Halaman Konfigurasi (`DapodikSettingsPage`)
Dapat diakses dari navigasi **Pengaturan → Konfigurasi Dapodik**. Menyediakan:
- Form input URL server, Token API, dan NPSN
- Tombol **"Uji Koneksi"** — tes real-time ke endpoint Dapodik
- Tombol **"Simpan Konfigurasi"** — menulis ke file `.env` otomatis
- Tombol **"Mulai Sinkronisasi Sekarang"** — dispatch job ke queue
- Panduan konfigurasi manual via `.env`

---

## Contoh Dispatch Job dari Kode Sendiri

Untuk tombol kustom di Resource/Page lain:
```php
use Sidu\DapodikSync\Jobs\SyncDapodikJob;

// Di dalam action tombol Filament:
->action(function () {
    SyncDapodikJob::dispatch(['siswa'], auth()->id());
})
```

---

## Menangkap Notifikasi Selesai Sync

```php
// app/Listeners/NotifyAdminSyncDone.php
use Sidu\DapodikSync\Events\DapodikSyncCompleted;
use Filament\Notifications\Notification;

class NotifyAdminSyncDone {
    public function handle(DapodikSyncCompleted $event): void {
        $user = auth()->user();
        
        Notification::make()
            ->title('Dapodik Sync Selesai')
            ->body('Durasi: ' . $event->durationInSeconds() . 's · ' . ($event->hasErrors ? 'Ada kegagalan.' : 'Semua berhasil.'))
            ->color($event->hasErrors ? 'danger' : 'success')
            ->sendToDatabase($user);
    }
}
```
