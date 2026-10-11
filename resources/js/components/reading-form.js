import {saveToOutbox} from "../storage/outbox.js";

async function registerSync() {
    if ('serviceWorker' in navigator && 'SyncManager' in window) {
        try {
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register('sync-outbox');
        } catch (error) {
            console.warn('Background sync registration failed:', error);
        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('readingForm', () => {
        return {
            online: navigator.onLine,

            init() {
                window.addEventListener('online', () => this.online = true);
                window.addEventListener('offline', () => this.online = false);
            },

            async submitForm(event) {
                if (this.online) {
                    return;
                }

                event.preventDefault();
                const formData = new FormData(this.$el);
                const payload = {
                    value: formData.get('value'),
                    note_at: formData.get('note_at'),
                    notes: formData.get('notes'),
                }

                const item = {
                    id: crypto.randomUUID(),
                    endpoint: this.$el.action,
                    method: this.$el._method?.value ?? this.$el.method,
                    payload,
                    createdAt: new Date().toISOString()
                }

                //save to index database
                //register a service worker to handle the background sync
                await saveToOutbox(item);
                await registerSync();

                if (item.method.toLowerCase() === 'post') {
                    this.$el.reset();
                }

            }
        }
    })
})

