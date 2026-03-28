<?php

namespace Sidu\DapodikFilament\Widgets;

use Filament\Widgets\Widget;
use Sidu\DapodikSync\Models\DapoSyncLog;
use Sidu\DapodikSync\Models\Staging\DapoSekolah;
use Sidu\DapodikSync\Models\Staging\DapoPtk;
use Sidu\DapodikSync\Models\Staging\DapoSiswa;
use Sidu\DapodikSync\Models\Staging\DapoRombel;
use Sidu\DapodikSync\Jobs\SyncDapodikJob;
use Filament\Notifications\Notification;

class DapodikStatusWidget extends Widget
{
    protected string $view = 'sidu-dapodik-filament::widgets.dapodik-status-widget';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 0;

    // Poll setiap 15 detik untuk update status queue
    protected static ?string $pollingInterval = '15s';

    protected function getViewData(): array
    {
        $lastLog   = DapoSyncLog::latest()->first();
        $lastSync  = $lastLog?->finished_at;

        return [
            'totalSiswa'   => DapoSiswa::count(),
            'totalPtk'     => DapoPtk::count(),
            'totalRombel'  => DapoRombel::count(),
            'namaSekolah'  => DapoSekolah::first()?->nama ?? 'Belum sinkron',
            'lastSync'     => $lastSync ? $lastSync->diffForHumans() : 'Belum pernah',
            'lastStatus'   => $lastLog?->status ?? 'none',
            'logList'      => DapoSyncLog::latest()->take(5)->get(),
        ];
    }

    public function syncAll(): void
    {
        SyncDapodikJob::dispatch(
            ['sekolah', 'ptk', 'siswa', 'rombel', 'pengguna'],
            auth()->id()
        );

        Notification::make()
            ->title('Sinkronisasi Dimulai')
            ->body('Proses sinkronisasi semua data Dapodik berjalan di background. Halaman akan diperbarui otomatis.')
            ->success()
            ->icon('heroicon-o-arrow-path')
            ->send();
    }

    public function syncEntity(string $entity): void
    {
        SyncDapodikJob::dispatch([$entity], auth()->id());

        Notification::make()
            ->title('Sinkronisasi ' . ucfirst($entity) . ' dimulai')
            ->success()
            ->send();
    }
}
