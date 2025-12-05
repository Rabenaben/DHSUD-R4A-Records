import './bootstrap';
import 'flowbite';
import './rem';
import './accounts';
import './hoa';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Toast functionality
class Toast {
    constructor() {
        this.config = {
            success: {
                topBarClass: 'bg-green-600',
                iconClass: 'bi bi-check-circle mr-3 text-green-600 text-xl'
            },
            error: {
                topBarClass: 'bg-red-600',
                iconClass: 'bi bi-x-circle mr-3 text-red-600 text-xl'
            },
            default: {
                topBarClass: 'bg-gray-300',
                iconClass: 'bi bi-info-circle mr-3 text-gray-600 text-xl'
            }
        };
    }

    show(message, type = 'default') {
        const toast = document.getElementById('toast');
        const toastContent = document.getElementById('toast-content');
        const toastIcon = document.getElementById('toast-icon');
        const toastMessage = document.getElementById('toast-message');

        // Remove any existing top bar to avoid duplicates
        const existingBar = toastContent.querySelector('.toast-top-bar');
        if (existingBar) existingBar.remove();

        // Create top colored bar div
        const topBar = document.createElement('div');
        topBar.className = `toast-top-bar h-1 w-full rounded-t-lg ${this.config[type].topBarClass}`;

        // Insert the top bar as the very first child inside toastContent
        toastContent.prepend(topBar);

        // Set icon and message
        toastMessage.textContent = message;
        toastIcon.className = this.config[type].iconClass;
        toastContent.className = 'relative flex flex-col p-0 rounded-lg shadow-lg bg-white text-black border border-gray-200 overflow-hidden';

        // Now create or update a container inside toastContent for icon + message
        let messageContainer = toastContent.querySelector('.toast-message-container');
        if (!messageContainer) {
            messageContainer = document.createElement('div');
            messageContainer.className = 'flex items-center p-4';
            toastContent.appendChild(messageContainer);
        } else {
            messageContainer.innerHTML = ''; // clear old content
        }

        // Append icon and message inside messageContainer
        messageContainer.appendChild(toastIcon);
        messageContainer.appendChild(toastMessage);

        // Show toast with slide-in animation
        toast.classList.remove('hidden', 'translate-x-full');
        toast.classList.add('translate-x-0');

        // Auto-hide after 3 seconds with slide-out animation
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.classList.add('hidden'), 300);
        }, 3000);
    }
}

window.showToast = (message, type) => new Toast().show(message, type);

Alpine.start();
