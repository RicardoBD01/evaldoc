// Namespace global simple para módulos
window.Modules = window.Modules || {};

window.Modules.usuarios = function () {
    mainUsuarios();
};

function mainUsuarios() {
    bindSubmitFormularioUsuario();
    // si tienes más: bindAbrirModal(), bindResetModal(), etc.
}

function bindSubmitFormularioUsuario() {
    // Delegación por si el modal se re-renderiza
    $(document).off("click", "#btnGuardarUsuario"); // evita dobles eventos
    $(document).on("click", "#btnGuardarUsuario", function (e) {
        e.preventDefault();
        insertarUsuarioAjax();
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
                mostrarError("success", resp.message || "Usuario agregado correctamente.");

                // Limpiar campos (si quieres)
                $("#inputNombre, #inputAPaterno, #inputAMaterno, #inputCorreo, #inputPass").val("");
                $("#inputRol").val("");

                // Cerrar modal (ajusta el id si es diferente)
                $("#modalAgregarUsuario").modal("hide");

                // Si luego quieres recargar la tabla sin recargar toda la página:
                // loadPage("usuarios", false);

            } else {
                mostrarError("danger", (resp && resp.message) ? resp.message : "No se pudo agregar el usuario.");
            }
        },
        error: function (xhr) {
            console.error("HTTP:", xhr.status);
            console.error("RESP:", xhr.responseText);
            mostrarError("danger", "Error del servidor al insertar usuario.");
        },
        complete: function () {
            $btn.prop("disabled", false).text("Guardar");
        }
    });
}

/**
 * Retorna string con el error o null si todo OK
 */
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

    if(tipo == 'success') icon = '<i class="fa-solid fa-circle-check"></i>';
    else if(tipo == 'warning') icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
    else if(tipo == 'danger') icon = '<i class="fa-solid fa-circle-xmark"></i>';

    let alert = '<div class="alert alert-'+ tipo + '" role="alert">' +
                    icon + ' ' + texto +
                '</div>';
    
    mensajes.setAttribute('class', '');
    mensajes.innerHTML = alert;
}

function mostrarOk(msg) {
    console.log(msg);
}
