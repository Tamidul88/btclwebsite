/* Fade sections in as they enter the viewport. */
const targets = document.querySelectorAll('.reveal');

if (!targets.length) {
    // nothing to do
} else if (!('IntersectionObserver' in window)) {
    targets.forEach((el) => el.classList.add('is-visible'));
} else {
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                // Drop the stagger once in place, so hover effects respond instantly.
                setTimeout(() => entry.target.style.removeProperty('--reveal-delay'), 1200);
                observer.unobserve(entry.target);
            });
        },
        { rootMargin: '0px 0px -10% 0px', threshold: 0.08 }
    );

    targets.forEach((el) => {
        // Stagger cards that share a parent: 0, 80, 160ms… capped so long grids don't drag.
        const siblings = [...el.parentElement.children].filter((c) => c.classList.contains('reveal'));
        el.style.setProperty('--reveal-delay', `${Math.min(siblings.indexOf(el), 5) * 80}ms`);
        observer.observe(el);
    });
}
