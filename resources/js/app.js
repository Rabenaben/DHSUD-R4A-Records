import './bootstrap';
import 'flowbite';
import './rem';
import './accounts';
import './hoa';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Toast functionality
window.showToast = function(message, type) {
    const toast = document.getElementById('toast');
    const toastContent = document.getElementById('toast-content');
    const toastIcon = document.getElementById('toast-icon');
    const toastMessage = document.getElementById('toast-message');

    // Set message
    toastMessage.textContent = message;

    // Set icon and color based on type
    if (type === 'success') {
        toastContent.className = 'flex items-center p-4 rounded-lg shadow-lg text-white bg-green-500';
        toastIcon.className = 'bi bi-check-circle mr-3 text-xl';
    } else if (type === 'error') {
        toastContent.className = 'flex items-center p-4 rounded-lg shadow-lg text-white bg-red-500';
        toastIcon.className = 'bi bi-x-circle mr-3 text-xl';
    }

    // Show toast with slide-in animation
    toast.classList.remove('hidden');
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');

    // Auto-hide after 3 seconds with slide-out animation
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 300); // Match transition duration
    }, 3000);
};

Alpine.start();
