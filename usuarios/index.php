<nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
        <li class="breadcrumb-item active" aria-current="page">Usuarios</li>
    </ol>
</nav>
<div class="card">
    <div class="card-body">
        <div class="container">
            <div class="row mb-3 justify-content-between">
                <div class="col-auto">
                    <h5>Gestión de usuarios</h5>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-dark"><i class="fa-regular fa-file-excel"></i> Importar
                        usuarios</button>
                    <button type="button" class="btn btn-outline-dark" data-bs-toggle="modal"
                        data-bs-target="#insertModal"><i class="fa-solid fa-plus"></i> Agregar usuario</button>
                </div>
            </div>
            <div class="row mb-3">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">No.</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Apellido paterno</th>
                            <th scope="col">Apellido materno</th>
                            <th scope="col">Correo</th>
                            <th scope="col">Rol</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tbodyUsuarios">
                        <tr>
                            <td colspan="7" class="text-center text-muted">Cargando usuarios...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row mb-3">
                <a class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="collapse"
                    href="#collapseDesactivados" role="button" aria-expanded="false"
                    aria-controls="collapseDesactivados" id="toggleDesactivados">

                    <i class="fa-solid fa-angle-right" id="iconDesactivados"></i>
                    <span class="fw-semibold">Usuarios desactivados</span>
                </a>
            </div>
            <div class="collapse" id="collapseDesactivados">
                <div class="row mb-3">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th scope="col">No.</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido paterno</th>
                                <th scope="col">Apellido materno</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyUsuariosDesactivados">
                            <tr>
                                <td colspan="7" class="text-center text-muted">Despliega para cargar usuarios
                                    desactivados…</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="insertModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="insertModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="insertModalLabel">Insertar usuario</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="userId">
                <div class="container">
                    <div class="row align-items-center">
                        <div id="mensajes"></div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="inputNombre" class="form-label">Nombre(s)</label>
                                <input type="text" class="form-control" id="inputNombre" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="inputAPaterno" class="form-label">Apellido paterno</label>
                                <input type="text" class="form-control" id="inputAPaterno" required>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="mb-3">
                                <label for="inputAMaterno" class="form-label">Apellido materno</label>
                                <input type="text" class="form-control" id="inputAMaterno" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="inputCorreo" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="inputCorreo"
                                    placeholder="ejemplo@cenidet.tecnm.mx" required>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="mb-3">
                                <label for="inputPass" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="inputPass" required>
                            </div>
                        </div>
                        <div class="col">
                            <div class="mb-3">
                                <label for="inputRol" class="form-label">Rol</label>
                                <?php
                                require_once $_SERVER['DOCUMENT_ROOT'] . "/evaldoc/conn/conn.php";
                                $principal = new Principal();
                                $roles = $principal->obtenerRoles();

                                if ($roles === null) {
                                    echo "Error cargando roles";
                                } else {
                                    echo '<select name="inputRol" id="inputRol" class="form-select">';
                                    echo '<option value="">Selecciona un rol</option>';

                                    foreach ($roles as $r) {
                                        echo '<option value="' . htmlspecialchars($r['id']) . '">'
                                            . htmlspecialchars($r['rol'])
                                            . '</option>';
                                    }

                                    echo '</select>';
                                }

                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarUsuario">Guardar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="passModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="passModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="passModalLabel">Cambiar contraseña</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="container">
                    <div class="row align-items-center">
                        <div id="mensajesPass" class="d-none"></div>

                        <input type="hidden" id="passUserId">

                        <div class="mb-3">
                            <label for="inputNewPass" class="form-label">Nueva contraseña</label>
                            <input type="password" class="form-control" id="inputNewPass" autocomplete="new-password"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="inputNewPass2" class="form-label">Confirmar nueva contraseña</label>
                            <input type="password" class="form-control" id="inputNewPass2" autocomplete="new-password"
                                required>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarPass">Guardar</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="deleteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h1 class="modal-title fs-5" id="deleteModalLabel">Confirmar eliminación</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="mensajesDelete" class="d-none"></div>

                <input type="hidden" id="deleteUserId">

                <p class="mb-0">
                    ¿Estás seguro de desactivar este usuario?
                    <br>
                    <small class="text-muted">El usuario ya no aparecerá en la lista, pero se conservarán sus
                        datos.</small>
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarEliminar">
                    Desactivar
                </button>
            </div>

        </div>
    </div>
</div>