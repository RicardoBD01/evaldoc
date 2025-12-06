<!doctype html>
<html lang="en">

<head>
    <title>Registro - Sistema de Evaluación Docente</title>
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
            <div class="row justify-content-center" style="width: 80%;">
                <div class="card" style="padding-top: 10px; padding-bottom: 10px;">
                    <div class="row justify-content-center">
                        <div class="col-4">
                            <img src="/evaldoc/assets/images/logos/tecnm_simple.png" alt="logo tecnm" width="512px"
                                height="512px">
                        </div>
                        <div class="col-8">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <a href="/evaldoc/index.php"
                                        class="text-nowrap logo-img text-center d-block py-3 w-100">
                                        <img src="/evaldoc/assets/images/logos/Logo-CENIDET.png" alt="logo cenidet"
                                            width="140px">
                                    </a>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="inputemail" class="form-label">Correo electrónico</label>
                                                <input type="email" class="form-control" id="inputemail"
                                                    placeholder="ejemplo@cenidet.tecnm.mx">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="inputnc" class="form-label">Número de control</label>
                                                <input type="text" class="form-control" id="inputnc">
                                            </div>
                                        </div>
                                    </div>
                                    <a href="./index.html"
                                        class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">Registrarse</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <?php
    include BASE_PATH . "/includes/footer.php";
    ?>
</body>

</html>