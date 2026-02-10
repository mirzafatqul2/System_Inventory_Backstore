<?php

function rupiah($angka)
{
    return "Rp " . number_format($angka, 0, ',', '.');
}

function format_bulan($bulan)
{
    $nama_bulan = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    $parts = explode('-', $bulan);
    return $nama_bulan[$parts[1]] . ' ' . $parts[0];
}
