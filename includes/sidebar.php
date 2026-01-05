<aside class="left-sidebar" style="top: 75px;">
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="./index.html" class="text-nowrap logo-img">
                <img src="assets/images/logos/logo.svg" alt="" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-6"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="sidebar-item">
                    <a class="sidebar-link" href="/evaldoc/index.php?page=inicio" aria-expanded="false">
                        <i class="fa-regular fa-house"></i>
                        <span class="hide-menu">Inicio</span>
                    </a>
                </li>

                <li>
                    <span class="sidebar-divider lg"></span>
                </li>

                <?php
                if (isset($_SESSION['rol'])) {
                    switch ($_SESSION['rol']) {
                        case 1: ?>
                            <div id="admin">
                                <li class="nav-small-cap">
                                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                                    <span class="hide-menu">Gestión</span>
                                </li>

                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="/evaldoc/index.php?page=usuarios" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="ti ti-user-circle"></i>
                                            </span>
                                            <span class="hide-menu">Usuarios</span>
                                        </div>

                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="fa-solid fa-laptop"></i>
                                            </span>
                                            <span class="hide-menu">Departamentos</span>
                                        </div>

                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="fa-solid fa-brain"></i>
                                            </span>
                                            <span class="hide-menu">Materias</span>
                                        </div>

                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="ti ti-layout-kanban"></i>
                                            </span>
                                            <span class="hide-menu">Periodos de evaluación</span>
                                        </div>

                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="fa-solid fa-person-chalkboard"></i>
                                            </span>
                                            <span class="hide-menu">Alumnos por materias</span>
                                        </div>

                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="ti ti-notes"></i>
                                            </span>
                                            <span class="hide-menu">Encuesta</span>
                                        </div>

                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="fa-regular fa-message"></i>
                                            </span>
                                            <span class="hide-menu">Respuestas</span>
                                        </div>

                                    </a>
                                </li>

                                <li>
                                    <span class="sidebar-divider lg"></span>
                                </li>
                            </div>
                            <?php
                            break;
                        case 2:
                            ?>
                            <div id="docente">
                                <li class="nav-small-cap">
                                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                                    <span class="hide-menu">Gestión</span>
                                </li>

                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="fa-solid fa-book"></i>
                                            </span>
                                            <span class="hide-menu">Mis grupos</span>
                                        </div>

                                    </a>
                                </li>
                            </div>
                            <?php
                            break;
                        case 3:
                            ?>
                            <div id="estudiante">
                                <li class="nav-small-cap">
                                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                                    <span class="hide-menu">Gestión</span>
                                </li>

                                <li class="sidebar-item">
                                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="d-flex">
                                                <i class="fa-regular fa-pen-to-square"></i>
                                            </span>
                                            <span class="hide-menu">Evaluaciones</span>
                                        </div>

                                    </a>
                                </li>
                            </div>
                            <?php
                            break;
                    }
                }
                ?>

                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Ayuda</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between" href="#" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="fa-regular fa-circle-question"></i>
                            </span>
                            <span class="hide-menu">Acerca de</span>
                        </div>

                    </a>
                </li>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>