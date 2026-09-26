import './bootstrap';
import './components/notification-banner.js';

document.addEventListener('alpine:init', () => {
    Alpine.store('net', {
        offline: !navigator.onLine,
        confirmOnline: navigator.onLine,
        lastChangedAt: null,

        init() {
            this.offline = !navigator.onLine;

            // navigator.onLine can report true on a reload while offline, so
            // confirm right away instead of waiting for the first interval tick.
            if (!this.offline) this.probe();

            setInterval(() =>
                !this.offline ? this.probe() : null, 3000);

            window.addEventListener('online', () => this.setStatus(false));
            window.addEventListener('offline', () => this.setStatus(true));
        },

        async probe() {
            try {
                const res = await fetch('/health', {cache: 'no-store'});

                this.confirmOnline = res.ok || res.status === 204;
            }
            catch {
                this.confirmOnline = false;
            }
        },

        setStatus(value) {
            this.offline = value;
            this.confirmOnline = !value;
            this.lastChangedAt = (new Date());
        }

    });
})
