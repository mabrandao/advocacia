tinymce.PluginManager.add('filemanager', function(editor) {
    editor.ui.registry.addButton('filemanager', {
        text: 'Gerenciador de Arquivos',
        icon: 'browse',
        onAction: function () {
            // Usa a variável global fileManagerUrl
            document.getElementById('fileManagerIframe').src = fileManagerUrl;
            
            // Força o nosso modal a ficar por cima
            $('#fileManagerModal').css('z-index', 999999);
            $('.modal-backdrop').css('z-index', 999998);
            
            // Configura o callback
            window.imageCallback = function(url) {
                editor.insertContent('<img src="' + url + '" class="img-fluid" />');
                $('#fileManagerModal').modal('hide');
            };
            
            // Mostra o modal
            $('#fileManagerModal').modal('show');
            
            // Previne que o modal do TinyMCE interfira
            $('.tox-dialog-wrap').hide();
        }
    });

    return {
        getMetadata: function () {
            return {
                name: 'File Manager Plugin',
                url: window.location.origin
            };
        }
    };
});
