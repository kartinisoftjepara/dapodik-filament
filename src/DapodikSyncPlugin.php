<?php

namespace Sidu\DapodikFilament;

use Filament\Contracts\Plugin;
use Filament\Panel;

/**
 * Plugin Filament untuk SiDU Dapodik Sync.
 *
 * Cara pakai di AppPanelProvider host-app:
 *   ->plugins([
 *       \Sidu\DapodikFilament\DapodikSyncPlugin::make(),
 *   ])
 */
class DapodikSyncPlugin implements Plugin
{
    protected bool $withWidget = true;
    protected bool $withSettingsPage = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'sidu-dapodik-sync';
    }

    // -------------------------------------------------------------------------
    // Fluent Configuration
    // -------------------------------------------------------------------------

    /** Nonaktifkan widget status di dashboard */
    public function withoutWidget(): static
    {
        $this->withWidget = false;
        return $this;
    }

    /** Nonaktifkan halaman settings konfigurasi Dapodik */
    public function withoutSettingsPage(): static
    {
        $this->withSettingsPage = false;
        return $this;
    }

    // -------------------------------------------------------------------------
    // Boot
    // -------------------------------------------------------------------------

    public function register(Panel $panel): void
    {
        $pages   = [];
        $widgets = [];

        if ($this->withWidget) {
            $widgets[] = \Sidu\DapodikFilament\Widgets\DapodikStatusWidget::class;
        }

        if ($this->withSettingsPage) {
            $pages[] = \Sidu\DapodikFilament\Pages\DapodikSettingsPage::class;
        }

        $panel
            ->pages($pages)
            ->widgets($widgets);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
