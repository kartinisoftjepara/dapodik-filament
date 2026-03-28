<?php

namespace Sidu\DapodikFilament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Sidu\DapodikSync\Services\DapodikService;
use Sidu\DapodikSync\Jobs\SyncDapodikJob;

class DapodikSettingsPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-circle-stack';
    protected static ?string $navigationLabel = 'Konfigurasi Dapodik';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?int    $navigationSort  = 90;
    protected static string  $view = 'sidu-dapodik-filament::pages.dapodik-settings-page';

    // State form
    public string  $base_url  = '';
    public string  $token     = '';
    public string  $npsn      = '';
    public ?string $testResult = null;
    public bool    $testSuccess = false;

    public function mount(): void
    {
        $this->base_url = config('dapodik.api.base_url', '');
        $this->token    = config('dapodik.api.token', '');
        $this->npsn     = config('dapodik.api.npsn', '');
    }

    public function testConeksi(DapodikService $service): void
    {
        $result = $service->testConnection($this->base_url, $this->token, $this->npsn);

        $this->testSuccess = $result['success'];
        $this->testResult  = $result['message'];

        Notification::make()
            ->title($result['success'] ? 'Koneksi Berhasil!' : 'Koneksi Gagal')
            ->body($result['message'])
            ->color($result['success'] ? 'success' : 'danger')
            ->send();
    }

    public function simpanKonfigurasi(): void
    {
        // Tulis ke .env atau config cache
        $this->updateEnv([
            'DAPODIK_URL'   => $this->base_url,
            'DAPODIK_TOKEN' => $this->token,
            'DAPODIK_NPSN'  => $this->npsn,
        ]);

        Notification::make()
            ->title('Konfigurasi Tersimpan')
            ->body('Nilai berhasil diperbarui di file .env')
            ->success()
            ->send();
    }

    public function syncSekarang(): void
    {
        SyncDapodikJob::dispatch(
            ['sekolah', 'ptk', 'siswa', 'rombel', 'pengguna'],
            auth()->id()
        );

        Notification::make()
            ->title('Sinkronisasi Dimulai')
            ->body('Job sync berjalan di background queue. Cek widget dashboard untuk status.')
            ->success()
            ->icon('heroicon-o-arrow-path')
            ->send();
    }

    /**
     * Helper: update nilai di file .env
     */
    protected function updateEnv(array $data): void
    {
        $envPath = base_path('.env');
        $content = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $value = str_contains($value, ' ') ? "\"{$value}\"" : $value;

            if (preg_match("/^{$key}=/m", $content)) {
                $content = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $content);
            } else {
                $content .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $content);
        \Illuminate\Support\Facades\Artisan::call('config:clear');
    }
}
