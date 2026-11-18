<?php

/**
 * Fungsi bantu akademik. Dimuat otomatis lewat BaseController ($helpers).
 * Semua aturan angka diambil dari app/Config/Akademik.php.
 */

if (! function_exists('hitung_nilai_akhir')) {
    /**
     * Nilai akhir = tugas x bobot + UTS x bobot + UAS x bobot.
     * Mengembalikan null bila salah satu komponen belum diisi.
     */
    function hitung_nilai_akhir(?float $tugas, ?float $uts, ?float $uas): ?float
    {
        if ($tugas === null || $uts === null || $uas === null) {
            return null;
        }
        $b = config('Akademik')->bobot;

        return round(($tugas * $b['tugas'] + $uts * $b['uts'] + $uas * $b['uas']) / 100, 2);
    }
}

if (! function_exists('nilai_huruf')) {
    function nilai_huruf(?float $nilaiAkhir): ?string
    {
        if ($nilaiAkhir === null) {
            return null;
        }
        foreach (config('Akademik')->skala as $s) {
            if ($nilaiAkhir >= $s['min']) {
                return $s['huruf'];
            }
        }

        return 'E';
    }
}

if (! function_exists('bobot_huruf')) {
    /** A → 4.0, AB → 3.5, dst. */
    function bobot_huruf(?string $huruf): ?float
    {
        foreach (config('Akademik')->skala as $s) {
            if ($s['huruf'] === $huruf) {
                return $s['bobot'];
            }
        }

        return null;
    }
}

if (! function_exists('nama_hari')) {
    function nama_hari(int $hari): string
    {
        return config('Akademik')->hari[$hari] ?? '-';
    }
}

if (! function_exists('jam')) {
    /** "07:30:00" → "07.30" */
    function jam(?string $time): string
    {
        return $time ? str_replace(':', '.', substr($time, 0, 5)) : '';
    }
}

if (! function_exists('format_ip')) {
    function format_ip(?float $ip): string
    {
        return $ip === null ? '–' : number_format($ip, 2, ',', '');
    }
}

if (! function_exists('kelas_huruf')) {
    /** Nama class CSS untuk mewarnai huruf nilai. */
    function kelas_huruf(?string $huruf): string
    {
        return match ($huruf) {
            'A', 'AB'      => 'grade grade-a',
            'B', 'BC'      => 'grade grade-b',
            'C'            => 'grade grade-c',
            'D', 'E'       => 'grade grade-d',
            default        => 'grade',
        };
    }
}

if (! function_exists('jadwal_bentrok')) {
    /**
     * Dua jadwal bentrok bila harinya sama dan rentang jamnya beririsan:
     * mulaiA < selesaiB  DAN  mulaiB < selesaiA
     */
    function jadwal_bentrok(int $hariA, string $mulaiA, string $selesaiA, int $hariB, string $mulaiB, string $selesaiB): bool
    {
        if ($hariA !== $hariB) {
            return false;
        }

        return substr($mulaiA, 0, 5) < substr($selesaiB, 0, 5)
            && substr($mulaiB, 0, 5) < substr($selesaiA, 0, 5);
    }
}
