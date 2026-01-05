$(main)

function main() {
    // Evento click del botón
    $("#btnLogin").on("click", function () {
        realizarLogin();
    });
}

function realizarLogin() {
    var data = new FormData();
    let email = document.getElementById('inputemail').value;
    let pass = document.getElementById('inputpass').value;
    console.log(email + "-" + pass);

    if (email === "" || pass === "") {
        mostrarError('warning', "Por favor llena todos los campos.");
        return;
    }
    data.append('email', email);
    data.append('pass', pass);

    $.ajax({
        url: "/evaldoc/login/login.php",
        type: "POST",
        data: data,
        contentType: false,
        processData: false,
        success: function (response) {
            if (response.success) {
                window.location.href = '/evaldoc';
            } else {
                mostrarError('danger', response.message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Status:", jqXHR.status);
            console.error("Respuesta:", jqXHR.responseText);
            console.error("Error:", errorThrown);

            mostrarError('danger', "Error de conexión con el servidor.");
        }
    });
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
