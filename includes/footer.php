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

    <!-- jQuery (SIEMPRE primero) -->
    <script src="/evaldoc/assets/js/jquery-3.7.1.min.js"></script>

    <!-- Scripts globales de la app -->
    <script src="/evaldoc/assets/js/app.js"></script>
    <script src="/evaldoc/assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="/evaldoc/assets/js/app.js"></script>
    <script src="/evaldoc/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
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
