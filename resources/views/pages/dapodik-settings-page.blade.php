<x-filament-panels::page>
    <style>
        .daposet-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 20px;
        }
        .daposet-card h3 {
            font-size: 0.95rem;
            font-weight: 800;
            color: #111827;
            margin: 0 0 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .daposet-card p.desc {
            font-size: 0.8rem;
            color: #6b7280;
            margin: 0 0 20px;
        }
        .daposet-field { margin-bottom: 16px; }
        .daposet-field label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }
        .daposet-field input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 0.875rem;
            outline: none;
            box-sizing: border-box;
            font-family: monospace;
            transition: border-color 0.2s;
        }
        .daposet-field input:focus { border-color: #3b82f6; }
        .daposet-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 20px; }
        .btn-primary {
            padding: 10px 22px;
            background: #1e40af;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .btn-primary:hover { background: #1d3a9e; transform: translateY(-1px); }
        .btn-secondary {
            padding: 10px 22px;
            background: white;
            color: #374151;
            border: 1.5px solid #d1d5db;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .btn-secondary:hover { border-color: #3b82f6; color: #1e40af; }
        .btn-success {
            padding: 10px 22px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }
        .test-result {
            margin-top: 14px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .test-result.success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .test-result.failed  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-info {
            background: #eff6ff;
            border: 1px solid #93c5fd;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.8rem;
            color: #1e40af;
        }
    </style>

    <div style="max-width: 700px;">

        {{-- Card Konfigurasi Koneksi --}}
        <div class="daposet-card">
            <h3>
                <i class="fas fa-plug" style="color: #3b82f6;"></i>
                Konfigurasi Koneksi Dapodik
            </h3>
            <p class="desc">Masukkan alamat server, token akses, dan NPSN sekolah Anda. Data ini akan disimpan ke file .env aplikasi.</p>

            <div class="daposet-field">
                <label>URL Server Dapodik</label>
                <input type="text" wire:model="base_url" placeholder="http://172.20.1.252:5774" />
            </div>
            <div class="daposet-field">
                <label>Token API (Bearer)</label>
                <input type="password" wire:model="token" placeholder="Masukkan token Dapodik..." />
            </div>
            <div class="daposet-field">
                <label>NPSN Sekolah</label>
                <input type="text" wire:model="npsn" placeholder="20315959" />
            </div>

            <div class="daposet-actions">
                <button class="btn-primary" wire:click="simpanKonfigurasi">
                    <i class="fas fa-save"></i> Simpan Konfigurasi
                </button>
                <button class="btn-secondary" wire:click="testConeksi">
                    <i class="fas fa-wifi"></i> Uji Koneksi
                </button>
            </div>

            @if($testResult)
                <div class="test-result {{ $testSuccess ? 'success' : 'failed' }}">
                    <i class="fas fa-{{ $testSuccess ? 'check-circle' : 'times-circle' }}"></i>
                    {{ $testResult }}
                </div>
            @endif
        </div>

        {{-- Card Sinkronisasi Manual --}}
        <div class="daposet-card">
            <h3>
                <i class="fas fa-sync-alt" style="color: #16a34a;"></i>
                Sinkronisasi Manual
            </h3>
            <p class="desc">Klik tombol di bawah ini untuk memulai sinkronisasi semua data (Sekolah, PTK, Siswa, Rombel, dan Pengguna) dari server Dapodik sekarang.</p>

            <div class="alert-info" style="margin-bottom: 16px;">
                <i class="fas fa-info-circle"></i>
                Proses sinkronisasi berjalan di <strong>background</strong>. Anda bisa menutup halaman ini dan melihat hasilnya di widget Dashboard.
            </div>

            <button class="btn-success" wire:click="syncSekarang">
                <i class="fas fa-play-circle"></i> Mulai Sinkronisasi Sekarang
            </button>
        </div>

        {{-- Panduan .env --}}
        <div class="daposet-card" style="border-style: dashed;">
            <h3>
                <i class="fas fa-terminal" style="color: #6b7280;"></i>
                Konfigurasi via .env (Alternatif)
            </h3>
            <p class="desc">Anda juga bisa mengisi konfigurasi langsung di file <code>.env</code> proyek Anda:</p>
            <pre style="background:#f1f5f9; padding:14px; border-radius:10px; font-size:0.8rem; overflow-x:auto;">DAPODIK_URL={{ $base_url ?: 'http://ip-server:5774' }}
DAPODIK_TOKEN=your-bearer-token-here
DAPODIK_NPSN={{ $npsn ?: 'npsn-sekolah-anda' }}</pre>
        </div>

    </div>
</x-filament-panels::page>
