<?php

/**
 * Fungsi bantu tampilan.
 */

if (! function_exists('current_user')) {
    function current_user(?string $key = null): mixed
    {
        $user = session('user');

        return $key === null ? $user : ($user[$key] ?? null);
    }
}

if (! function_exists('icon')) {
    /** Ikon dari SVG sprite di layouts/app.php → <svg><use href="#i-nama"></svg> */
    function icon(string $name, string $class = ''): string
    {
        return '<svg class="icon ' . esc($class, 'attr') . '" aria-hidden="true"><use href="#i-' . esc($name, 'attr') . '"></use></svg>';
    }
}

if (! function_exists('menu_active')) {
    /** "is-active" bila URL sekarang berada di bawah $path */
    function menu_active(string $path): string
    {
        $uri = trim(uri_string(), '/');
        $path = trim($path, '/');

        return ($uri === $path || str_starts_with($uri, $path . '/')) ? 'is-active' : '';
    }
}

if (! function_exists('field_value')) {
    /** Nilai untuk input form: input lama (setelah gagal validasi) atau data dari database. */
    function field_value(string $field, ?array $row = null, mixed $default = ''): string
    {
        return (string) old($field, $row[$field] ?? $default);
    }
}

if (! function_exists('field_error')) {
    /** Pesan validasi untuk satu field (dikirim lewat ->withInput()). */
    function field_error(string $field): string
    {
        $errors = session('_ci_validation_errors') ?? [];
        $msg = is_array($errors) ? ($errors[$field] ?? null) : null;

        return $msg ? '<p class="field-error">' . esc($msg) . '</p>' : '';
    }
}

if (! function_exists('has_error')) {
    function has_error(string $field): string
    {
        $errors = session('_ci_validation_errors') ?? [];

        return isset($errors[$field]) ? 'has-error' : '';
    }
}

if (! function_exists('role_label')) {
    function role_label(?string $role): string
    {
        return ['admin' => 'Administrator', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'][$role] ?? '';
    }
}

if (! function_exists('inisial')) {
    function inisial(string $nama): string
    {
        $nama = preg_replace('/^(Dr|Prof|Ir)\.\s*/i', '', $nama);
        $nama = trim(explode(',', $nama)[0]);
        $kata = preg_split('/\s+/', $nama) ?: [''];
        $hasil = mb_substr($kata[0], 0, 1) . (count($kata) > 1 ? mb_substr(end($kata), 0, 1) : '');

        return mb_strtoupper($hasil);
    }
}

if (! function_exists('tanggal_id')) {
    function tanggal_id(?string $tanggal = null): string
    {
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $hari = [1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $t = $tanggal ? strtotime($tanggal) : time();

        return $hari[(int) date('N', $t)] . ', ' . date('j', $t) . ' ' . $bulan[(int) date('n', $t)] . ' ' . date('Y', $t);
    }
}

if (! function_exists('field_row')) {
    /**
     * Satu baris form bergaya Settings iOS: label di kiri, kontrol di kanan, pesan error di bawah.
     * $control adalah HTML input/select yang sudah jadi.
     */
    function field_row(string $name, string $label, string $control): string
    {
        return '<div class="row stack ' . has_error($name) . '">'
            . '<div class="field"><label for="' . esc($name, 'attr') . '">' . esc($label) . '</label>' . $control . '</div>'
            . field_error($name)
            . '</div>';
    }
}

if (! function_exists('select_options')) {
    /** <option> dari array [value => label] dengan nilai terpilih. */
    function select_options(array $options, mixed $selected, ?string $placeholder = null): string
    {
        $html = $placeholder !== null ? '<option value="">' . esc($placeholder) . '</option>' : '';
        foreach ($options as $value => $label) {
            $sel = (string) $value === (string) $selected ? ' selected' : '';
            $html .= '<option value="' . esc((string) $value, 'attr') . '"' . $sel . '>' . esc($label) . '</option>';
        }

        return $html;
    }
}
