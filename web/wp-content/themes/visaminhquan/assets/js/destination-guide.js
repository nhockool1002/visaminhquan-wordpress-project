(function () {
    'use strict';

    function initDestinationGuide(root) {
        if (!root) return;

        const items = root.querySelectorAll('.vmq-dg-item');
        items.forEach(function (item) {
            const trigger = item.querySelector('.vmq-dg-item__trigger');
            const panel = item.querySelector('.vmq-dg-item__panel');
            if (!trigger) return;

            const hasPanel = !!panel;
            item.classList.toggle('has-children', hasPanel);
            trigger.setAttribute('aria-expanded', 'false');
            if (panel) {
                panel.hidden = true;
            }

            trigger.addEventListener('click', function (event) {
                const link = event.target.closest('a');
                if (link) return;

                if (!hasPanel || !panel) {
                    return;
                }

                event.preventDefault();
                const isOpen = item.classList.contains('is-open');
                item.classList.toggle('is-open', !isOpen);
                trigger.setAttribute('aria-expanded', String(!isOpen));
                panel.hidden = isOpen;
            });
        });
    }

    function boot() {
        const guides = document.querySelectorAll('[data-vmq-destination-guide]');
        guides.forEach(initDestinationGuide);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
