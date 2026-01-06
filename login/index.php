<!doctype html>
<html lang="es">

<head>
    <title>Login - Sistema de Evaluación Docente</title>
    <?php
    include_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/config.php";
    include BASE_PATH . "/includes/header.php";
    ?>
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div class="position-relative overflow-hidden min-vh-100 d-flex align-items-center justify-content-center"
            style="background-color: #23236df2;">
            <div class="row justify-content-center ">
                <div class="card" style="padding-top: 10px; padding-bottom: 10px;">
                    <div class="row justify-content-center">
                        <div class="col-6">
                            <img src="/evaldoc/assets/images/logos/tecnm_simple.png" alt="logo tecnm" width="512px"
                                height="512px">
                        </div>
                        <div class="col-6">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <a href="/evaldoc/index.php"
                                        class="text-nowrap logo-img text-center d-block py-3 w-100">
                                        <img src="/evaldoc/assets/images/logos/Logo-CENIDET.png" alt="logo cenidet"
                                            width="140px">
                                    </a>
                                    <div id="mensajes"></div>
                                    <form>
                                        <div class="mb-3">
                                            <label for="inputemail" class="form-label">Correo
                                                institucional</label>
                                            <input type="email" class="form-control" id="inputemail"
                                                aria-describedby="emailHelp" name="inputemail"
                                                placeholder="ejemplo@cenidet.tecnm.mx">
                                        </div>
                                        <div class="mb-4">
                                            <label for="inputpass" class="form-label">Contraseña</label>
                                            <input type="password" class="form-control" name="inputpass" id="inputpass">
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input primary" type="checkbox" value=""
                                                    id="flexCheckChecked" checked>
                                                <label class="form-check-label text-dark" for="flexCheckChecked">
                                                    Recordarme
                                                </label>
                                            </div>
                                            <a class="text-primary fw-bold" href="./index.html">Olvidé la
                                                contraseña</a>
                                        </div>
                                        <button type="button" id="btnLogin"
                                            class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                            Iniciar sesión
                                        </button>

                                        <div class="d-flex align-items-center justify-content-center">
                                            <p class="fs-4 mb-0 fw-bold">¿No tienes una cuenta?</p>
                                            <a class="text-primary fw-bold ms-2" href="/evaldoc/registro">Crea una</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    $pageScript = 'login.js';
    include BASE_PATH . "/includes/footer.php";
    ?>