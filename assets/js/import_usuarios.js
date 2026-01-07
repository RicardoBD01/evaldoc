(function () {
    window.ImportUsuarios = window.ImportUsuarios || {};

    function mostrarErrorImport(tipo, texto) {
        const el = document.getElementById("importMensajes");
        if (!el) return;

        let icon = "";
        if (tipo === "success") icon = '<i class="fa-solid fa-circle-check"></i>';
        else if (tipo === "warning") icon = '<i class="fa-solid fa-triangle-exclamation"></i>';
        else icon = '<i class="fa-solid fa-circle-xmark"></i>';

        el.className = "";
        el.innerHTML = `
      <div class="alert alert-${tipo}" role="alert">
        ${icon} ${texto}
      </div>
    `;
    }

    function clearImportUI() {
        const mensajes = document.getElementById("importMensajes");
        const acc = document.getElementById("importAccordionContainer");
        const resumen = document.getElementById("importResumen");

        if (mensajes) {
            mensajes.className = "d-none";
            mensajes.innerHTML = "";
        }
        if (acc) acc.innerHTML = "";
        if (resumen) resumen.style.display = "none";
    }

    function setResumen(data) {
        const resumen = document.getElementById("importResumen");
        if (!resumen) return;

        resumen.style.display = "";
        const set = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val;
        };

        set("resPeriodo", data.periodo || "-");
        set("resDepartamentos", data.summary?.departamentos ?? "-");
        set("resDocentes", data.summary?.docentes ?? "-");
        set("resFilas", data.summary?.filas ?? "-");
        set("resOfertas", data.summary?.ofertas ?? "-");
        set("resAlumnosUnicos", data.summary?.alumnos_unicos ?? "-");
        set("resWarnings", data.summary?.warnings ?? "0");
    }

    function esc(s) {
        return String(s ?? "")
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    function renderAccordion(data) {
        const container = document.getElementById("importAccordionContainer");
        if (!container) return;

        container.innerHTML = "";
        const depAccordionId = "accDeps";

        // Helpers UI
        const warnCount = (x) => Number(x?.warnings_count || 0);

        const warnBadge = (n) => {
            n = Number(n || 0);
            if (n <= 0) return "";
            return `<span class="badge bg-warning text-dark ms-2" title="Advertencias">${n} ⚠</span>`;
        };

        const itemWarnClass = (n) => (Number(n || 0) > 0 ? "border border-warning-subtle" : "");

        // Track de collapse IDs que deben abrirse automáticamente
        const autoOpen = new Set();

        data.departamentos.forEach((dep, depIdx) => {
            const depId = `dep_${depIdx}`;
            const depCollapse = `collapse_${depId}`;
            const depWarn = warnCount(dep);

            const docentesCount = dep.docentes?.length || 0;
            const ofertasCount = dep.docentes?.reduce((a, d) => a + (d.ofertas?.length || 0), 0) || 0;

            const depItem = document.createElement("div");
            depItem.className = `accordion-item ${itemWarnClass(depWarn)}`;
            depItem.innerHTML = `
      <h2 class="accordion-header" id="heading_${depId}">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
          data-bs-target="#${depCollapse}" aria-expanded="false" aria-controls="${depCollapse}">
          ${esc(dep.nombre)}
          <span class="ms-2 text-muted small">(${docentesCount} docentes, ${ofertasCount} ofertas)</span>
          ${warnBadge(depWarn)}
        </button>
      </h2>

      <div id="${depCollapse}" class="accordion-collapse collapse" aria-labelledby="heading_${depId}"
        data-bs-parent="#${depAccordionId}">
        <div class="accordion-body">
          <div class="accordion" id="accDoc_${depId}"></div>
        </div>
      </div>
    `;
            container.appendChild(depItem);

            // Si el departamento tiene warnings, márcalo para abrir
            if (depWarn > 0) autoOpen.add(depCollapse);

            const docContainer = depItem.querySelector(`#accDoc_${depId}`);

            dep.docentes.forEach((doc, docIdx) => {
                const docId = `${depId}_doc_${docIdx}`;
                const docCollapse = `collapse_${docId}`;
                const docWarn = warnCount(doc);

                const docEmailTxt = doc.email
                    ? `— ${esc(doc.email)}`
                    : `<span class="text-warning">— sin email</span>`;

                const ofCount = doc.ofertas?.length || 0;

                const docItem = document.createElement("div");
                docItem.className = `accordion-item ${itemWarnClass(docWarn)}`;
                docItem.innerHTML = `
        <h2 class="accordion-header" id="heading_${docId}">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
            data-bs-target="#${docCollapse}" aria-expanded="false" aria-controls="${docCollapse}">
            ${esc(doc.nombre)} ${docEmailTxt}
            <span class="ms-2 text-muted small">(${ofCount} ofertas)</span>
            ${warnBadge(docWarn)}
          </button>
        </h2>

        <div id="${docCollapse}" class="accordion-collapse collapse" aria-labelledby="heading_${docId}"
          data-bs-parent="#accDoc_${depId}">
          <div class="accordion-body">
            ${doc.warnings?.length
                        ? `<div class="alert alert-warning py-2 mb-2"><b>Advertencias docente:</b> ${esc(doc.warnings.join(", "))}</div>`
                        : ""
                    }
            <div class="accordion" id="accOf_${docId}"></div>
          </div>
        </div>
      `;
                docContainer.appendChild(docItem);

                if (docWarn > 0) {
                    autoOpen.add(depCollapse);
                    autoOpen.add(docCollapse);
                }

                const ofContainer = docItem.querySelector(`#accOf_${docId}`);

                doc.ofertas.forEach((of, ofIdx) => {
                    const ofId = `${docId}_of_${ofIdx}`;
                    const ofCollapse = `collapse_${ofId}`;
                    const ofWarn = warnCount(of);

                    const alumnosCount = of.alumnos?.length || 0;
                    const ofTitle = `${esc(of.materia_clave)} — ${esc(of.materia_nombre)} — Grupo ${esc(of.grupo)} `;

                    const ofItem = document.createElement("div");
                    ofItem.className = `accordion-item ${itemWarnClass(ofWarn)}`;
                    ofItem.innerHTML = `
          <h2 class="accordion-header" id="heading_${ofId}">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
              data-bs-target="#${ofCollapse}" aria-expanded="false" aria-controls="${ofCollapse}">
              ${ofTitle}
              <span class="ms-2 text-muted small">(${alumnosCount} alumnos)</span>
              ${warnBadge(ofWarn)}
            </button>
          </h2>

          <div id="${ofCollapse}" class="accordion-collapse collapse" aria-labelledby="heading_${ofId}"
            data-bs-parent="#accOf_${docId}">
            <div class="accordion-body">
              ${of.warnings?.length
                            ? `<div class="alert alert-warning py-2 mb-2"><b>Advertencias oferta:</b> ${esc(of.warnings.join(", "))}</div>`
                            : ""
                        }

              <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Matrícula</th>
                      <th>Nombre</th>
                      <th>Email</th>
                      <th>Warnings</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${(of.alumnos || []).map((a, i) => {
                            const aHasWarn = !!a.has_warnings || (a.warnings && a.warnings.length);
                            const aWarnTxt = (a.warnings && a.warnings.length) ? esc(a.warnings.join(", ")) : "";

                            // Si hay warning en alumno, abre su ruta completa
                            if (aHasWarn) {
                                autoOpen.add(depCollapse);
                                autoOpen.add(docCollapse);
                                autoOpen.add(ofCollapse);
                            }

                            return `
                        <tr class="${aHasWarn ? "table-warning" : ""}">
                          <td>${i + 1}</td>
                          <td>${esc(a.matricula)}</td>
                          <td>${esc(a.nombre)}</td>
                          <td>${esc(a.email)}</td>
                          <td>
                            ${aHasWarn
                                    ? `<span class="badge bg-warning text-dark">${aWarnTxt || "warning"}</span>`
                                    : ""
                                }
                          </td>
                        </tr>
                      `;
                        }).join("")}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        `;
                    ofContainer.appendChild(ofItem);

                    if (ofWarn > 0) {
                        autoOpen.add(depCollapse);
                        autoOpen.add(docCollapse);
                        autoOpen.add(ofCollapse);
                    }
                });
            });
        });

        container.id = depAccordionId;

        // ✅ Abrir automáticamente los bloques con warnings.
        // Debe ejecutarse después de renderizar el DOM.
        // Usamos Bootstrap Collapse de forma segura.
        setTimeout(() => {
            autoOpen.forEach((collapseId) => {
                const el = document.getElementById(collapseId);
                if (!el) return;
                try {
                    const inst = bootstrap.Collapse.getOrCreateInstance(el, { toggle: false });
                    inst.show();
                } catch (e) {
                    // Si bootstrap no está disponible por alguna razón, no rompemos la UI
                    console.warn("No se pudo abrir collapse:", collapseId, e);
                }
            });
        }, 0);
    }


    async function cargarPreview() {
        const fileInput = document.getElementById("excelImportFile");
        if (!fileInput || !fileInput.files || !fileInput.files.length) {
            mostrarErrorImport("warning", "Selecciona un archivo Excel (.xlsx).");
            return;
        }

        clearImportUI();

        const fd = new FormData();
        fd.append("file", fileInput.files[0]);

        const btn = document.getElementById("btnCargarPreview");
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Leyendo...`;
        }

        try {
            const resp = await fetch("/evaldoc/usuarios/preview_import.php", {
                method: "POST",
                body: fd,
            });

            const data = await resp.json();

            if (!data.success) {
                mostrarErrorImport("danger", data.message || "No se pudo generar la vista previa.");
                return;
            }

            setResumen(data);
            renderAccordion(data);

            if ((data.summary?.warnings ?? 0) > 0) {
                mostrarErrorImport("warning", "Vista previa generada con advertencias. Revisa los bloques.");
            } else {
                mostrarErrorImport("success", "Vista previa generada correctamente.");
            }
        } catch (e) {
            console.error(e);
            mostrarErrorImport("danger", "Error de conexión/servidor al generar la vista previa." + e);
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = `<i class="fa-solid fa-magnifying-glass"></i> Generar vista previa`;
            }
        }
    }

    /**
     * ✅ ÚNICO punto de entrada
     * Llamar cada vez que se entra a la sección Usuarios.
     * Usa off/on para no duplicar handlers.
     */
    window.ImportUsuarios.init = function () {
        // Abrir modal
        $(document).off("click", "#btnAbrirImportModal");
        $(document).on("click", "#btnAbrirImportModal", function () {
            clearImportUI();
            $("#excelImportFile").val("");
            $("#importFileName").text("");
            $("#importPreviewModal").modal("show");
        });

        // Mostrar nombre de archivo
        $(document).off("change", "#excelImportFile");
        $(document).on("change", "#excelImportFile", function () {
            const f = this.files && this.files[0] ? this.files[0].name : "";
            $("#importFileName").text(f);
        });

        // Generar preview
        $(document).off("click", "#btnCargarPreview");
        $(document).on("click", "#btnCargarPreview", function (e) {
            e.preventDefault();
            cargarPreview();
        });
    };
})();
