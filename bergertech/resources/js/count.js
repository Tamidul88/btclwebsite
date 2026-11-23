/*
 * Counts a figure up from zero the first time it scrolls into view:
 *   <dd data-count="12" data-suffix="+">12+</dd>
 * The final value is already in the markup, so without JS (or with
 * reduced motion) nothing changes.
 */
const figures = document.querySelectorAll('[data-count]');
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const DURATION = 1400;
const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

const run = (el) => {
    const target = Number(el.dataset.count);
    const suffix = el.dataset.suffix ?? '';
    const start = performance.now();

    const tick = (now) => {
        const progress = Math.min((now - start) / DURATION, 1);
        el.textContent = `${Math.round(target * easeOutCubic(progress))}${suffix}`;
        if (progress < 1) requestAnimationFrame(tick);
    };

    requestAnimationFrame(tick);
};

if (figures.length && !reduceMotion && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            observer.unobserve(entry.target);
            run(entry.target);
        });
    }, { threshold: 0.6 });

    figures.forEach((el) => observer.observe(el));
}
