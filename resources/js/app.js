import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.store('net', {
        offline: null,
        lastChangedAt: null,

        init() {
            this.offline = !navigator.onLine;

            window.addEventListener('online', () => this.setStatus(false));
            window.addEventListener('offline', () => this.setStatus(true));
        },

        setStatus(value) {
            this.offline = value;
            this.lastChangedAt = (new Date());
        }

    });
})
