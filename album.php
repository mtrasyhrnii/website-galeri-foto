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
    $namaAlbum = $_POST['nama_album'];
    $deskripsiAlbum = $_POST['deskripsi_album'];
    $userID = $_SESSION["user_id"]; // Mengambil User ID dari session

    // Set tanggal dibuat
    $tanggalDibuat = date('Y-m-d');

    // Masukkan data ke database
    $sql = "INSERT INTO album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) 
            VALUES ('$namaAlbum', '$deskripsiAlbum', '$tanggalDibuat', '$userID')";

    if (mysqli_query($conn, $sql)) {
        // Redirect ke halaman daftar album setelah sukses
        header('Location: album.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Album</title>
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
            background-color: #ffff;
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
            top: 0;
            left: 0;
        }

        .navbar .title {
            font-size: 24px;
            color: #fff;
            font-weight: bold;
            text-transform: uppercase;
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
            color: #A6B37D;
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

        .container {
            margin: 20px auto;
            padding: 20px;
            max-width: 600px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            font-size: 28px;
            color: #2c3e50;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input[type="submit"] {
            background-color: #C0C78C;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        input[type="submit"]:hover {
            background-color: #A6B37D;
            transform: translateY(-2px);
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0; 
            font-size: 14px; 
            background-color: white;
            border: 1px solid #ddd; 
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
        }

        table th {
            background-color: #A6B37D;
            color: white;
            position: sticky; 
            top: 0;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            z-index: 1;
            text-align: center;
        }

        table td {
            border-bottom: 1px solid #ddd;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        footer { 
            text-align: center; 
            padding: 10px; 
            background-color: #fff; 
            color: #333; 
            margin-top: 25px; 
            width: 100%; 
            bottom: 0; 
            left: 0; 
        } 

        footer p { 
            font-size: 15px;  
            margin: 0; 
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

    <br><br><div class="container">
        <h1>Tambah Album</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nama_album">Nama Album:</label>
                <input type="text" name="nama_album" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi_album">Deskripsi Album:</label>
                <textarea name="deskripsi_album" required></textarea>
            </div>
            
            <input type="submit" value="Simpan Album">
        </form>
    </div>

    <!-- Wrapper Container for Table -->
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Album</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Dibuat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'config.php';
                
                // Query untuk mengambil data album
                $query = "SELECT * FROM album ORDER BY TanggalDibuat ASC";
                $result = mysqli_query($conn, $query);
                $no = 1;

                // Menampilkan data album
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                        <td>{$no}</td>
                        <td>{$row['NamaAlbum']}</td>
                        <td>{$row['Deskripsi']}</td>
                        <td>{$row['TanggalDibuat']}</td>
                        <td>
                            <a href='albumedit.php?id={$row['AlbumID']}' class='action-button edit-button'>Edit</a>
                        </td>
                    </tr>";
                    $no++;
                }
                ?>
            </tbody>
        </table>
    </div>

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
