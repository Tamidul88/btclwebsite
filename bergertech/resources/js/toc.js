/*
 * Marks the "On this page" link for the section being read with
 * data-active. A section counts as being read while any part of it sits in
 * a band just below the header; when several do, the first one wins.
 */
const links = [...document.querySelectorAll('[data-toc-link]')];
const sections = [...document.querySelectorAll('[data-toc-section]')];

if (links.length && sections.length && 'IntersectionObserver' in window) {
    const visible = new Set();

    const setActive = (id) => {
        links.forEach((link) => link.toggleAttribute('data-active', link.getAttribute('href') === `#${id}`));
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                visible.add(entry.target.id);
            } else {
                visible.delete(entry.target.id);
            }
        });

        const current = sections.find((section) => visible.has(section.id));
        if (current) setActive(current.id);
    }, { rootMargin: '-20% 0px -65% 0px' });

    sections.forEach((section) => observer.observe(section));
}
