/*
 * Customer logo slider (components/customers.blade.php). The logos are listed
 * twice in a native scroller; whenever it reaches the second set it steps back
 * one set's width, which looks identical, so it never runs out.
 */
const AUTO_DELAY = 2800;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

document.querySelectorAll('[data-customers]').forEach((root) => {
    const list = root.querySelector('[data-customers-list]');
    const items = [...list.children];
    const count = items.length / 2;
    let target = null; // where a running animation is heading, so quick clicks add up
    let settle = null;
    let inView = false;

    const step = () => items[0].getBoundingClientRect().width;
    const current = () => target ?? Math.round(list.scrollLeft / step());

    const finish = () => {
        clearTimeout(settle);
        target = null;
        if (list.scrollLeft >= step() * count - 1) list.scrollLeft -= step() * count;
    };

    const move = (direction) => {
        let from = current();

        if (direction < 0 && from <= 0) {
            list.scrollLeft += step() * count;
            from += count;
        } else if (direction > 0 && from >= count) {
            list.scrollLeft -= step() * count;
            from -= count;
        }

        target = from + direction;
        list.scrollTo({ left: target * step(), behavior: reduceMotion ? 'auto' : 'smooth' });
        clearTimeout(settle);
        settle = setTimeout(finish, 900);
    };

    // The instant step back or forward one set also ends a scroll, so wait for the real target.
    list.addEventListener('scrollend', () => {
        if (target === null || Math.abs(list.scrollLeft - target * step()) < 2) finish();
    });
    root.querySelector('[data-customers-prev]').addEventListener('click', () => move(-1));
    root.querySelector('[data-customers-next]').addEventListener('click', () => move(1));

    new IntersectionObserver(([entry]) => {
        inView = entry.isIntersecting;
        // Load the logos waiting off to the side, so they don't pop in while sliding.
        if (inView) list.querySelectorAll('img').forEach((img) => (img.loading = 'eager'));
    }).observe(root);

    if (reduceMotion) return;

    setInterval(() => {
        const paused = document.hidden || root.matches(':hover') || root.querySelector(':focus-visible');
        if (inView && !paused) move(1);
    }, AUTO_DELAY);
});
