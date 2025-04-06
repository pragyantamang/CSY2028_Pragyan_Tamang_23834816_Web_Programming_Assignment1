<?php
session_start();
include('database.php');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Carbuy Auctions</title>
    <link rel="stylesheet" href="carbuy.css" />
</head>

<body>
    <header>
        <h1><span class="C">C</span><span class="a">a</span><span class="r">r</span><span class="b">b</span><span class="u">u</span><span class="y">y</span></h1>

        <form action="#">
            <input type="text" name="search" placeholder="Search for a car" />
            <input type="submit" name="submit" value="Search" />
        </form>
          <!-- Admin Login/Logout Button -->
          <?php if (isset($_SESSION['admin'])): ?>
            <a href="logout.php"><button>Logout</button></a>
        <?php else: ?>
            <a href="adminlogin.php"><button>Login</button></a>
        <?php endif; ?>

         
    </header>

    <nav>
        <ul>
            <!-- Home link to adminpanel.php -->
            <li><a href="adminpanel.php">Home</a></li>
            <!-- Link to manage categories page -->
            <li><a href="adminCategories.php">Manage Categories</a></li>
            <!-- Link to manage addAdmin.php -->
            <li><a href="addAdmin.php">Add Admin</a></li>
            <!-- Link to manage admins page -->
            <li><a href="manageAdmins.php">Manage Admins</a></li>
        </ul>
    </nav>

    <img src="banners/1.jpg" alt="Banner" />

    <main>
        <!-- Main content goes here -->
    </main>

    <footer>
        &copy; Carbuy 2025
    </footer>
</body>
</html>

