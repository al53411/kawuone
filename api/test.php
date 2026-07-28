<?php
// Tampilkan informasi PHP & Status Server
echo "<h1> PHP Vercel Berhasil Berjalan!</h1>";
echo "<p>Waktu Server: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>Versi PHP: " . phpversion() . "</p>";

// Tes pembuatan folder /tmp (Simulasi storage Vercel)
$tmpFolder = '/tmp/test_dir';
if (!is_dir($tmpFolder)) {
    mkdir($tmpFolder, 0755, true);
}

if (is_dir($tmpFolder)) {
    echo "<p style='color: green;'> Status Filesystem /tmp: <b>Writable / Berhasil Akses!</b></p>";
} else {
    echo "<p style='color: red;'> Status Filesystem /tmp: <b>Gagal!</b></p>";
}