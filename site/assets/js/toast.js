import { toast } from 'solid-toast';

export function showToast(message, type = 'info') {
    if (!message) return;
    
    const toastOptions = {
        duration: 5000,
        position: 'top-right',
    };

    switch(type) {
        case 'success':
            toast.success(message, toastOptions);
            break;
        case 'error':
            toast.error(message, toastOptions);
            break;
        case 'warning':
            toast(message, {
                ...toastOptions,
                icon: '⚠️',
                style: {
                    background: '#ffc107',
                    color: '#000'
                }
            });
            break;
        case 'info':
            toast.info(message, toastOptions);
            break;
        default:
            toast(message, toastOptions);
    }
}
