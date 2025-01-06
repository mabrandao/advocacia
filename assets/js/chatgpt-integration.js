class ChatGPTIntegration {
    constructor(apiKey) {
        this.apiKey = apiKey;
        this.baseUrl = 'https://api.openai.com/v1/chat/completions';
    }

    async generateResponse(prompt, context = 'geral') {
        try {
            const response = await fetch(this.baseUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.apiKey}`
                },
                body: JSON.stringify({
                    model: 'gpt-4',
                    messages: [{
                        role: 'user',
                        content: prompt
                    }],
                    temperature: 0.7
                })
            });

            const data = await response.json();
            return data.choices[0].message.content;
        } catch (error) {
            console.error('Erro ao chamar ChatGPT:', error);
            throw error;
        }
    }
}

// Função para adicionar botão ChatGPT aos textareas
function addChatGPTToTextareas() {
    const textareas = document.querySelectorAll('textarea');
    
    textareas.forEach(textarea => {
        // Detectar contexto baseado na página atual
        const isNoticiaPage = window.location.href.includes('noticias');
        const pageContext = isNoticiaPage ? 'noticias' : 'geral';

        // Criar wrapper div
        const wrapper = document.createElement('div');
        wrapper.className = 'textarea-wrapper position-relative';
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(textarea);

        // Adicionar botão
        const button = document.createElement('button');
        button.className = 'btn btn-primary chatgpt-btn';
        button.innerHTML = '<i class="bi bi-robot"></i> ChatGPT';
        button.style.position = 'absolute';
        button.style.right = '10px';
        button.style.top = '10px';
        wrapper.appendChild(button);

        // Adicionar evento de clique
        button.addEventListener('click', async () => {
            try {
                const apiKey = localStorage.getItem('chatgpt_api_key');
                if (!apiKey) {
                    const key = prompt('Por favor, insira sua chave API do ChatGPT:');
                    if (key) {
                        localStorage.setItem('chatgpt_api_key', key);
                    } else {
                        return;
                    }
                }

                const chatGPT = new ChatGPTIntegration(apiKey);
                const currentText = textarea.value;
                
                // Mostrar loading
                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processando...';

                // Preparar prompt baseado no contexto
                let prompt = '';
                if (pageContext === 'noticias') {
                    // Obter categoria selecionada
                    const categoriaSelect = document.querySelector('select[name="categoria"]');
                    const categoria = categoriaSelect ? categoriaSelect.value : '';
                    const titulo = document.querySelector('input[name="titulo"]')?.value || '';

                    if (currentText) {
                        prompt = `Como editor jurídico especializado, por favor, revise e melhore o seguinte texto para uma matéria jurídica${categoria ? ' na categoria ' + categoria : ''}${titulo ? ' com o título "' + titulo + '"' : ''}:\n\n${currentText}\n\nPor favor, mantenha um tom profissional e adequado para publicação em um portal jurídico, garantindo precisão técnica e clareza na comunicação.`;
                    } else {
                        prompt = `Como editor jurídico especializado, por favor, ajude-me a criar uma matéria jurídica${categoria ? ' na categoria ' + categoria : ''}${titulo ? ' com o título "' + titulo + '"' : ''}. A matéria deve ser informativa, profissional e adequada para publicação em um portal jurídico.`;
                    }
                } else {
                    if (currentText) {
                        prompt = 'Por favor, ajude-me a melhorar o seguinte texto para um contexto jurídico:\n\n' + currentText;
                    } else {
                        prompt = 'Por favor, me ajude a escrever um texto jurídico profissional.';
                    }
                }

                const response = await chatGPT.generateResponse(prompt, pageContext);
                textarea.value = response;

                // Restaurar botão
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-robot"></i> ChatGPT';
            } catch (error) {
                alert('Erro ao processar requisição: ' + error.message);
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-robot"></i> ChatGPT';
            }
        });
    });
}

// Adicionar estilos CSS
const style = document.createElement('style');
style.textContent = `
    .textarea-wrapper {
        position: relative;
        margin-bottom: 1rem;
    }
    .chatgpt-btn {
        opacity: 0.7;
        transition: opacity 0.3s;
    }
    .chatgpt-btn:hover {
        opacity: 1;
    }
`;
document.head.appendChild(style);

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', addChatGPTToTextareas);
