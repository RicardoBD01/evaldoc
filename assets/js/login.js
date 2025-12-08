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
        mostrarError("Por favor llena todos los campos.");
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
                window.location.href = "/evaldoc/";
            } else {
                mostrarError(response.message);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Status:", jqXHR.status);
            console.error("Respuesta:", jqXHR.responseText);
            console.error("Error:", errorThrown);

            mostrarError("Error de conexión con el servidor.");
        }
    });
}

function mostrarError(texto) {
    console.log(texto);
}
