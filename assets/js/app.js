$(main);

function main() {
    if (window.FORCE_CHANGE_PASS) {
        initForceChangePassword();
        return; // <-- bloquea carga SPA y navegación
    }

    bindSidebarLinks();
    handleBackForward();

    const params = new URLSearchParams(window.location.search);
    const page = params.get("page") || "inicio";
    loadPage(page, false);
}


function bindSidebarLinks() {
    $(document).on("click", "a.js-nav", function (e) {
        e.preventDefault();

        const page = $(this).data("page");
        if (!page) return;

        loadPage(page, true);
        setActiveLink(page);
    });
}

function loadPage(page, pushState) {
    $("#app-content").empty().html("<div class='p-3'>Cargando...</div>");

    $.ajax({
        url: "/evaldoc/pages/load.php",
        method: "GET",
        data: { page },

        success: function (html, _status, xhr) {
            const trimmed = (html || "").trim();

            if (trimmed.length === 0) {
                $("#app-content").html("<div class='p-3 text-muted'>Aún no hay contenido en esta sección.</div>");
            } else {
                $("#app-content").html(html);

                // ✅ Script sugerido por backend (ej. "usuarios.js")
                const pageScript = xhr.getResponseHeader("X-Page-Script") || "";
                loadModuleScript(page, pageScript);
            }

            if (pushState) {
                history.pushState({ page }, "", `/evaldoc/index.php?page=${encodeURIComponent(page)}`);
            }
        },

        error: function (xhr) {
            $("#app-content").html(`<div class="p-3 text-danger">No se pudo cargar la sección (${xhr.status}).</div>`);
        }
    });
}

function handleBackForward() {
    window.addEventListener("popstate", function (event) {
        const page = (event.state && event.state.page)
            ? event.state.page
            : (new URLSearchParams(window.location.search).get("page") || "inicio");

        loadPage(page, false);
        setActiveLink(page);
    });
}

function setActiveLink(page) {
    $("a.js-nav").removeClass("active");
    $(`a.js-nav[data-page="${page}"]`).addClass("active");
}

function loadModuleScript(page, pageScriptFromHeader) {
    let src = "";

    // ✅ Prioridad: header X-Page-Script
    if (pageScriptFromHeader && pageScriptFromHeader.trim() !== "") {
        src = `/evaldoc/assets/js/${pageScriptFromHeader.trim()}`;
    } else {
        // (fallback opcional)
        const map = {
            usuarios: "/evaldoc/assets/js/usuarios.js",
            materias: "/evaldoc/assets/js/materias.js",
            encuesta: "/evaldoc/assets/js/encuesta.js",
        };
        src = map[page] || "";
    }

    if (!src) {
        // Si no hay script, aún podemos intentar re-init si existe
        if (window.Modules && typeof window.Modules[page] === "function") window.Modules[page]();
        return;
    }

    const key = pageScriptFromHeader?.trim() || page;

    // Evitar cargar 2 veces el mismo script
    if (document.querySelector(`script[data-module="${key}"]`)) {
        if (window.Modules && typeof window.Modules[page] === "function") window.Modules[page]();
        return;
    }

    const s = document.createElement("script");
    s.src = src;
    s.defer = true;
    s.setAttribute("data-module", key);

    s.onload = function () {
        if (window.Modules && typeof window.Modules[page] === "function") window.Modules[page]();
    };

    s.onerror = function () {
        console.error("No se pudo cargar el script del módulo:", src);
    };

    document.body.appendChild(s);
}

function initForceChangePassword() {
    // Mostrar modal
    const modalEl = document.getElementById("forcePassModal");
    if (!modalEl) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();

    // Bloquear clicks en sidebar/app (por si acaso)
    document.querySelectorAll("a.js-nav, button, a").forEach(el => {
        if (el.closest("#forcePassModal")) return;
    });

    $("#btnForcePassSave").off("click").on("click", function () {
        const newPass = $("#newPass").val();
        const confirmPass = $("#confirmPass").val();

        const msg = $("#forcePassMsg");
        msg.html("");

        if (!newPass || !confirmPass) {
            msg.html(`<div class="alert alert-warning py-2">Llena todos los campos.</div>`);
            return;
        }
        if (newPass !== confirmPass) {
            msg.html(`<div class="alert alert-warning py-2">Las contraseñas no coinciden.</div>`);
            return;
        }
        if (newPass.length < 8) {
            msg.html(`<div class="alert alert-warning py-2">Mínimo 8 caracteres.</div>`);
            return;
        }

        $.ajax({
            url: "/evaldoc/auth/change_password.php",
            type: "POST",
            data: { new_pass: newPass, confirm_pass: confirmPass },
            success: function (resp) {
                if (resp.success) {
                    msg.html(`<div class="alert alert-success py-2">${resp.message}</div>`);
                    // recargar ya sin bloqueo
                    setTimeout(() => window.location.href = "/evaldoc", 600);
                } else {
                    msg.html(`<div class="alert alert-danger py-2">${resp.message}</div>`);
                }
            },
            error: function () {
                msg.html(`<div class="alert alert-danger py-2">Error de conexión.</div>`);
            }
        });
    });
}

