/*
 * Feeds the pointer position into cards with .card-hover or .spotlight so
 * their ::before glow (see app.css) follows the cursor. One delegated
 * listener covers every card, including ones added later.
 */
const SELECTOR = '.card-hover, .spotlight';

if (window.matchMedia('(hover: hover)').matches) {
    document.addEventListener('pointermove', (event) => {
        const card = event.target instanceof Element ? event.target.closest(SELECTOR) : null;
        if (!card) return;

        const rect = card.getBoundingClientRect();
        card.style.setProperty('--spot-x', `${event.clientX - rect.left}px`);
        card.style.setProperty('--spot-y', `${event.clientY - rect.top}px`);
    }, { passive: true });
}
