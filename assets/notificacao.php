<?php
// Verifica se a sessão está disponível
if (!isset($session)) {
    global $session;
}

?>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

<style>
    .toastify {
        background: unset !important;
        padding: 0 !important;
        box-shadow: none !important;
    }
    .toastify .alert {
        margin: 0;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        min-width: 300px;
    }
    .toastify .progress {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
    }
</style>

<script>
    function showToast(message, type = 'info') {
        if (!message) return;
        
        let icon = '';
        let classe = '';
        let tipo = '';
        
        switch(type) {
            case 'success':
                icon = '<i class="fa fa-check-circle"></i>';
                classe = "alert-success";
                tipo = "Sucesso";
                break;
            case 'error':
                icon = '<i class="fa fa-times-circle"></i>';
                classe = "alert-danger";
                tipo = "Erro";
                break;
            case 'warning':
                icon = '<i class="fa fa-exclamation-triangle"></i>';
                classe = "alert-warning";
                tipo = "Atenção";
                break;
            case 'info':
                icon = '<i class="fa fa-info-circle"></i>';
                classe = "alert-info";
                tipo = "Informação";
                break;
            default:
                icon = '<i class="fa fa-info-circle"></i>';
                classe = "alert-secondary";
                tipo = "Veja";
        }

        const toastContent = document.createElement('div');
        toastContent.innerHTML = `
            <div class="text-center alert ${classe}">  
                <div class="row justify-content-center">
                    <div class="col-md-auto align-self-center justify-content-center">
                        <h3 class="m-0 p-0">${icon}</h3>  
                    </div>
                    <div class="col-md">
                        <h4 class="alert-heading m-0">${tipo}</h4>
                        <hr class="dropdown-divider">
                        <span class="m-0" style="font-size: 18px; font-weight: bold">${message}</span>
                    </div>
                </div>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        `;
        
        const toast = Toastify({
            node: toastContent,
            duration: 5000,
            gravity: "top",
            position: "right",
            className: 'custom-toast',
            stopOnFocus: true,            
            offset: {
                x: 100,
                y: 100
            },            
            onClick: function(){} // Callback after click
        }).showToast();

        // Animar a barra de progresso
        const progressBar = toastContent.querySelector('.progress-bar');
        const duration = 5000; // mesma duração do toast
        const interval = 50; // atualizar a cada 50ms
        const steps = duration / interval;
        let progress = 0;
        
        const progressInterval = setInterval(() => {
            progress += (100 / steps);
            if (progress >= 100) {
                clearInterval(progressInterval);
            } else {
                progressBar.style.width = progress + '%';
                progressBar.setAttribute('aria-valuenow', progress);
            }
        }, interval);
    }

    // Verificar e exibir mensagens flash
    const messages = {
        success: '<?php echo $session->getFlash("success") ?? ""; ?>',
        error: '<?php echo $session->getFlash("error") ?? ""; ?>',        
        warning: '<?php echo $session->getFlash("warning") ?? ""; ?>',
        info: '<?php echo $session->getFlash("info") ?? ""; ?>',
        default: '<?php echo $session->getFlash("default") ?? ""; ?>'
    };

    // Função para exibir mensagens
    function showMessages() {
        // Primeiro mostra mensagem principal se houver
        for (const [type, message] of Object.entries(messages)) {
            if (message && message.trim() !== '') {
                showToast(message, type);
                break;
            }
        }

    }

    // Executar quando a página carregar
    window.addEventListener('load', showMessages);
</script>