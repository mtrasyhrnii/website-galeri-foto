<?php
session_start();
require_once("config.php");

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Mendapatkan FotoID dan AlbumID dari form
$fotoId = filter_input(INPUT_POST, 'foto_id', FILTER_SANITIZE_NUMBER_INT);
$albumId = filter_input(INPUT_POST, 'album_id', FILTER_SANITIZE_NUMBER_INT);
$komentar = filter_input(INPUT_POST, 'komentar', FILTER_SANITIZE_STRING); // Ambil komentar dari form

// Dapatkan UserID dari sesi
$userId = $_SESSION["user_id"];

// Masukkan komentar ke dalam database
$sql_comment = "INSERT INTO komentarfoto (FotoID, UserID, IsiKomentar, TanggalKomentar) VALUES (?, ?, ?, NOW())";
$stmt_comment = $conn->prepare($sql_comment);
$stmt_comment->bind_param("iis", $fotoId, $userId, $komentar); // "iis" berarti integer, integer, string

if ($stmt_comment->execute()) {
    // Jika sukses, redirect kembali ke halaman album
    header("Location: viewalbum.php?album=$albumId");
    exit;
} else {
    // Jika gagal, tampilkan pesan error
    echo "Terjadi kesalahan saat menambahkan komentar.";
}
?>
