<main id="main" class="main">

    <div class="pagetitle">
      <h1><?= isset($titulo) ? $titulo : "" ?> Noticias</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=base_url()?>">Home</a></li>
          <li class="breadcrumb-item active"><?= isset($titulo) ? $titulo : "" ?> Noticias</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">
            <a class="btn btn-primary" href="<?=base_url()?>admin/noticias-store">
              <i class="bi bi-plus-circle"></i> Nova Notícia
            </a>
          </h5>
          
          <?php
            $table = ViewsHelpers::ajaxDataTables("admin/noticias-listar", $filds);
            echo $table['table'];   
          ?>
          
        </div>
      </div>
    </section>

</main><!-- End #main -->

<?= $table['script'];?>