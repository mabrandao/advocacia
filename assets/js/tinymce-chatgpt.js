tinymce.PluginManager.add('chatgpt', function(editor) {
    editor.ui.registry.addButton('chatgpt', {
        text: 'AI Editor',
        icon: 'chatgpt',
        onAction: function () {
            editor.windowManager.open({
                title: 'AI Editor - Digite seu prompt',
                body: {
                    type: 'panel',
                    items: [{
                        type: 'textarea',
                        name: 'prompt',
                        label: 'Prompt',
                        placeholder: 'Digite aqui sua instrução para a IA...'
                    }]
                },
                buttons: [
                    {
                        type: 'cancel',
                        text: 'Cancelar'
                    },
                    {
                        type: 'submit',
                        text: 'Enviar',
                        primary: true
                    }
                ],
                onSubmit: async function (api) {
                    try {
                        const prompt = api.getData().prompt;
                        
                        if (!prompt.trim()) {
                            editor.notificationManager.open({
                                text: 'Por favor, digite um prompt.',
                                type: 'error',
                                timeout: 3000
                            });
                            return;
                        }

                        api.close();
                        editor.setProgressState(true);

                        const response = await fetch('/advocacia/api/openai-proxy.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                messages: [{
                                    role: 'user',
                                    content: prompt
                                }]
                            })
                        });

                        const data = await response.json();

                        if (!response.ok || data.error) {
                            const errorMessage = data.error?.message || `Erro ${response.status}: ${response.statusText}`;
                            console.error('Erro detalhado:', data);
                            throw new Error(errorMessage);
                        }

                        if (!data.choices?.[0]?.message?.content) {
                            console.error('Resposta inválida:', data);
                            throw new Error('Resposta inválida do servidor');
                        }

                        editor.setContent(data.choices[0].message.content);
                        editor.setProgressState(false);

                    } catch (error) {
                        editor.setProgressState(false);
                        editor.notificationManager.open({
                            text: 'Erro ao processar requisição: ' + error.message,
                            type: 'error',
                            timeout: 5000
                        });
                    }
                }
            });
        }
    });

    editor.ui.registry.addIcon('chatgpt', '<svg width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/></svg>');

    return {
        getMetadata: function () {
            return {
                name: 'AI Editor Plugin',
                url: 'https://seu-site.com/ai-editor-plugin'
            };
        }
    };
});
