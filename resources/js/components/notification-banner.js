document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBanner', () => {
        return {
            message: '',
            state: 'default',

            init() {
                if (!('Notification' in window)) {
                    this.state = 'unsupported';
                    this.message = 'This browser does not support notifications';
                    return;
                }

                this.state = Notification.permission; // 'default', 'granted', 'denied'

                if (this.state === 'denied') {
                    this.message = 'You blocked notifications in your browser settings.';
                }
            },
            async request() {
                try {
                    const result = await Notification.requestPermission();
                    this.state = result;

                    if (this.state === 'granted') {
                        this.message = `Thanks! We'll notify you of new activity`;
                    } else if (this.state === 'denied') {
                        this.message = 'You blocked notifications in your browser settings';
                    }
                } catch (e) {
                    console.log(e);
                    this.message = 'something went wrong requesting permission';
                }
            }
        };
    });
});
