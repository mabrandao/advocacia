<style>
  body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
  }

  main {
    flex: 1;
  }

  footer {
    margin-top: 0; /* Removendo o margin-top existente */
    position: relative;
  }
</style>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <?= date('Y') ?> <strong><span>Advocacia</span></strong>. Todos os direitos reservados
    </div>
    <div class="credits">
     Desenvolvido por <a href="https://mbdesign.com/">MBDesign - AGÊNCIA DIGITAL</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="<?= base_url() ?>assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/bootstrap/js/popper.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/chart.js/chart.umd.js"></script>
  <script src="<?= base_url() ?>assets/vendor/echarts/echarts.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/quill/quill.js"></script>
  <script src="<?= base_url() ?>assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="<?= base_url() ?>assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="<?= base_url() ?>assets/vendor/php-email-form/validate.js"></script>
  <!-- DataTables -->
  <script src="<?= base_url() ?>assets/vendor/datatables/dataTables.min.js"></script>

  <!-- Template Main JS File -->
  <script src="<?= base_url() ?>assets/js/main.js"></script>

  <script>
    // Função para inicializar tooltips
    function initTooltips() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        var tooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (tooltip) {
          tooltip.dispose();
        }
        new bootstrap.Tooltip(tooltipTriggerEl, {
          trigger: 'hover'
        });
      });
    }

    // Inicializa tooltips quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
      initTooltips();
    });

    // Reinicializa tooltips após cada draw do DataTables
    $(document).on('draw.dt', function() {
      setTimeout(function() {
        initTooltips();
      }, 100);
    });
  </script>

</body>       

</html>