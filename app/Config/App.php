<?php
namespace App\Config;

class App
{
    public const APP_NAME = 'Yunda Fashion';
    public const APP_TAGLINE = 'POS & Digital Twin Inventory System';
    public const APP_VERSION = '1.0';

    public static function getBaseUrl(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Deteksi subpath folder project
        $scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
        // Jika scriptName berakhir dengan /public, arahkan base url ke root project atau ke scriptName
        // Agar konsisten, kita gunakan path base project:
        $base = rtrim($scriptName, '/');
        // Bersihkan '/public' jika masuk dari public folder
        if (str_ends_with($base, '/public')) {
            $base = substr($base, 0, -7);
        }
        
        return $protocol . $host . $base;
    }

    public static function formatRupiah(float|int $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public static function formatTanggal(string $datetime): string
    {
        if (empty($datetime)) return '-';
        $timestamp = strtotime($datetime);
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];
        $tgl = date('d', $timestamp);
        $bln = $bulan[(int)date('m', $timestamp)];
        $thn = date('Y', $timestamp);
        $jam = date('H:i', $timestamp);
        return "$tgl $bln $thn, $jam WIB";
    }

    public static function formatTanggalPendek(string $datetime): string
    {
        if (empty($datetime)) return '-';
        return date('d/m/Y H:i', strtotime($datetime));
    }
}
