$(main);

function main() {
    bindSidebarLinks();
    handleBackForward();

    // Si entras directo a /index.php?page=usuarios, carga esa sección
    const params = new URLSearchParams(window.location.search);
    const page = params.get("page") || "inicio";
    loadPage(page, false);
}

function bindSidebarLinks() {
    // Delegación por si el sidebar se re-renderiza
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
        data: { page: page },
        success: function (html) {
            const trimmed = (html || "").trim();
            if (trimmed.length === 0) {
                $("#app-content").html("<div class='p-3 text-muted'>Aún no hay contenido en esta sección.</div>");
            } else {
                $("#app-content").html(html);
                loadModuleScript(page);
            }

            if (pushState) {
                history.pushState({ page }, "", `/evaldoc/index.php?page=${encodeURIComponent(page)}`);
            }
        },
        error: function (xhr) {
            $("#app-content").html(
                `<div class="p-3 text-danger">No se pudo cargar la sección (${xhr.status}).</div>`
            );
        }
    });
}


function handleBackForward() {
    window.addEventListener("popstate", function (event) {
        const page = (event.state && event.state.page)
            ? event.state.page
            : (new URLSearchParams(window.location.search).get("page") || "dashboard");

        loadPage(page, false);
        setActiveLink(page);
    });
}

function setActiveLink(page) {
    $("a.js-nav").removeClass("active");
    $(`a.js-nav[data-page="${page}"]`).addClass("active");
}

function loadModuleScript(page) {
    const map = {
        usuarios: "/evaldoc/assets/js/usuarios.js",
        materias: "/evaldoc/assets/js/materias.js",
        encuesta: "/evaldoc/assets/js/encuesta.js",
        // agrega más conforme crezcan
    };

    const src = map[page];
    if (!src) return;

    // Evita cargarlo más de una vez
    if (document.querySelector(`script[data-module="${page}"]`)) {
        // Si ya existe, solo re-inicializa handlers si el módulo lo define
        if (window.Modules && typeof window.Modules[page] === "function") {
            window.Modules[page]();
        }
        return;
    }

    const s = document.createElement("script");
    s.src = src;
    s.defer = true;
    s.setAttribute("data-module", page);

    s.onload = function () {
        if (window.Modules && typeof window.Modules[page] === "function") {
            window.Modules[page]();
        }
    };

    s.onerror = function () {
        console.error("No se pudo cargar el script del módulo:", src);
    };

    document.body.appendChild(s);
}


function initPageScripts(page) {
    // Aquí inicializas cosas específicas por pantalla
    // Ej: si usuarios necesita DataTables, etc.
    // if (page === "usuarios") initUsuarios();
}
