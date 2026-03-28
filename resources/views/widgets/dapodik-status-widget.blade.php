<x-filament-widgets::widget>
    <style>
        .dapo-widget { padding: 0; }
        .dapo-header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            border-radius: 16px 16px 0 0;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        .dapo-header-left h2 {
            color: white;
            font-size: 1.1rem;
            font-weight: 800;
            margin: 0;
        }
        .dapo-header-left p {
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
            margin: 2px 0 0;
        }
        .dapo-header-right { display: flex; gap: 10px; flex-wrap: wrap; }
        .dapo-btn {
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        .dapo-btn-white {
            background: white;
            color: #1e40af;
        }
        .dapo-btn-white:hover { background: #eff6ff; transform: translateY(-1px); }
        .dapo-btn-outline {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.4);
        }
        .dapo-btn-outline:hover { background: rgba(255,255,255,0.25); }
        .dapo-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            padding: 20px 25px;
            background: var(--fi-gray-50, #f8fafc);
        }
        .dapo-stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px;
            text-align: center;
        }
        .dapo-stat-card .stat-num {
            font-size: 1.8rem;
            font-weight: 900;
            color: #1e40af;
            line-height: 1;
        }
        .dapo-stat-card .stat-label {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 600;
            margin-top: 5px;
        }
        .dapo-entity-btns {
            padding: 0 25px 8px;
            background: var(--fi-gray-50, #f8fafc);
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .dapo-entity-btn {
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            transition: all 0.2s;
        }
        .dapo-entity-btn:hover {
            border-color: #3b82f6;
            color: #1e40af;
            background: #eff6ff;
        }
        .dapo-log-table {
            padding: 15px 25px 20px;
        }
        .dapo-log-table table { width: 100%; border-collapse: collapse; font-size: 0.8rem; }
        .dapo-log-table th {
            text-align: left;
            padding: 6px 10px;
            color: #6b7280;
            font-weight: 700;
            border-bottom: 1px solid #e5e7eb;
        }
        .dapo-log-table td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; }
        .badge-success { background: #dcfce7; color: #166534; padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.7rem; }
        .badge-failed { background: #fee2e2; color: #991b1b; padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.7rem; }
        .badge-pending { background: #fef3c7; color: #92400e; padding: 2px 10px; border-radius: 20px; font-weight: 700; font-size: 0.7rem; }
    </style>

    <div class="dapo-widget">
        {{-- Header --}}
        <div class="dapo-header">
            <div class="dapo-header-left">
                <h2><i class="fas fa-sync-alt" style="margin-right:8px;"></i>Data Dapodik · {{ $namaSekolah }}</h2>
                <p>Sinkronisasi terakhir: <strong>{{ $lastSync }}</strong></p>
            </div>
            <div class="dapo-header-right">
                <button class="dapo-btn dapo-btn-white" wire:click="syncAll">
                    <i class="fas fa-sync-alt"></i> Sinkronkan Semua
                </button>
                <a href="{{ \Sidu\DapodikFilament\Pages\DapodikSettingsPage::getUrl() }}"
                   class="dapo-btn dapo-btn-outline">
                    <i class="fas fa-cog"></i> Konfigurasi
                </a>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="dapo-stats">
            <div class="dapo-stat-card">
                <div class="stat-num">{{ number_format($totalSiswa) }}</div>
                <div class="stat-label">Peserta Didik</div>
            </div>
            <div class="dapo-stat-card">
                <div class="stat-num">{{ number_format($totalPtk) }}</div>
                <div class="stat-label">PTK</div>
            </div>
            <div class="dapo-stat-card">
                <div class="stat-num">{{ number_format($totalRombel) }}</div>
                <div class="stat-label">Rombongan Belajar</div>
            </div>
            <div class="dapo-stat-card">
                <div class="stat-num" style="color: {{ $lastStatus === 'success' ? '#16a34a' : ($lastStatus === 'failed' ? '#dc2626' : '#d97706') }}; font-size: 1rem;">
                    {{ strtoupper($lastStatus === 'none' ? 'BELUM SYNC' : $lastStatus) }}
                </div>
                <div class="stat-label">Status Terakhir</div>
            </div>
        </div>

        {{-- Tombol Sync Per Entitas --}}
        <div class="dapo-entity-btns">
            @foreach(['sekolah', 'ptk', 'siswa', 'rombel', 'pengguna'] as $entity)
                <button class="dapo-entity-btn" wire:click="syncEntity('{{ $entity }}')">
                    <i class="fas fa-sync-alt" style="font-size: 0.7rem;"></i>
                    Sync {{ ucfirst($entity) }}
                </button>
            @endforeach
        </div>

        {{-- Tabel Log --}}
        <div class="dapo-log-table">
            <p style="font-size: 0.8rem; font-weight: 700; color: #374151; margin: 0 0 8px;">Riwayat Sinkronisasi Terbaru</p>
            <table>
                <thead>
                    <tr>
                        <th>Entitas</th>
                        <th>Status</th>
                        <th>Record</th>
                        <th>Durasi</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logList as $log)
                        <tr>
                            <td style="font-weight: 600;">{{ ucfirst($log->entity) }}</td>
                            <td>
                                <span class="badge-{{ $log->status }}">{{ strtoupper($log->status) }}</span>
                            </td>
                            <td>{{ number_format($log->record_count) }}</td>
                            <td>{{ $log->duration_seconds }}s</td>
                            <td>{{ $log->finished_at?->format('d/m H:i') ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; color: #9ca3af; padding: 20px;">
                                Belum ada riwayat sinkronisasi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-widgets::widget>
