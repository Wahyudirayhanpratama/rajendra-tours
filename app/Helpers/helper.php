<?php

use Carbon\Carbon;

function formatIndonesianDate($dateString)
{
    Carbon::setLocale('id');

    $date = Carbon::parse($dateString);

    return $date->translatedFormat('l, d F Y');
}

function formatJam($jam)
{
    if (!$jam) return '-';

    try {
        return Carbon::createFromFormat('H:i:s', $jam)->format('H:i');
    } catch (\Exception $e) {
        return $jam; // fallback jika gagal parsing
    }
}

function formatIndonesia($dateString)
{
    Carbon::setLocale('id');

    $date = Carbon::parse($dateString);

    return $date->translatedFormat('F Y');
}

function formatHariTanggalPendek($dateString)
{
    if (!$dateString) return '-';

    try {
        Carbon::setLocale('id');
        return Carbon::parse($dateString)->translatedFormat('D, d');
    } catch (\Exception $e) {
        return $dateString;
    }
}
