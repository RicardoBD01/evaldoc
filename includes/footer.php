<?php
// evaldoc/includes/footer.php
?>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; <?= date('Y') ?> Sistema de Evaluación Docente</p>
    </footer>

    <!-- =========================
         SCRIPTS GLOBALES
         ========================= -->

    <!-- =========================
         ORDEN IMPORTANTE
         1) jQuery
         2) Bootstrap (para Modals/Collapse)
         3) app.js (SPA + force-change modal)
         4) scripts del template
       ========================= -->

    <!-- jQuery (una sola vez) -->
    <script src="/evaldoc/assets/js/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap (bundle incluye Popper) -->
    <script src="/evaldoc/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script global SPA (debe ir DESPUÉS de Bootstrap) -->
    <script src="/evaldoc/assets/js/app.js"></script>

    <!-- Scripts del template (si los ocupas) -->
    <script src="/evaldoc/assets/js/sidebarmenu.js"></script>
    <script src="/evaldoc/assets/js/app.min.js"></script>
    <script src="/evaldoc/assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="/evaldoc/assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="/evaldoc/assets/js/dashboard.js"></script>

    <!-- Scripts específicos por página (opcional) -->
    <?php if (!empty($pageScript)): ?>
        <script src="/evaldoc/assets/js/<?= htmlspecialchars($pageScript) ?>"></script>
    <?php endif; ?>

</body>
</html>
