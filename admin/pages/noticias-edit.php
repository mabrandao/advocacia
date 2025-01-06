<main id="main" class="main">
    <div class="pagetitle">
      <h1><?= isset($titulo) ? $titulo : "" ?> Notícia</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?=base_url()?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?=base_url()?>admin/noticias">Notícias</a></li>
          <li class="breadcrumb-item active"><?= isset($titulo) ? $titulo : "" ?></li>
        </ol>
      </nav>
    </div>

    <section class="section">
      <div class="card">
        <div class="card-body">
          <?php 
          $id = isset($noticia['id']) ? $noticia['id'] : null;
          $urc = is_null($id) ? "" : "/".$id;
          $param = [
            'action' => "admin/noticias-".$tipo.$urc,
            'hidden' => [
              'id' => $id
            ],
            'fields' => [
              [
                'name' => 'categoria',
                'type' => 'select',
                'label' => 'Categoria da Notícia',
                'class' => 'form-control', 
                'value' => isset($noticia["categoria"]) ? $noticia["categoria"] : "",               
                'required' => true,
                'col' => 'col-md-4',
                'options' => [
                  '' => "-- Selecione --",
                  'Política' => 'Política',
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
                'required' => true,
                'col' => 'col-md-8',
                'placeholder' => 'Digite o títutlo da noticia aqui...'
              ],
              [
                'name' => 'image',
                'type' => 'input-group',
                'label' => 'Imagem Principal',
                'class' => 'form-control',
                'value' => isset($noticia["image"]) ? $noticia["image"] : "",
                'required' => true
              ],               
              [
                'name' => 'galeria',
                'type' => 'input-group',
                'label' => 'Galeria de Imagens',
                'class' => 'form-control',
                'value' => isset($noticia["galeria"]) ? $noticia["galeria"] : "",
                'required' => false
              ],
              [
                'name' => 'content',
                'type' => 'textarea',
                'label' => 'Conteúdo da Notícia',
                'class' => 'form-control editor',
                'value' => isset($noticia["content"]) ? $noticia["content"] : "",
                'required' => true,
                'col' => 'col-md-12',
                'id' => 'content',
                'placeholder' => 'Digite o conteúdo da notícia aqui...'
              ]
            ]
          ];
          
          echo ViewsHelpers::creatForm($param);
          ?>
        </div>
      </div>
    </section>
</main>
