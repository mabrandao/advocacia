<main id="main" class="main">

  <div class="pagetitle">
      <h1><?= isset($title) ? $title : "" ?> Noticias</h1>
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
            <h3 class="card-title"><?= isset($title) ? $title : "" ?> Notícia</h3>
              </div>
          <?php 
         
          $param = [
           'action' => "admin/noticias-".$tipo,
           'hidden' => [
               'id' => isset($noticia['id']) ? $noticia['id'] : ''
           ],
           'fields' => [
               [
                   'name' => 'slug',
                   'type' => 'hidden',
                  
                   'class' => 'form-control',
                   'value' => isset($noticia["slug"]) ? $noticia["slug"] : "",
                   'required' => true                  
               ],
               [
                   'name' => 'categoria',
                   'type' => 'select',
                   'label' => 'Categoria da Notícia',
                   'class' => 'form-control',
                   'value' => isset($noticia["categoria"]) ? $noticia["categoria"] : "",
                   'required' => true,
                   'options' => [
                       'Politica' => 'Política',
                       'Economia' => 'Economia',
                       'Cidade' => 'Cidade',
                       'Oeste' => 'Oeste'
                   ]
               ],
               [
                   'name' => 'titulo',
                   'type' => 'text',
                   'label' => 'Título da Notícia',
                   'class' => 'form-control',
                   'value' => isset($noticia["titulo"]) ? $noticia["titulo"] : "",
                   'required' => true
               ],
               [
                   'name' => 'image',
                   'type' => 'file',
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
                   'required' => false,
                   'multiple' => false
               ],
               [
                   'name' => 'content',
                   'type' => 'textarea',
                   'label' => 'Conteúdo da Notícia',
                   'class' => 'form-control editor', 
                   'value' => isset($noticia["content"]) ? $noticia["content"] : "",
                   'required' => true,
                   'col' => 'col-md-12',
                   'id' => 'content'
               ]
           ],
           'buttons' => [
               [
                   'type' => 'submit',
                   'class' => 'btn-primary',
                   'text' => $tipo == 'store' ? 'Cadastrar' : 'Atualizar'
               ],
               [
                   'type' => 'button',
                   'class' => 'btn-secondary',
                   'text' => 'Voltar'
               ]
           ]
       ];
      
      echo ViewsHelpers::creatForm($param);
       ?>
      </div>
    </div>
  </section>

</main><!-- End #main -->
