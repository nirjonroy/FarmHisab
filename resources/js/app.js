import './bootstrap';
import '../css/app.css';
import * as bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js';

window.bootstrap = bootstrap;

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.app-dropdown > [data-bs-toggle="dropdown"]').forEach((toggle) => {
        toggle.addEventListener('click', (event) => {
            if (window.bootstrap?.Dropdown) {
                return;
            }

            event.preventDefault();
            const menu = toggle.parentElement.querySelector('.app-dropdown-menu');

            document.querySelectorAll('.app-dropdown-menu.show').forEach((openMenu) => {
                if (openMenu !== menu) {
                    openMenu.classList.remove('show');
                    openMenu.previousElementSibling?.setAttribute('aria-expanded', 'false');
                }
            });

            menu?.classList.toggle('show');
            toggle.setAttribute('aria-expanded', menu?.classList.contains('show') ? 'true' : 'false');
        });
    });

    document.addEventListener('click', (event) => {
        if (event.target.closest('.app-dropdown')) {
            return;
        }

        document.querySelectorAll('.app-dropdown-menu.show').forEach((menu) => {
            menu.classList.remove('show');
            menu.previousElementSibling?.setAttribute('aria-expanded', 'false');
        });
    });
});
