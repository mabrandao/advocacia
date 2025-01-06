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

// Inicializa o TinyMCE em todos os textareas com classe 'editor'
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: 'textarea.editor',
        height: 500,
        language: 'pt_BR',
        license_key: 'gpl',
        theme: 'silver',
        promotion: false,
        filemanager_url: fileManagerUrl,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'emoticons',
            'fullscreen', 'insertdatetime', 'media', 'nonbreaking',
            'pagebreak', 'preview', 'save', 'searchreplace', 'visualblocks',
            'visualchars', 'wordcount', 'code', 'chatgpt', 'filemanager'
        ],
        toolbar: [
            'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | outdent indent | bullist numlist | link image filemanager media | charmap emoticons | fullscreen preview | code chatgpt',
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
        automatic_uploads: true,

        // Evento de setup para garantir que o modal funcione corretamente
        setup: function(editor) {
            editor.on('init', function() {
                // Previne problemas de foco no modal
                document.getElementById('fileManagerModal')?.addEventListener('shown.bs.modal', function (e) {
                    e.preventDefault();
                    setTimeout(function() {
                        document.getElementById('fileManagerIframe').focus();
                    }, 100);
                });                
            });
            editor.on('change', function() {
                // Atualiza o textarea original
                editor.save();
                // Dispara evento de mudança para validação
                const textarea = document.getElementById(editor.id);
                if (textarea) {
                    textarea.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
        }
    });
});
