import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

document.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-track-url]');
    if (!link) return;
    fetch(link.dataset.trackUrl, { method: 'GET', keepalive: true });
});
