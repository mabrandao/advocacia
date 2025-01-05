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

  <style>
    /* Garante que nosso modal fique sempre na frente */
    .modal-backdrop {
        z-index: 1055 !important;
    }
    #fileManagerModal {
        z-index: 1056 !important;
    }
  </style>

  <!-- Modal -->
  <div class="modal" id="fileManagerModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="fileManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="fileManagerModalLabel">Gerenciador de Arquivos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body p-0">
          <iframe id="fileManagerIframe" src="" style="width: 100%; height: calc(100vh - 130px); border: none;" tabindex="-1"></iframe>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="<?=base_url().'assets/vendor/tinymce/tinymce.min.js'?>"></script>

  <script>
    // Variável global para armazenar o callback
    let imageCallback;

    // Função para receber a URL da imagem selecionada do iframe
    window.addEventListener('message', function(e) {
        if (e.data.mceAction === 'FileSelected') {
            if (typeof imageCallback === 'function') {
                imageCallback(e.data.url);
                imageCallback = null;
                $('#fileManagerModal').modal('hide');
            }
        }
    });

    tinymce.init({
        selector: '#content',
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
        file_picker_callback: function(cb, value, meta) {
            if (meta.filetype === 'image') {
                imageCallback = cb;
                
                // Abre nosso modal
                var fileManagerUrl = '<?= base_url() ?>gerenciador/tinyfilemanager.php?tokenrr=dfbs8fgd61vdfvdv542@ff52364';
                document.getElementById('fileManagerIframe').src = fileManagerUrl;
                
                // Força o nosso modal a ficar por cima
                $('#fileManagerModal').css('z-index', 999999);
                $('.modal-backdrop').css('z-index', 999998);
                
                // Mostra o modal
                $('#fileManagerModal').modal('show');
                
                // Previne que o modal do TinyMCE interfira
                $('.tox-dialog-wrap').hide();
            }
        },
        
        // Configurações adicionais de imagem
        image_title: true,
        image_description: false,
        image_dimensions: false,
        image_class_list: [
            {title: 'Responsiva', value: 'img-fluid'}
        ],
        
        // Permite upload direto de imagens
        automatic_uploads: true
    });

    // Previne problemas de foco
    document.getElementById('fileManagerModal').addEventListener('shown.bs.modal', function (e) {
        e.preventDefault();
        setTimeout(function() {
            document.getElementById('fileManagerIframe').focus();
        }, 100);
    });
  </script>

</main><!-- End #main -->
