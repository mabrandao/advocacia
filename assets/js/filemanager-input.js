// Função para receber a URL da imagem selecionada do iframe para inputs
window.addEventListener('message', function(e) {
    baseUrl = "http://localhost/advocacia" //window.location.protocol + '//' + window.location.host;
    console.log('Mensagem recebida:', e.data); // Debug
    
    // Verifica se é uma mensagem do gerenciador de arquivos
    if (e.data.mceAction === 'FileSelected' && e.data.url) {
        // Pega o input ativo
        const activeInput = document.querySelector('[data-filemanager-active="true"]');
        console.log('Input ativo:', activeInput); // Debug
        
        if (activeInput) {
            console.log('Atualizando input com URL:', e.data.url); // Debug
            
            // Atualiza o valor do input
            activeInput.value = e.data.url;
            
            // Remove o marcador de ativo
            activeInput.removeAttribute('data-filemanager-active');
            
            // Atualiza a prévia da imagem
            const previewImg = document.getElementById(activeInput.id + '_preview');
            if (previewImg) {
                previewImg.src = baseUrl + e.data.url;
                previewImg.parentElement.style.display = 'block';
            }
            
            // Fecha o modal usando Bootstrap 5
            const modal = document.getElementById('fileManagerModal');
            if (modal) {
                const modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        }
    }
});

// Inicializa os botões do gerenciador de arquivos
document.addEventListener('DOMContentLoaded', function() {
    console.log('Inicializando gerenciador de arquivos'); // Debug
    
    // Para cada botão de arquivo
    document.querySelectorAll('.btn-file-manager').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Botão clicado:', this.dataset.target); // Debug
            
            // Pega o input relacionado
            const input = document.getElementById(this.dataset.target);
            console.log('Input encontrado:', input); // Debug
            
            if (input) {
                // Remove o marcador de qualquer outro input ativo
                document.querySelectorAll('[data-filemanager-active="true"]').forEach(el => {
                    el.removeAttribute('data-filemanager-active');
                });
                
                // Marca o input como ativo
                input.setAttribute('data-filemanager-active', 'true');
                
                // Abre o modal do gerenciador
                const iframe = document.getElementById('fileManagerIframe');
                if (iframe) {
                    iframe.src = fileManagerUrl;
                }
                
                const modal = document.getElementById('fileManagerModal');
                if (modal) {
                    const modalInstance = new bootstrap.Modal(modal);
                    modalInstance.show();
                }
            }
        });
    });

    // Para cada input com prévia de imagem
    document.querySelectorAll('.input-file-preview').forEach(input => {
        // Se já tiver valor, mostra a prévia
        if (input.value) {
            const previewImg = document.getElementById(input.id + '_preview');
            if (previewImg) {
                previewImg.src = input.value;
                previewImg.parentElement.style.display = 'block';
            }
        }
    });
});
