tinymce.PluginManager.add('chatgpt', function(editor) {
    // Adiciona o botão à barra de ferramentas
    editor.ui.registry.addButton('chatgpt', {
        text: 'ChatGPT',
        icon: 'bot',
        onAction: async function () {
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

                // Obter o conteúdo atual do editor
                const currentContent = editor.getContent({format: 'text'});
                
                // Obter categoria e título
                const categoriaSelect = document.querySelector('select[name="categoria"]');
                const categoria = categoriaSelect ? categoriaSelect.value : '';
                const titulo = document.querySelector('input[name="titulo"]')?.value || '';

                // Mostrar indicador de carregamento
                editor.setProgressState(true);

                // Preparar prompt para o ChatGPT
                let prompt = '';
                if (currentContent.trim()) {
                    prompt = `Como editor jurídico especializado, por favor, revise e melhore o seguinte texto para uma matéria jurídica${categoria ? ' na categoria ' + categoria : ''}${titulo ? ' com o título "' + titulo + '"' : ''}:\n\n${currentContent}\n\nPor favor, mantenha um tom profissional e adequado para publicação em um portal jurídico, garantindo precisão técnica e clareza na comunicação.`;
                } else {
                    prompt = `Como editor jurídico especializado, por favor, ajude-me a criar uma matéria jurídica${categoria ? ' na categoria ' + categoria : ''}${titulo ? ' com o título "' + titulo + '"' : ''}. A matéria deve ser informativa, profissional e adequada para publicação em um portal jurídico.`;
                }

                // Fazer a chamada para a API do ChatGPT
                const response = await fetch('https://api.openai.com/v1/chat/completions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${apiKey}`
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
                const newContent = data.choices[0].message.content;

                // Atualizar o conteúdo do editor
                editor.setContent(newContent);
                editor.setProgressState(false);

            } catch (error) {
                editor.setProgressState(false);
                editor.notificationManager.open({
                    text: 'Erro ao processar requisição: ' + error.message,
                    type: 'error'
                });
            }
        }
    });

    // Adiciona um ícone personalizado para o botão
    editor.ui.registry.addIcon('bot', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/></svg>');

    return {
        getMetadata: function () {
            return {
                name: 'ChatGPT Plugin',
                url: 'https://seu-site.com/chatgpt-plugin'
            };
        }
    };
});
