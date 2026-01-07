// Namespace global simple para módulos
window.Modules = window.Modules || {};

window.Modules.usuarios = function () {
    mainUsuarios();
};

function mainUsuarios() {
    reloadUsuarios();
    bindSubmitFormularioUsuario();
    bindAgregarUsuario();
    bindEditarUsuario();
    bindEditarContrasena();
    bindGuardarContrasena();
    bindEliminarUsuario();
    bindConfirmarEliminar();
    bindResetModalOnClose();
    bindReactivarUsuario();

    initDesactivadosCollapse();
    initImportPreview();
}

function loadScriptOnce(src, key) {
    window.__loadedScripts = window.__loadedScripts || {};
    if (window.__loadedScripts[key]) return window.__loadedScripts[key];

    window.__loadedScripts[key] = new Promise((resolve, reject) => {
        const s = document.createElement("script");
        s.src = src;
        s.defer = true;
        s.onload = () => resolve(true);
        s.onerror = () => reject(new Error("No se pudo cargar: " + src));
        document.body.appendChild(s);
    });

    return window.__loadedScripts[key];
}

function initImportPreview() {
    loadScriptOnce("/evaldoc/assets/js/import_usuarios.js", "import_usuarios")
        .then(() => {
            if (window.ImportUsuarios && typeof window.ImportUsuarios.init === "function") {
                window.ImportUsuarios.init(); // ✅ binds off/on
            } else {
                console.warn("import_usuarios.js cargó pero no expone window.ImportUsuarios.init()");
            }
        })
        .catch(console.error);
}

function bindSubmitFormularioUsuario() {
    // Delegación por si el modal se re-renderiza
    $(document).off("click", "#btnGuardarUsuario"); // evita dobles eventos
    $(document).on("click", "#btnGuardarUsuario", function () {
        const mode = $("#insertModal").data("mode");

        if (mode === "edit") {
            actualizarUsuarioAjax();
        } else {
            insertarUsuarioAjax();
        }
    });

}

function bindAgregarUsuario() {
    $(document).off("click", "#btnAgregarUsuario");
    $(document).on("click", "#btnAgregarUsuario", function (e) {
        e.preventDefault();
        abrirModalInsertar();
    });
}

function bindEditarUsuario() {
    $(document).off("click", ".btnEditarUsuario");
    $(document).on("click", ".btnEditarUsuario", function () {
        const id = $(this).data("id");
        if (!id) return;

        cargarUsuarioEnModal(id);
    });
}

function bindEditarContrasena() {
    $(document).off("click", ".btnEditarContraseña");
    $(document).on("click", ".btnEditarContraseña", function () {
        const id = $(this).data("id");
        if (!id) return;

        // limpiar
        $("#passUserId").val(id);
        $("#inputNewPass, #inputNewPass2").val("");
        $("#mensajesPass").addClass("d-none").html("");

        $("#passModal").modal("show");
    });
}

function bindGuardarContrasena() {
    $(document).off("click", "#btnGuardarPass");
    $(document).on("click", "#btnGuardarPass", function (e) {
        e.preventDefault();
        actualizarContrasenaAjax();
    });
}

function bindEliminarUsuario() {
    $(document).off("click", ".btnEliminarUsuario");
    $(document).on("click", ".btnEliminarUsuario", function () {
        const id = $(this).data("id");
        if (!id) return;

        // limpiar estado del modal
        $("#deleteUserId").val(id);
        $("#mensajesDelete").addClass("d-none").html("");

        $("#deleteModal").modal("show");
    });
}

function bindConfirmarEliminar() {
    $(document).off("click", "#btnConfirmarEliminar");
    $(document).on("click", "#btnConfirmarEliminar", function (e) {
        e.preventDefault();
        const id = $("#deleteUserId").val();
        if (!id) return;

        eliminarUsuarioAjax(id);
    });
}

function bindResetModalOnClose() {
    $("#insertModal").on("hidden.bs.modal", function () {
        limpiarFormularioUsuario();
        $("#insertModal").data("mode", "insert"); // vuelve al default
    });
}

function bindReactivarUsuario() {
    $(document).off("click", ".btnReactivarUsuario");
    $(document).on("click", ".btnReactivarUsuario", function () {
        const id = $(this).data("id");
        if (!id) return;

        $.ajax({
            url: "/evaldoc/usuarios/reactivate.php",
            type: "POST",
            dataType: "json",
            data: { id },
            success: function (resp) {
                if (resp && resp.success) {
                    mostrarError("success", resp.message || "Usuario reactivado.");
                    reloadUsuarios();
                    reloadUsuariosDesactivados(true);
                } else {
                    mostrarError("danger", (resp && resp.message) ? resp.message : "No se pudo reactivar.");
                }
            },
            error: function (xhr) {
                console.error(xhr.status, xhr.responseText);
                mostrarError("danger", "Error del servidor al reactivar.");
            }
        });
    });
}

function initDesactivadosCollapse() {
    const $collapse = $("#collapseDesactivados");

    // Rotación del ícono
    $collapse.on("show.bs.collapse", function () {
        $("#iconDesactivados").removeClass("fa-angle-right").addClass("fa-angle-down");
        reloadUsuariosDesactivados(true);
    });

    $collapse.on("hide.bs.collapse", function () {
        $("#iconDesactivados").removeClass("fa-angle-down").addClass("fa-angle-right");
    });
}

let desactivadosCargados = false;

function reloadUsuariosDesactivados(force = false) {
    if (desactivadosCargados && !force) return;

    $("#tbodyUsuariosDesactivados").html(
        `<tr><td colspan="7" class="text-center text-muted">Cargando usuarios desactivados...</td></tr>`
    );

    $.ajax({
        url: "/evaldoc/usuarios/list_inactive.php",
        type: "GET",
        success: function (html) {
            const trimmed = (html || "").trim();
            if (trimmed.length === 0) {
                $("#tbodyUsuariosDesactivados").html(
                    `<tr><td colspan="7" class="text-center text-muted">No hay usuarios desactivados.</td></tr>`
                );
            } else {
                $("#tbodyUsuariosDesactivados").html(html);
            }
            desactivadosCargados = true;
        },
        error: function (xhr) {
            console.error(xhr.status, xhr.responseText);
            $("#tbodyUsuariosDesactivados").html(
                `<tr><td colspan="7" class="text-center text-danger">Error al cargar usuarios desactivados.</td></tr>`
            );
        }
    });
}

function abrirModalInsertar() {
    limpiarFormularioUsuario();

    $("#insertModalLabel").text("Insertar usuario");
    $("#btnGuardarUsuario").text("Insertar");
    $("#insertModal").data("mode", "insert");

    $("#insertModal").modal("show");
}

function abrirModalEditar(usuario) {
    limpiarFormularioUsuario();

    $("#insertModalLabel").text("Editar usuario");
    $("#btnGuardarUsuario").text("Guardar cambios");
    $("#insertModal").data("mode", "edit");

    // Guardar ID
    $("#userId").val(usuario.id);

    // Precargar campos
    $("#inputNombre").val(usuario.nombre);
    $("#inputAPaterno").val(usuario.apaterno);
    $("#inputAMaterno").val(usuario.amaterno);
    $("#inputCorreo").val(usuario.correo);
    $("#inputRol").val(usuario.rol);

    // Contraseña NO obligatoria al editar
    $("#inputPass").val("").prop("required", false);

    $("#insertModal").modal("show");
}

function cargarUsuarioEnModal(id) {
    $("#mensajes").addClass("d-none").html("");

    $.ajax({
        url: "/evaldoc/usuarios/get.php",
        type: "GET",
        dataType: "json",
        data: { id: id },
        success: function (resp) {
            if (!resp.success) {
                mostrarError("danger", resp.message || "No se pudo cargar el usuario.");
                return;
            }

            const u = resp.usuario;

            // Si quieres recargar roles dinámicamente, opcional:
            // renderRoles(resp.roles, u.rol);

            abrirModalEditar(u); // ✅ abre #insertModal en modo edición
        },
        error: function (xhr) {
            console.error(xhr.status, xhr.responseText);
            mostrarError("danger", "Error del servidor al cargar el usuario.");
        }
    });
}

function reloadUsuarios() {
    $("#tbodyUsuarios").html(`<tr><td colspan="7" class="text-center text-muted">Cargando...</td></tr>`);

    $.ajax({
        url: "/evaldoc/usuarios/list.php",
        type: "GET",
        success: function (html) {
            const trimmed = (html || "").trim();
            if (trimmed.length === 0) {
                $("#tbodyUsuarios").html(`<tr><td colspan="7" class="text-center text-muted">Sin datos</td></tr>`);
            } else {
                $("#tbodyUsuarios").html(html);
            }
        },
        error: function (xhr) {
            console.error(xhr.status, xhr.responseText);
            $("#tbodyUsuarios").html(
                `<tr><td colspan="7" class="text-center text-danger">Error al cargar usuarios</td></tr>`
            );
        }
    });
}

function insertarUsuarioAjax() {
    // 1) Leer valores
    const payload = {
        nombre: $("#inputNombre").val()?.trim(),
        apaterno: $("#inputAPaterno").val()?.trim(),
        amaterno: $("#inputAMaterno").val()?.trim(),
        correo: $("#inputCorreo").val()?.trim(),
        pass: $("#inputPass").val() || "",
        rol: $("#inputRol").val()
    };

    // 2) Validación rápida (cliente)
    const err = validarUsuario(payload);
    if (err) {
        mostrarError("danger", err);
        return;
    }

    // 3) (Opcional) deshabilitar botón mientras envía
    const $btn = $("#btnGuardarUsuario");
    $btn.prop("disabled", true).text("Guardando...");

    // 4) Enviar
    $.ajax({
        url: "/evaldoc/usuarios/insert.php",
        type: "POST",
        dataType: "json",
        data: payload,
        success: function (resp) {
            if (resp && resp.success) {
                mostrarError("success", resp.message || "Usuario agregado.");
                $("#insertModal").modal("hide");
                reloadUsuarios();
            } else {
                mostrarError("danger", (resp && resp.message) ? resp.message : "No se pudo agregar el usuario.");
            }
        }
        ,
        error: function (xhr) {
            console.error("HTTP:", xhr.status);
            console.error("RESP:", xhr.responseText);
            mostrarError("danger", "Error del servidor al insertar usuario.");
        },
        complete: function () {
            $btn.prop("disabled", false);

            const mode = $("#insertModal").data("mode");
            $btn.text(mode === "edit" ? "Guardar cambios" : "Insertar");
        }
    });
}

function actualizarUsuarioAjax() {
    const payload = {
        id: $("#userId").val(),
        nombre: $("#inputNombre").val()?.trim(),
        apaterno: $("#inputAPaterno").val()?.trim(),
        amaterno: $("#inputAMaterno").val()?.trim(), // puede ser vacío
        correo: $("#inputCorreo").val()?.trim(),
        rol: $("#inputRol").val(),
        pass: $("#inputPass").val() || ""        // opcional en edición
    };

    // Validación cliente (pass NO obligatoria)
    const err = validarUsuarioEdicion(payload);
    if (err) {
        mostrarError("danger", err);
        return;
    }

    const $btn = $("#btnGuardarUsuario");
    $btn.prop("disabled", true).text("Guardando...");

    $.ajax({
        url: "/evaldoc/usuarios/update.php",
        type: "POST",
        dataType: "json",
        data: payload,
        success: function (resp) {
            if (resp && resp.success) {
                mostrarError("success", resp.message || "Usuario actualizado.");

                $("#insertModal").modal("hide");
                reloadUsuarios(); // ✅ refresca tabla sin recargar página
            } else {
                mostrarError("danger", (resp && resp.message) ? resp.message : "No se pudo actualizar.");
            }
        },
        error: function (xhr) {
            console.error(xhr.status, xhr.responseText);
            mostrarError("danger", "Error del servidor al actualizar usuario.");
        },
        complete: function () {
            $btn.prop("disabled", false);

            const mode = $("#insertModal").data("mode");
            $btn.text(mode === "edit" ? "Guardar cambios" : "Insertar");
        }
    });
}

function actualizarContrasenaAjax() {
    const id = $("#passUserId").val();
    const p1 = $("#inputNewPass").val() || "";
    const p2 = $("#inputNewPass2").val() || "";

    if (!id || parseInt(id, 10) <= 0) return mostrarErrorPass("danger", "ID inválido.");
    if (!p1 || !p2) return mostrarErrorPass("danger", "Debes llenar ambos campos.");
    if (p1 !== p2) return mostrarErrorPass("danger", "Las contraseñas no coinciden.");
    if (p1.length < 8) return mostrarErrorPass("warning", "La contraseña debe tener al menos 8 caracteres.");

    const $btn = $("#btnGuardarPass");
    $btn.prop("disabled", true).text("Guardando...");

    $.ajax({
        url: "/evaldoc/usuarios/update_password.php",
        type: "POST",
        dataType: "json",
        data: { id: id, pass: p1 },
        success: function (resp) {
            if (resp && resp.success) {
                mostrarErrorPass("success", resp.message || "Contraseña actualizada.");

                // cerrar modal después de un momento (opcional)
                setTimeout(() => $("#passModal").modal("hide"), 600);
            } else {
                mostrarErrorPass("danger", (resp && resp.message) ? resp.message : "No se pudo actualizar la contraseña.");
            }
        },
        error: function (xhr) {
            console.error(xhr.status, xhr.responseText);
            mostrarErrorPass("danger", "Error del servidor al actualizar contraseña.");
        },
        complete: function () {
            $btn.prop("disabled", false).text("Guardar");
        }
    });
}

function eliminarUsuarioAjax(id) {
    const $btn = $("#btnConfirmarEliminar");
    $btn.prop("disabled", true).text("Desactivando...");

    $.ajax({
        url: "/evaldoc/usuarios/delete.php",
        type: "POST",
        dataType: "json",
        data: { id: id },
        success: function (resp) {
            if (resp && resp.success) {
                // Mensaje global (tu div mensajes principal)
                mostrarError("success", resp.message || "Usuario desactivado.");

                $("#deleteModal").modal("hide");
                reloadUsuarios(); // refrescar tabla
                if ($("#collapseDesactivados").hasClass("show")) {
                    reloadUsuariosDesactivados(true);
                } else {
                    // opcional: para que al abrir después se recargue
                    desactivadosCargados = false;
                }
            } else {
                mostrarErrorDelete("danger", (resp && resp.message) ? resp.message : "No se pudo desactivar.");
            }
        },
        error: function (xhr) {
            console.error(xhr.status, xhr.responseText);
            mostrarErrorDelete("danger", "Error del servidor al desactivar usuario.");
        },
        complete: function () {
            $btn.prop("disabled", false).text("Desactivar");
        }
    });
}

function confirmarEliminarUsuario(id) {
    if (!confirm("¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.")) {
        return;
    }

    eliminarUsuarioAjax(id);
}

function validarUsuarioEdicion(u) {
    if (!u.id || parseInt(u.id, 10) <= 0) return "ID inválido.";
    if (!u.nombre) return "El nombre es obligatorio.";
    if (!u.apaterno) return "El apellido paterno es obligatorio.";
    if (!u.correo) return "El correo es obligatorio.";
    if (!u.rol) return "Debes seleccionar un rol.";

    const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!reEmail.test(u.correo)) return "El correo no tiene un formato válido.";

    const rolNum = parseInt(u.rol, 10);
    if (Number.isNaN(rolNum) || rolNum <= 0) return "Rol inválido.";

    // pass opcional; si viene, mínimo 8
    if (u.pass && u.pass.length > 0 && u.pass.length < 8) {
        return "Si cambias la contraseña, debe tener al menos 8 caracteres.";
    }

    return null;
}

function validarUsuario(u) {
    if (!u.nombre) return "El nombre es obligatorio.";
    if (!u.apaterno) return "El apellido paterno es obligatorio.";
    if (!u.correo) return "El correo es obligatorio.";
    if (!u.pass) return "La contraseña es obligatoria.";
    if (!u.rol) return "Debes seleccionar un rol.";

    // Email básico
    const reEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!reEmail.test(u.correo)) return "El correo no tiene un formato válido.";

    // Password mínimo (ajusta a tus reglas)
    if (u.pass.length < 8) return "La contraseña debe tener al menos 8 caracteres.";

    // Rol numérico válido
    const rolNum = parseInt(u.rol, 10);
    if (Number.isNaN(rolNum) || rolNum <= 0) return "Rol inválido.";

    return null;
}

function mostrarError(tipo, texto) {
    let mensajes = document.getElementById('mensajes');
    var icon;

    mensajes.setAttribute('class', 'd-none')

    if (tipo == 'success') icon = '<i class="fa-solid fa-circle-check"></i>';
    else if (tipo == 'warning') icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
    else if (tipo == 'danger') icon = '<i class="fa-solid fa-circle-xmark"></i>';

    let alert = '<div class="alert alert-' + tipo + '" role="alert">' +
        icon + ' ' + texto +
        '</div>';

    mensajes.setAttribute('class', '');
    mensajes.innerHTML = alert;
}

function mostrarErrorPass(tipo, texto) {
    let mensajes = document.getElementById('mensajesPass');
    let icon;

    mensajes.setAttribute('class', 'd-none');

    if (tipo === 'success') icon = '<i class="fa-solid fa-circle-check"></i>';
    else if (tipo === 'warning') icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
    else if (tipo === 'danger') icon = '<i class="fa-solid fa-circle-xmark"></i>';

    let alert = '<div class="alert alert-' + tipo + '" role="alert">' +
        icon + ' ' + texto +
        '</div>';

    mensajes.setAttribute('class', '');
    mensajes.innerHTML = alert;
}

function mostrarErrorDelete(tipo, texto) {
    let mensajes = document.getElementById('mensajesDelete');
    var icon;

    mensajes.setAttribute('class', 'd-none')

    if (tipo === 'success') icon = '<i class="fa-solid fa-circle-check"></i>';
    else if (tipo === 'warning') icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
    else if (tipo === 'danger') icon = '<i class="fa-solid fa-circle-xmark"></i>';

    let alert = '<div class="alert alert-' + tipo + '" role="alert">' +
        icon + ' ' + texto +
        '</div>';

    mensajes.setAttribute('class', '');
    mensajes.innerHTML = alert;
}

function mostrarOk(msg) {
    console.log(msg);
}

function limpiarFormularioUsuario() {
    $("#userId").val("");
    $("#inputNombre, #inputAPaterno, #inputAMaterno, #inputCorreo, #inputPass").val("");
    $("#inputRol").val("");
    $("#mensajes").addClass("d-none").html("");

    $("#inputPass").prop("required", true);

    $("#insertModalLabel").text("Insertar usuario");
    $("#btnGuardarUsuario").text("Insertar");
    $("#insertModal").data("mode", "insert");
}

function escapeHtml(str) {
    return String(str)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}