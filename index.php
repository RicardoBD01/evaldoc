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
                    <div id="app-content">
                        <?php require BASE_PATH . "/pages/load.php"; ?>
                    </div>
                </div>
            </div>
            <!--  Index End -->
        </div>
    </div>
    <?php $forceChange = !empty($_SESSION['must_change_pass']); ?>

    <?php if ($forceChange): ?>
        <div class="modal fade" id="forcePassModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
            data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambio obligatorio de contraseña</h5>
                    </div>
                    <div class="modal-body">
                        <div id="forcePassMsg"></div>

                        <div class="mb-3">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="newPass" minlength="8">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Confirmar contraseña</label>
                            <input type="password" class="form-control" id="confirmPass" minlength="8">
                        </div>

                        <div class="text-muted small">
                            Por seguridad, debes cambiar tu contraseña para continuar.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="btnForcePassSave">Guardar</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.FORCE_CHANGE_PASS = true;
        </script>
    <?php endif; ?>

    <?php
    include BASE_PATH . "/includes/footer.php";
    ?>