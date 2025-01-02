<main id="main" class="main">

  <div class="pagetitle">
      <h1><?= isset($titulo) ? $titulo : "" ?> Noticias</h1>
    <nav>
      <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=base_url()?>">Home</a></li>
          <li class="breadcrumb-item active"><?= isset($tipo) ? $tipo : "" ?> Noticias</li>
      </ol>
    </nav>
    </div><!-- End Page Title -->

  <section class="section">
        <div class="card">
          <div class="card-body">
          <div class="card-header">
            <h3 class="card-title"><?= isset($titulo) ? $titulo : "" ?> Notícia</h3>
          </div>
          <?php 
         
          $param = [
           'action' => "admin/noticias-".$tipo,
           'hidden' => [
               'id' => isset($noticia['id']) ? $noticia['id'] : ''
           ],
           'fields' => [
               [
                   'name' => 'categoria',
                   'type' => 'select',
                   'label' => 'Selecione a Categoria da Notícia',
                   'class' => 'form-control',
                   'value' => isset($noticia["categoria"]) ? $noticia["categoria"] : "",
                   'required' => true,
                   'options' => [
                       '' => "-- Selecione --",
                       'Politica' => 'Política',
                       'Economia' => 'Economia',
                       'Cidade' => 'Cidade',
                       'Oeste' => 'Oeste'
                   ]
               ],
               [
                   'name' => 'titulo',
                   'type' => 'text',
                   'label' => 'Título da Materia',
                   'class' => 'form-control',
                   'value' => isset($noticia["titulo"]) ? $noticia["titulo"] : "",
                   'required' => true
               ],
               [
                   'name' => 'image',
                   'type' => 'input-group',
                   'label' => 'Imagem da Notícia',
                   'class' => 'form-control',
                   'value' => isset($noticia["image"]) ? $noticia["image"] : "",
                   'required' => true
               ],               
               [
                   'name' => 'galeria',
                   'type' => 'text',
                   'label' => 'Galeria de Imagens',
                   'class' => 'form-control',
                   'value' => isset($noticia["galeria"]) ? $noticia["galeria"] : "",
                   'required' => false
               ],
               [
                   'name' => 'content',
                   'type' => 'textarea',
                   'label' => 'Notícia Completa',
                   'class' => 'form-control editor', 
                   'value' => isset($noticia["content"]) ? $noticia["content"] : "",
                   'required' => true,
                   'col' => 'col-md-12',
                   'id' => 'content'
               ]
           ]
       ];
      
        echo ViewsHelpers::creatForm($param);
       ?>
      </div>
    </div>
  </section>

  <!-- Modal -->
  <div class="modal" id="fileManagerModal" tabindex="-1000">
    <div class="modal-dialog modal-fullscreen" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <iframe id="fileManagerIframe" src="" style="width: 100%; height: 400px; border: none;"></iframe>
        </div>
        <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
        </div>
      </div>
    </div>
  </div>

</main><!-- End #main -->

<script src="<?=base_url().'assets/vendor/tinymce/tinymce.min.js'?>"></script>

<script>
  tinymce.init({
    selector: 'textarea#content',
    height: 500,
    language: 'pt_BR',
    license_key: 'gpl',
    theme: 'silver',
    plugins: [
        'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'emoticons',
        'fullscreen', 'insertdatetime', 'media', 'nonbreaking',
        'pagebreak', 'preview', 'save', 'searchreplace', 'visualblocks',
        'visualchars', 'wordcount', 'code'
    ],
    toolbar: [
        'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist | link image media | charmap emoticons | fullscreen preview | code',
        'insertdatetime | pagebreak | save | help | searchreplace | visualblocks visualchars | nonbreaking | quickbars | table'
    ],
    image_advtab: true,
    skin: 'oxide',
    content_css: 'default',
    icons: 'default',

    // Configuração do gerenciador de arquivos
    file_picker_types: 'image',
    file_picker_callback: function(callback, value, meta) {
        if (meta.filetype === 'image') {
            // Lógica para abrir o Tiny File Manager no modal
            var fileManagerUrl = '<?= base_url() ?>gerenciador/tinyfilemanager.php?tokenrr=dfbs8fgd61vdfvdv542@ff#&52364'; // Altere para o caminho correto
            document.getElementById('fileManagerIframe').src = fileManagerUrl; // Define a URL do Tiny File Manager no iframe
            $('#fileManagerModal').modal('show'); // Exibe o modal
        }
    }
  });
</script>
