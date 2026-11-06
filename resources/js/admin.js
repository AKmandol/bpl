import Modal from 'bootstrap/js/dist/modal.js';

document.addEventListener('DOMContentLoaded', () => {
    const modalToOpen = document.querySelector('[data-open-on-load]');

    if (modalToOpen) {
        Modal.getOrCreateInstance(modalToOpen).show();
    }
});
