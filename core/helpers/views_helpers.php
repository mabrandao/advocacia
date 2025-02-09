<?php

class ViewsHelpers
{
    /**
     * Gera uma tabela DataTable com processamento server-side
     * 
     * @param string $url URL para requisição AJAX
     * @param array $campos Array com os campos a serem exibidos
     * @return array Array com HTML da tabela e script de inicialização
     */
    public static function ajaxDataTables($url, $campos)
    {
        // Configurações padrão
        $config = [
            'processing' => true,
            'serverSide' => true,
            'ajax' => [
                'url' => base_url() . $url,
                'type' => 'POST',
                'data' => 'function(d) { return d; }'
            ],
            'columns' => [],
            'language' => [
                'url' => base_url() . 'assets/vendor/datatables/pt-BR.json'
            ],
            'pageLength' => 25,
            'dom' => "<'row text-center mb-3'<'col-sm-12 col-md-6 text-center'l><'col-sm-12 col-md-6 text-end'f>>" .
                    "<'row'<'col-sm-12'tr>>" .
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7 d-flex justify-content-end'p>>" .
                    "<'row text-center mt-3'<'col-12'B>>",
            'buttons' => [
                [
                    'extend' => 'copy',
                    'text' => '<i class="bi bi-clipboard"></i> Copiar',
                    'className' => 'btn btn-secondary'
                ],
                [
                    'extend' => 'excel',
                    'text' => '<i class="bi bi-file-earmark-excel"></i> Excel',
                    'className' => 'btn btn-success'
                ],
                [
                    'extend' => 'pdf',
                    'text' => '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    'className' => 'btn btn-danger'
                ],
                [
                    'extend' => 'print',
                    'text' => '<i class="bi bi-printer"></i> Imprimir',
                    'className' => 'btn btn-info'
                ]
            ]
        ];
        
        // Gera as colunas baseado nos campos informados
        foreach ($campos as $coluna => $rotulo) {
            $config['columns'][] = [
                'data' => $coluna,  // Nome da coluna no banco de dados (sem acento)
                'title' => $rotulo, // Rótulo para exibição (pode ter acento)
                'orderable' => true,
                'searchable' => true
            ];
        }

        // Adiciona a coluna de ações
        $config['columns'][] = [
            'title' => 'Ações',
            'data' => 'acoes',
            'orderable' => false,
            'searchable' => false,            
        ];

        // Gera o HTML da tabela
        $table = '<table id="datatable" class="table table-striped table-sm table-hover table-bordered">';
        $table .= '<thead><tr>';
        foreach ($config['columns'] as $column) {
            $table .= '<th class="text-center">' . $column['title'] . '</th>';
        }
        $table .= '</tr></thead>';
        $table .= '<tbody></tbody>';
        $table .= '</table>';

        // Gera o script de inicialização
        $script = '
        <script>
            $(document).ready(function() {
                $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "'. $config['ajax']['url'] . '",
                        type: "'. $config['ajax']['type'] . '",
                        data: function(d) {
                            return d;
                        }
                    },
                    columns: ' . json_encode($config['columns']) . ',
                    language: {
                        url:  "'. $config['language']['url'] . '"
                    },
                    pageLength: ' . $config['pageLength'] . ',
                    dom: "' . $config['dom'] . '",
                    buttons: ' . json_encode($config['buttons']) . ',
                    initComplete: function(settings, json) {
                        // Callback após inicialização
                    },
                    drawCallback: function(settings) {
                        // Callback após cada redesenho
                    },
                    error: function(xhr, error, thrown) {
                        console.error("Erro no DataTable:", error);
                    }
                });
            });
        </script>';

        return [
            'table' => $table,
            'script' => $script
        ];
    }

    public static function creatForm($param)
    {
        // Verifica se os parâmetros necessários foram passados
        if (!isset($param['action']) || !isset($param['fields'])) {
            return 'Parâmetros action e fields são obrigatórios';
        }

        $hasFileManager = false;
        $form = '';

        // Verifica se tem campos que usam o gerenciador de arquivos
        foreach ($param['fields'] as $field) {
            if (isset($field['type']) && $field['type'] === 'input-group') {
                $hasFileManager = true;
                break;
            }
        }

        // Adiciona os scripts do gerenciador de arquivos se necessário
        if ($hasFileManager) {
            $form .= '
            <script src="'.base_url().'gerenciador/retornar_caminho.js"></script>
            <script>var fileManagerUrl = "'.base_url().'gerenciador/tinyfilemanager.php?tokenrr=dfbs8fgd61vdfvdv542@ff52364";</script>
            <script src="'.base_url().'assets/js/filemanager-input.js"></script>
            
            <!-- Modal do Gerenciador de Arquivos -->
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
            
            <style>
                .modal-backdrop { z-index: 1055 !important; }
                #fileManagerModal { z-index: 1056 !important; }
                .image-preview { 
                    max-width: 200px;
                    margin-top: 10px;
                    display: none;
                }
                .image-preview img {
                    width: 100%;
                    height: auto;
                    border-radius: 4px;
                    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                }
            </style>';
        }

        $hasTextarea = false;
        foreach ($param['fields'] as $field) {
            if (isset($field['type']) && $field['type'] === 'textarea') {
                $hasTextarea = true;
                break;
            }
        }

        if ($hasTextarea) {
            $form .= '
            <!-- TinyMCE e plugins -->
            <script src="'.base_url().'assets/vendor/tinymce/tinymce.min.js"></script>
            <script src="'.base_url().'assets/js/tinymce-chatgpt.js"></script>
            <script>var fileManagerUrl = "'.base_url().'gerenciador/tinyfilemanager.php?tokenrr=dfbs8fgd61vdfvdv542@ff52364";</script>
            <script src="'.base_url().'assets/js/tinymce-filemanager.js"></script>
            <script src="'.base_url().'assets/js/tinymce-init.js"></script>

            <!-- Modal do Gerenciador de Arquivos -->
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
            </div>';
        }

        $form .= '<form action="'.base_url().$param['action'].'" method="POST" enctype="multipart/form-data">';
        
        if (isset($param['hidden'])) {
            foreach ($param['hidden'] as $name => $value) {
                $form .= '<input type="hidden" name="'.$name.'" value="'.$value.'">';
            }
        }

        $form .= '<div class="row">';
        foreach ($param['fields'] as $field) {
            // Verifica se os campos obrigatórios existem
            if (!isset($field['name']) || !isset($field['type']) || !isset($field['label'])) {
                continue;
            }

            $class = isset($field['class']) ? $field['class'] : '';
            $value = isset($field['value']) ? $field['value'] : '';
            $required = isset($field['required']) && $field['required'] ? 'required="true"' : '';
            $placeholder = isset($field['placeholder']) ? 'placeholder="'.$field['placeholder'].'"' : '';
            $col = isset($field['col']) ? $field['col'] : 'col-md-6';

            $form .= '<div class="'.$col.'">';
            $form .= '<div class="form-group my-2">';
            $form .= '<label for="'.$field['name'].'" class="form-label">'.$field['label'].'</label>';

            switch ($field['type']) {
                case 'textarea':
                    $form .= '<textarea class="form-control '.$class.'" 
                        id="'.$field['name'].'" name="'.$field['name'].'" 
                        '.$required.' '.$placeholder.' rows="5">'.$value.'</textarea>';
                    break;

                case 'select':
                    $form .= '<select class="form-control '.$class.'" id="'.$field['name'].'" 
                        name="'.$field['name'].'" '.$required.'>';
                    if (isset($field['options'])) {
                        foreach ($field['options'] as $key => $option) {
                            $selected = ($value == $key) ? 'selected' : '';
                            $form .= '<option value="'.$key.'" '.$selected.'>'.$option.'</option>';
                        }
                    }
                    $form .= '</select>';
                    break;

                case 'radio':
                case 'checkbox':
                    if (isset($field['options'])) {
                        foreach ($field['options'] as $key => $option) {
                            $checked = ($value == $key) ? 'checked' : '';
                            $form .= '<div class="form-check">';
                            $form .= '<input class="form-check-input '.$class.'" type="'.$field['type'].'" 
                                id="'.$field['name'].'_'.$key.'" name="'.$field['name'].'" value="'.$key.'" '.$checked.' '.$required.'>';
                            $form .= '<label class="form-check-label" for="'.$field['name'].'_'.$key.'">'.$option.'</label>';
                            $form .= '</div>';
                        }
                    }
                    break;
                case 'input-group':
                    $inputId = isset($field['id']) ? $field['id'] : $field['name'];
                    $form .= '<div class="input-group">
                                <div class="input-group-prepend">
                                    <button class="btn btn-secondary btn-file-manager" type="button" data-target="'.$inputId.'">
                                        <i class="bi bi-folder2-open"></i> Selecione
                                    </button>
                                </div>
                                <input type="text" class="form-control '.$class.' input-file-preview" 
                                    id="'.$inputId.'" name="'.$field['name'].'" value="'.$value.'" 
                                    '.$required.' '.$placeholder.'>
                            </div>
                            <div class="image-preview">
                                <img id="'.$inputId.'_preview" src="<?=$base_url()?>" alt="Prévia da imagem">
                            </div>';
                    break;

                default:
                    $form .= '<input type="'.$field['type'].'" class="form-control '.$class.'" 
                        id="'.$field['name'].'" name="'.$field['name'].'" value="'.$value.'" 
                        '.$required.' '.$placeholder.'>';
            }

            $form .= '</div></div>';
        }
        $form .= '</div>';

        // Extrai o nome base da URL removendo sufixos e IDs
        $back = preg_replace('/-(?:editar|cadastrar|store|edit)(?:\/\d+)?$/', '', $param['action']);
        
        // Extrai o sufixo usando expressão regular, ignorando o ID se presente
        if (preg_match('/-?(editar|cadastrar|store|edit)(?:\/\d+)?$/', $param['action'], $matches)) {
            $sufix = $matches[1];
        } else {
            $sufix = '';
        }


        switch($sufix) {
            case 'editar':
                $buttonText = 'Atualizar';
                break;
            case 'edit':
                $buttonText = 'Atualizar';
                break;
            case 'cadastrar':
                $buttonText = 'Salvar';
                break;
            case 'store':
                $buttonText = 'Salvar';
                break;
            default:
                $buttonText = 'Salvar';
        }

        // Botões do formulário
        $form .= '<div class="row mt-3">';
        $form .= '<div class="col-12">';
        
        if (isset($param['buttons']) && is_array($param['buttons'])) {
            foreach ($param['buttons'] as $button) {
                extract($button);
                $form .= '<button type="'.$type.'" class="btn '.$class.' me-2">'.$text.'</button>';
            }
        } else {
            $form .= '<button type="submit" class="btn btn-primary me-2">'.$buttonText.'</button>';
            $form .= '<a href="'.base_url().$back.'" class="btn btn-secondary">Voltar</a>';
        }
        $form .= '</div></div>';

        $form .= '</form>';
        return $form;
    }

    /**
     * exemplo de uso da função creatForm:
     * 
     * $param = [
     *      'action' => 'admin/categorias/editar',
     *      'fields' => [
     *          [
     *              'name' => 'nome',
     *              'type' => 'text',
     *              'label' => 'Nome',
     *              'class' => 'form-control',
     *              'value' => $categoria->nome,
     *              'required' => true
     *          ],
     *          [
     *              'name' => 'descricao',
     *              'type' => 'textarea',
     *              'label' => 'Descrição',
     *              'class' => 'form-control',
     *              'value' => $categoria->descricao,
     *              'required' => true
     *          ]
     *      ],
     *      'buttons' => [
     *          [
     *              'type' => 'submit',
     *              'class' => 'btn-primary',
     *              'text' => 'Salvar'
     *          ],
     *          [
     *              'type' => 'button',
     *              'class' => 'btn-secondary',
     *              'text' => 'Voltar'
     *          ]
     *      ]
     *  ];
     * 
     * echo $this->creatForm($param);
     */

    public function ConvertDate($data){
        $data = date('d/m/Y', strtotime($data));
        return $data;
    }

    public function ConvertDateTime($data){
        $data = date('d/m/Y H:i:s', strtotime($data));
        return $data;
    }

    public function ConvertTime($data){
        $data = date('H:i:s', strtotime($data));
        return $data;
    }
}