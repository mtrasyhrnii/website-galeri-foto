<?php
session_start();
include 'config.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ID album diberikan
if (!isset($_GET['id'])) {
    header('Location: album.php');
    exit();
}

// Ambil ID album dari query string
$albumID = $_GET['id'];

// Ambil data album berdasarkan ID
$sql = "SELECT * FROM album WHERE AlbumID = '$albumID'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    echo "Album tidak ditemukan.";
    exit();
}

$album = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $namaAlbum = $_POST['nama_album'];
    $deskripsiAlbum = $_POST['deskripsi_album'];

    // Update data album ke database
    $sql = "UPDATE album SET NamaAlbum = '$namaAlbum', Deskripsi = '$deskripsiAlbum' WHERE AlbumID = '$albumID'";

    if (mysqli_query($conn, $sql)) {
        // Redirect ke halaman daftar album setelah sukses
        header('Location: album.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Album</title>
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

        main {
            margin: 80px auto; 
            padding: 20px;
            width: 40%;
            background-color: #ffffff; 
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        <h1>Edit Album</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="nama_album">Nama Album:</label>
                <input type="text" name="nama_album" value="<?php echo htmlspecialchars($album['NamaAlbum']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi_album">Deskripsi Album:</label>
                <textarea name="deskripsi_album" required><?php echo htmlspecialchars($album['Deskripsi']); ?></textarea>
            </div>
            
            <input type="submit" value="Update Album">
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
