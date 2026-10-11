async function registerSync() {
    if ('serviwceWorker' in navigator && 'SyncManager' in window) {
        try {

        } catch (error) {

        }
    }
}

document.addEventListener('alpine:init', () => {
    Alpine.data('readingForm', () => {
        return {
            online: navigator.online,

            init() {
                window.addEventListener('online', () => this.online = true);
                window.addEventListener('offline', () => this.online = false);
            },

            submitForm(event) {
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
                    method: this.$el_method?.value ?? this.$el.method,
                    payload,
                    createdAt: new Date().toISOString()
                }

                //save to index database
                //register a service worker to handle the background sync

                if (item.method.toLowerCase() === 'post') {
                    this.$el.reset();
                }





            }
        }
    })
})

