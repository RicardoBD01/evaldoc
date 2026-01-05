<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'ok') {
    // Si no está la variable 'login' o no es 'OK', redirigir
    header("Location: /evaldoc/login/");
    exit;
}
?>
<!doctype html>
<html lang="es">

<head>
    <title>Sistema de Evaluación Docente</title>
    <?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/config.php";
    include BASE_PATH . "/includes/header.php";
    ?>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <!--  App Topstrip -->
        <?php include BASE_PATH . "/includes/topstrip.php"; ?>
        <!-- Sidebar Start -->
        <?php
        include BASE_PATH . "/includes/sidebar.php";
        ?>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <?php
            include BASE_PATH . "/includes/nav.php";
            ?>
            <!--  Header End -->
            <!--  Index Start -->
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    <div id="app-content"></div>
                </div>
            </div>
            <!--  Index End -->
        </div>
    </div>
    <?php
    include BASE_PATH . "/includes/footer.php";
    ?>