<?php
session_start();
include 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $judulFoto = $_POST['judul_foto'];
    $deskripsiFoto = $_POST['deskripsi_foto'];
    $albumID = $_POST['album_id'];
    $userID = $_SESSION["user_id"]; // Mengambil User ID dari session

    // Proses upload file
    $targetDir = "uploads/"; 
    $targetFile = $targetDir . basename($_FILES["foto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar sebenarnya atau bukan
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check === false) {
        echo "File yang diupload bukan gambar.";
        $uploadOk = 0;
    }

    // Cek ukuran file (maksimum 5MB)
    if ($_FILES["foto"]["size"] > 5000000) {
        echo "Maaf, ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Cek format file
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Jika semua cek lulus, upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
            // Masukkan data ke database
            $tanggalUnggah = date('Y-m-d');
            $sql = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) 
                    VALUES ('$judulFoto', '$deskripsiFoto', '$tanggalUnggah', '$targetFile', '$albumID', '$userID')";

            if (mysqli_query($conn, $sql)) {
                // Redirect ke halaman index setelah sukses
                header('Location: viewalbum.php?album=' . htmlspecialchars($albumID));
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Maaf, terjadi kesalahan saat mengupload file.";
        }
    }

    mysqli_close($conn);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FMO.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }

        body {
            font-size: 14px;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #C0C78C;
            padding: 15px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            z-index: 1000;
            position: fixed;
        }

        .navbar .title {
            font-size: 24px;
            color: #fff;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar .nav-links a {
            color: #333;
            text-decoration: none;
            font-size: 18px;
            padding: 8px 15px;
        }

        .navbar .nav-links a:hover {
            color: #2563EB;
        }

        .navbar .profile-icon {
            font-size: 36px;
            color: #fff;
            cursor: pointer;
            margin-right: 20px;
        }

        .navbar .dropdown-menu {
            display: none;
            position: absolute;
            right: 20px;
            top: 60px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
            min-width: 160px;
        }

        .navbar .dropdown-menu a {
            display: block;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            text-align: left;
        }

        .navbar .dropdown-menu a:hover {
            background-color: #f2f6d2;
            color: #ffffff;
        }

        /* Main Styles */
        main {
            margin: 80px auto; /* Adjusted for fixed navbar */
            padding: 20px;
            max-width: 600px;
            background-color: #ffffff; /* White background for the main content */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        }

        h1 {
            font-size: 280px; /* Increased font size for the title */
            color: #2c3e50; /* Darker shade for better contrast */
            margin-bottom: 20px;
            text-align: center; /* Centering the title */
            border-bottom: 2px solid #C0C78C; /* Underline effect for the title */
            padding-bottom: 10px; /* Padding for spacing */
        }

        .form-group {
            margin-bottom: 2px;
            padding: 10px 0; /* Add padding for spacing */
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px; /* Increased padding for better touch target */
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s; /* Smooth transition for border color */
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #C0C78C; /* Highlight border on focus */
            outline: none; /* Remove default outline */
        }

        input[type="submit"] {
            background-color: #C0C78C;
            color: white;
            padding: 12px 20px; /* Increased padding for button */
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s; /* Smooth transitions */
        }

        input[type="submit"]:hover {
            background-color: #A6B37D;
            transform: translateY(-2px); /* Slight lift effect on hover */
        }

        input[type="submit"]:active {
            transform: translateY(0); /* Reset lift effect when clicked */
        }

        /* Footer */
        footer { 
            text-align: center; 
            padding: 10px; 
            color: #333; 
            margin-top: 20px; 
            width: 100%; 
            bottom: 0; 
            left: 0; 
        } 

        footer p { 
            font-size: 15px;  
            margin: 0; 
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .section .col-5, .container .col-4 {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="title">FMO.</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="album.php">Album</a>
            <div class="profile-actions">
                <div class="profile-icon" onclick="toggleDropdown()">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="profile.php">Profil</a>
                    <a href="admin_dashboard.php">Dashboard Admin</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <br><br><main>
        <h1>Tambah Foto</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul_foto">Judul Foto:</label>
                <input type="text" name="judul_foto" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi_foto">Deskripsi Foto:</label>
                <textarea name="deskripsi_foto" required></textarea>
            </div>

            <div class="form-group">
                <label for="album_id">Pilih Album:</label>
                <select name="album_id" required>
                    <?php
                    // Ambil daftar album dari database
                    $sql = "SELECT AlbumID, NamaAlbum FROM album";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['AlbumID']}'>{$row['NamaAlbum']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="foto">Upload Foto:</label>
                <input type="file" name="foto" required>
            </div>
            
            <input type="submit" value="Simpan"> 
        </form>
    </main>

    <footer>
        <p>&copy; 2024 FMO. All rights reserved.</p>
    </footer>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('dropdown-menu');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>
