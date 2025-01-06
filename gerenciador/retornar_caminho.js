function retornar_caminho(url) {
    // Verifica se há um input ativo no parent
    const hasActiveInput = window.parent.document.querySelector('[data-filemanager-active="true"]');
    
    if (hasActiveInput) {
        // Para input, vamos usar caminho relativo a partir de /assets/
        let relativePath = url;
        
        // Se a URL for absoluta, extrai apenas a parte após /assets/
        const assetsMatch = url.match(/\/assets\/.+/);
        if (assetsMatch) {
            relativePath = assetsMatch[0];
        }
        
        console.log('Enviando caminho relativo:', relativePath); // Debug
        
        window.parent.postMessage({
            mceAction: 'FileSelected',
            url: relativePath
        }, '*');
        return;
    }
    
    // Para o TinyMCE, mantém a URL absoluta
    if (!url.startsWith('http')) {
        var base = window.location.protocol + '//' + window.location.host;
        if (!url.startsWith('/')) {
            url = '/' + url;
        }
        url = base + url;
    }
    
    console.log('Enviando URL absoluta:', url); // Debug
    
    // Se não tiver input ativo, assume que é o TinyMCE
    if (window.parent.imageCallback) {
        window.parent.imageCallback(url);
    }
}
