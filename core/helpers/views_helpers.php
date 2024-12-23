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
            'dom' => "<'row text-center mb-3'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" .
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
        foreach ($campos as $campo) {
            $config['columns'][] = [
                'data' => $campo,
                'title' => ucfirst($campo),
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
            'className' => 'text-center'
        ];

        // Gera o HTML da tabela
        $table = '<table id="datatable" class="table table-striped">';
        $table .= '<thead><tr>';
        foreach ($config['columns'] as $column) {
            $table .= '<th>' . $column['title'] . '</th>';
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

    /**
     * Exemplo de uso:
     * 
     * $campos = [
     *     'id',
     *     'nome',
     *     'botoes'
     * ];
     * 
     * $datatable = $viewHelper->ajaxDataTables('/api/dados', $campos);
     * echo $datatable['table'];
     * echo $datatable['script'];
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