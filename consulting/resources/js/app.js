/*
 * Alpine's CSP-safe build.
 *
 * The standard build compiles x-on / x-bind expressions with new Function(),
 * which would force 'unsafe-eval' into the site-wide Content-Security-Policy.
 * The CSP build does not, at the cost of requiring behaviour to be declared
 * through Alpine.data() rather than as inline expressions — which is the
 * trade made deliberately here. See config/security.php.
 */
import Alpine from '@alpinejs/csp';

/*
 * The panel is shown by toggling a class, never x-show.
 *
 * x-show works by writing style="display: none" onto the element, and the
 * site's Content-Security-Policy sets style-src without 'unsafe-inline' — the
 * browser drops that attribute and the menu would be stuck open. Toggling a
 * class touches classList instead, which CSP does not police.
 */
Alpine.data('mobileNav', () => ({
    open: false,
    get panelClass() {
        return this.open ? '' : 'hidden';
    },
    toggle() {
        this.open = !this.open;
    },
    close() {
        this.open = false;
    },
}));

Alpine.data('disclosure', () => ({
    open: false,
    toggle() {
        this.open = !this.open;
    },
}));

window.Alpine = Alpine;
Alpine.start();
