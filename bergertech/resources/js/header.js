const header = document.getElementById('site-header');
const progress = document.getElementById('scroll-progress');
const menu = document.getElementById('mega-menu');
const toggle = document.getElementById('menu-toggle');
const close = document.getElementById('menu-close');

/* ------------------------------------------------------------------ *
 * Sticky header: full width over a hero, a floating pill once the
 * page scrolls. The styling hangs off data-scrolled in the markup.
 * ------------------------------------------------------------------ */
if (header) {
    const startsTransparent = header.dataset.transparent === 'true';

    const sync = () => {
        header.dataset.scrolled = String(!startsTransparent || window.scrollY > 40);

        if (progress) {
            const scrollable = document.documentElement.scrollHeight - window.innerHeight;
            const ratio = scrollable > 0 ? Math.min(window.scrollY / scrollable, 1) : 0;
            progress.style.transform = `scaleX(${ratio})`;
        }
    };

    sync();
    window.addEventListener('scroll', sync, { passive: true });
}

/* ------------------------------------------------------------------ *
 * Desktop dropdown panels: hover with a mouse, click or keyboard
 * otherwise. Only one is open at a time.
 * ------------------------------------------------------------------ */
// Long enough to cross the gap between a trigger and its panel.
const CLOSE_DELAY = 180;

const dropdowns = [...document.querySelectorAll('[data-dropdown]')].map((root) => ({
    root,
    trigger: root.querySelector('[data-dropdown-trigger]'),
    panel: root.querySelector('[data-dropdown-panel]'),
    timer: null,
    openedByHover: false,
}));

const isOpen = (dropdown) => dropdown.trigger.getAttribute('aria-expanded') === 'true';

const setOpen = (dropdown, open) => {
    clearTimeout(dropdown.timer);
    if (!open) dropdown.openedByHover = false;

    dropdown.trigger.setAttribute('aria-expanded', String(open));
    dropdown.panel.toggleAttribute('data-open', open);

    if (open) {
        dropdowns.forEach((other) => {
            if (other !== dropdown && isOpen(other)) setOpen(other, false);
        });
    }
};

dropdowns.forEach((dropdown) => {
    const { root, trigger, panel } = dropdown;

    root.addEventListener('pointerenter', (event) => {
        if (event.pointerType !== 'mouse') return;
        if (!isOpen(dropdown)) dropdown.openedByHover = true;
        setOpen(dropdown, true);
    });

    root.addEventListener('pointerleave', (event) => {
        if (event.pointerType !== 'mouse') return;
        dropdown.timer = setTimeout(() => setOpen(dropdown, false), CLOSE_DELAY);
    });

    // A click that lands just after a hover opened the panel keeps it open
    // rather than toggling it straight back shut.
    trigger.addEventListener('click', () => {
        if (dropdown.openedByHover) {
            dropdown.openedByHover = false;
            return;
        }
        setOpen(dropdown, !isOpen(dropdown));
    });

    root.addEventListener('focusout', (event) => {
        if (!root.contains(event.relatedTarget)) setOpen(dropdown, false);
    });

    panel.addEventListener('click', (event) => {
        if (event.target.closest('a')) setOpen(dropdown, false);
    });
});

if (dropdowns.length) {
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;

        const open = dropdowns.find(isOpen);
        if (!open) return;

        setOpen(open, false);
        open.trigger.focus();
    });

    document.addEventListener('click', (event) => {
        dropdowns.forEach((dropdown) => {
            if (isOpen(dropdown) && !dropdown.root.contains(event.target)) setOpen(dropdown, false);
        });
    });
}

/* ------------------------------------------------------------------ *
 * Full screen menu for mobile and tablet.
 * ------------------------------------------------------------------ */
if (menu && toggle) {
    const bars = {
        top: toggle.querySelector('[data-bar="top"]'),
        mid: toggle.querySelector('[data-bar="mid"]'),
        bottom: toggle.querySelector('[data-bar="bottom"]'),
    };

    let lastFocused = null;

    const setBars = (open) => {
        bars.top?.classList.toggle('translate-y-2', open);
        bars.top?.classList.toggle('rotate-45', open);
        bars.mid?.classList.toggle('opacity-0', open);
        bars.bottom?.classList.toggle('-translate-y-2', open);
        bars.bottom?.classList.toggle('-rotate-45', open);
    };

    const openMenu = () => {
        lastFocused = document.activeElement;
        menu.hidden = false;
        // Next frame, so the opacity transition has a starting value to animate from.
        requestAnimationFrame(() => menu.classList.remove('opacity-0'));
        document.body.style.overflow = 'hidden';
        toggle.setAttribute('aria-expanded', 'true');
        setBars(true);
        close?.focus();
    };

    const closeMenu = () => {
        menu.classList.add('opacity-0');
        document.body.style.overflow = '';
        toggle.setAttribute('aria-expanded', 'false');
        setBars(false);

        const finish = () => {
            menu.hidden = true;
            menu.removeEventListener('transitionend', finish);
        };
        menu.addEventListener('transitionend', finish);

        if (lastFocused instanceof HTMLElement) {
            lastFocused.focus();
        }
    };

    const isMenuOpen = () => !menu.hidden;

    toggle.addEventListener('click', () => (isMenuOpen() ? closeMenu() : openMenu()));
    close?.addEventListener('click', closeMenu);

    // Any navigation from inside the menu should close it first.
    menu.addEventListener('click', (event) => {
        if (event.target.closest('a')) closeMenu();
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isMenuOpen()) closeMenu();
    });

    // Keep focus inside the panel while it is open.
    menu.addEventListener('keydown', (event) => {
        if (event.key !== 'Tab') return;

        const focusable = menu.querySelectorAll('a[href], button:not([disabled])');
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    });
}
