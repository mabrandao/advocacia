<?php

class ViewsHelpers
{
    /**
     * Gera uma tabela DataTable com processamento server-side
     * 
     * @param array $config Configurações da tabela
     *      - columns: Array com definições das colunas [
     *          'title' => título da coluna,
     *          'data' => nome do campo no banco/resposta,
     *          'render' => função JS para renderizar o conteúdo (opcional),
     *          'searchable' => boolean se permite busca (opcional),
     *          'orderable' => boolean se permite ordenação (opcional)
     *      ]
     *      - url: URL para requisição AJAX
     *      - id: ID da tabela (opcional, default: datatable)
     *      - class: Classes CSS adicionais (opcional)
     *      - language: URL do arquivo de tradução (opcional)
     *      - buttons: Array com botões para exportação (opcional)
     *      - order: Array com ordem inicial (opcional)
     *      - pageLength: Registros por página (opcional)
     * @return array Array com HTML da tabela e script de inicialização
     */
    public function ajaxDataTables($config)
    {
        // Configurações padrão
        $defaults = [
            'id' => 'datatable',
            'class' => 'table table-striped',
            'language' => 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Portuguese-Brasil.json',
            'buttons' => ['copy', 'csv', 'excel', 'pdf', 'print'],
            'pageLength' => 10,
            'order' => [[0, 'desc']]
        ];

        // Mescla configurações padrão com as fornecidas
        $config = array_merge($defaults, $config);

        // Gera cabeçalho da tabela
        $headers = '';
        foreach ($config['columns'] as $column) {
            $headers .= "<th>{$column['title']}</th>";
        }

        // Gera HTML da tabela
        $table = "<table id=\"{$config['id']}\" class=\"{$config['class']}\" style=\"width:100%\">
                    <thead>
                        <tr>{$headers}</tr>
                    </thead>
                    <tbody></tbody>
                </table>";

        // Gera definições das colunas para o DataTables
        $columnDefs = [];
        foreach ($config['columns'] as $i => $column) {
            $def = [
                'data' => $column['data'],
                'name' => $column['data'],
                'targets' => $i
            ];

            if (isset($column['render'])) {
                $def['render'] = $column['render'];
            }
            if (isset($column['searchable'])) {
                $def['searchable'] = $column['searchable'];
            }
            if (isset($column['orderable'])) {
                $def['orderable'] = $column['orderable'];
            }

            $columnDefs[] = $def;
        }

        // Gera script de inicialização
        $script = "<script>
            $(document).ready(function() {
                $('#{$config['id']}').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: '{$config['url']}',
                        type: 'POST',
                        data: function(d) {
                            // Adiciona dados extras se necessário
                            return d;
                        }
                    },
                    columnDefs: " . json_encode($columnDefs) . ",
                    order: " . json_encode($config['order']) . ",
                    pageLength: {$config['pageLength']},
                    language: {
                        url: '{$config['language']}'
                    },
                    dom: 'Bfrtip',
                    buttons: " . json_encode($config['buttons']) . ",
                    initComplete: function(settings, json) {
                        // Callback após inicialização
                    },
                    drawCallback: function(settings) {
                        // Callback após cada redesenho
                    },
                    error: function(xhr, error, thrown) {
                        console.error('Erro no DataTable:', error);
                    }
                });
            });
        </script>";

        return [
            'tabela' => $table,
            'script' => $script
        ];
    }

    /**
     * Exemplo de uso:
     * 
     * $config = [
     *     'columns' => [
     *         [
     *             'title' => 'ID',
     *             'data' => 'id'
     *         ],
     *         [
     *             'title' => 'Nome',
     *             'data' => 'nome',
     *             'render' => 'function(data, type, row) {
     *                 return '<a href="/view/'+row.id+'">'+data+'</a>';
     *             }'
     *         ],
     *         [
     *             'title' => 'Ações',
     *             'data' => null,
     *             'render' => 'function(data, type, row) {
     *                 return '<button onclick="editar('+row.id+')">Editar</button>';
     *             }',
     *             'orderable': false,
     *             'searchable': false
     *         ]
     *     ],
     *     'url' => '/api/dados',
     *     'id' => 'minha-tabela',
     *     'class' => 'table table-hover',
     *     'buttons' => ['excel', 'pdf'],
     *     'order' => [[1, 'asc']],
     *     'pageLength' => 25
     * ];
     * 
     * $datatable = $viewHelper->ajaxDataTables($config);
     * echo $datatable['tabela'];
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