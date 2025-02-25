<?php

function openDB() {
    global $conn;
    $servername = "localhost";
    $username = "root2";
    $password = "f8nF1o$88";
    $dbname = "crm_carvizio";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    } else {
        echo '<script type="text/javascript">
            window.onload = function () { alert("Connessione al DB riuscita"); } 
        </script>'; 
    }        
}

function closeDB() {
    global $conn;
    $conn->close();        
}

function scrap_all() {
    global $conn;
    openDB();
    $sql = "SELECT * FROM leads";
    $result = $conn->query($sql);
    
    scrap($result);
    closeDB();
}

function scrap_notSent() {
    global $conn;
    openDB();
    $sql = "SELECT * FROM leads WHERE esito = 0";
    $result = $conn->query($sql);

    scrap($result);
    closeDB();
}

function scrap($result) {
    if ($result->num_rows > 0) {
        // Output della tabella HTML
        echo "<div class='table-responsive'>";
        echo "<table class='table table-bordered'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Nome e Cognome</th>";
        echo "<th>Email</th>";
        echo "<th>Telefono</th>";
        echo "<th>Esito</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        // Output dei dati delle lead
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["fl_name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["phone"] . "</td>";
            echo "<td>" . $row["esito"] . "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    } else {
        echo "0 results";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adextra Leads Center</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .wrapper {
            display: flex;
            width: 100%;
            flex: 1;
        }
        #sidebar {
            min-width: 250px;
            max-width: 250px;
            background: #7386D5;
            color: #fff;
            transition: all 0.3s;
            min-height: 100vh;
        }
        #sidebar.active {
            min-width: 80px;
            max-width: 80px;
        }
        #sidebar .sidebar-header {
            padding: 20px;
            background: #6d7fcc;
            text-align: center;
        }
        #sidebar .sidebar-header img {
            width: 100%;
            height: 50px; /* Altezza fissa per il logo */
            object-fit: contain; /* Mantieni le proporzioni */
        }
        #sidebar ul.components {
            padding: 20px 0;
            border-bottom: 1px solid #47748b;
        }
        #sidebar ul p {
            color: #fff;
            padding: 10px;
        }
        #sidebar ul li a {
            padding: 10px 15px;
            font-size: 1.1em;
            display: flex;
            align-items: center;
            color: #fff;
            text-align: left;
            background: #5a5a9e;
            margin: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        #sidebar ul li a i {
            font-size: 2.5em;
            margin-right: 20px;
            flex-shrink: 0;
            text-align: center;
            width: 50px;
        }
        #sidebar ul li a .menu-text {
            margin-left: 10px;
        }
        #sidebar ul li a:hover {
            color: #fff;
            background: #4a4a8e;
            text-decoration: none;
        }
        #sidebar.active ul li a {
            flex-direction: column;
            text-align: center;
        }
        #sidebar.active ul li a i {
            margin: 0;
        }
        #sidebar.active .menu-text {
            display: none;
        }
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            background: #f8f9fa;
            overflow: auto;
        }
        .navbar {
            background: #6d7fcc;
            color: #fff;
            margin-bottom: 20px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px; /* Angoli arrotondati */
        }
        .navbar-toggler {
            color: #fff;
            border-color: #fff;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #ffffff;
            font-weight: bold; /* Grassetto */
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="active">
            <div class="sidebar-header">
                <img src="assets\img\logo.png" alt="Logo">
            </div>
            <ul class="list-unstyled components">
                <li>
                    <a href="#"><i class="fas fa-home"></i> <span class="menu-text">Home</span></a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-user"></i> <span class="menu-text">About</span></a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-briefcase"></i> <span class="menu-text">Services</span></a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-envelope"></i> <span class="menu-text">Contact</span></a>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <nav class="navbar navbar-expand-lg">
                <span class="navbar-brand">Adextra Leads Center</span>
                <button type="button" id="sidebarCollapse" class="btn btn-info">
                    <i class="fas fa-bars fa-2x"></i> 
                </button>
            </nav>
            <h2>Page Content</h2>
            <p>This is the content section.</p>

            <!-- LOAD DATA FROM DATABASE -->
            <?php
                echo "<br><h2>All</h2>";
                scrap_all();
                echo "<br><h2>NotSent</h2>";
                scrap_notSent();
            ?>

        </div>
</body>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/js/all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>

</html>
