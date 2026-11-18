<?php

/**
 * Pesan validasi Bahasa Indonesia.
 * Aktif karena App::$defaultLocale = 'id'. Aturan yang tidak ada di sini
 * memakai pesan bawaan (Bahasa Inggris).
 */
return [
    'required'              => '{field} wajib diisi.',
    'min_length'            => '{field} minimal {param} karakter.',
    'max_length'            => '{field} maksimal {param} karakter.',
    'exact_length'          => '{field} harus {param} karakter.',
    'is_unique'             => '{field} sudah digunakan.',
    'is_not_unique'         => '{field} tidak ditemukan.',
    'valid_email'           => '{field} harus berupa alamat email yang valid.',
    'integer'               => '{field} harus berupa bilangan bulat.',
    'numeric'               => '{field} harus berupa angka.',
    'decimal'               => '{field} harus berupa angka.',
    'is_natural'            => '{field} harus berupa angka positif.',
    'is_natural_no_zero'    => '{field} harus lebih dari nol.',
    'in_list'               => '{field} harus salah satu dari: {param}.',
    'matches'               => '{field} tidak sama dengan {param}.',
    'alpha_numeric'         => '{field} hanya boleh berisi huruf dan angka.',
    'alpha_numeric_punct'   => '{field} berisi karakter yang tidak diizinkan.',
    'greater_than'          => '{field} harus lebih besar dari {param}.',
    'greater_than_equal_to' => '{field} minimal {param}.',
    'less_than'             => '{field} harus lebih kecil dari {param}.',
    'less_than_equal_to'    => '{field} maksimal {param}.',
    'regex_match'           => 'Format {field} tidak sesuai.',
    'valid_date'            => '{field} harus berupa tanggal/jam yang valid.',
];
