/*
 * "What's happening" gallery (components/whats-happening.blade.php).
 * Clicking a strip, an arrow, or pressing ←/→ on a strip makes it the active
 * card. Each panel's data-distance (0 = active) drives its width in app.css.
 */
const MAX_DISTANCE = 4;

document.querySelectorAll('[data-happening]').forEach((root) => {
    const panels = [...root.querySelectorAll('[data-happening-panel]')];
    const captions = [...root.querySelectorAll('[data-happening-caption]')];
    const prev = root.querySelector('[data-happening-prev]');
    const next = root.querySelector('[data-happening-next]');
    let active = 0;

    const show = (index) => {
        active = Math.max(0, Math.min(index, panels.length - 1));

        panels.forEach((panel, i) => {
            panel.dataset.distance = String(Math.min(Math.abs(i - active), MAX_DISTANCE));
            panel.classList.toggle('is-active', i === active);
            panel.setAttribute('aria-pressed', String(i === active));
        });

        captions.forEach((caption, i) => {
            caption.hidden = i !== active;
        });

        prev.disabled = active === 0;
        next.disabled = active === panels.length - 1;
    };

    panels.forEach((panel, i) => {
        panel.addEventListener('click', () => show(i));
        panel.addEventListener('keydown', (event) => {
            if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
            event.preventDefault();
            show(active + (event.key === 'ArrowRight' ? 1 : -1));
            panels[active].focus();
        });
    });

    prev.addEventListener('click', () => show(active - 1));
    next.addEventListener('click', () => show(active + 1));
});
