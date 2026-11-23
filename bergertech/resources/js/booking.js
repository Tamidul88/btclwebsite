/*
 * Calendly style time picker: the first click on a time splits it into the
 * selected time and a "Next" button. Without JS each time links straight to
 * the details step, so this is purely an enhancement.
 */
const list = document.querySelector('[data-slots]');

const ACTIVE = ['bg-slate-600', 'border-slate-600', 'text-white'];
const IDLE = ['text-brand-600', 'border-brand-600/40'];

if (list) {
    list.addEventListener('click', (event) => {
        const time = event.target.closest('[data-slot-time]');
        if (!time) return;

        event.preventDefault();

        list.querySelectorAll('[data-slot]').forEach((slot) => {
            const isPicked = slot.contains(time);
            const slotTime = slot.querySelector('[data-slot-time]');

            slotTime.classList.remove(...(isPicked ? IDLE : ACTIVE));
            slotTime.classList.add(...(isPicked ? ACTIVE : IDLE));
            slot.querySelector('[data-slot-next]').classList.toggle('hidden', !isPicked);
        });

        time.parentElement.querySelector('[data-slot-next]').focus();
    });
}
