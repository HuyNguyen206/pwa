
const urlBase64ToUint8Array = function (base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
        .replace(/-/g, '+')
        .replace(/_/g, '/');

    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);

    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }

    return outputArray;
};
document.addEventListener('alpine:init', () => {
    Alpine.data('notificationBanner', () => {
        return {
            message: '',
            state: 'default',
            subscribed: false,

            async init() {
                if (!('Notification' in window)) {
                    this.state = 'unsupported';
                    this.message = 'This browser does not support notifications';
                    return;
                }

                this.state = Notification.permission; // 'default', 'granted', 'denied'

                if (this.state === 'denied') {
                    this.message = 'You blocked notifications in your browser settings.';
                }

                if (this.state !== 'granted') {
                    return;
                }

                const registration = await navigator.serviceWorker.ready;
                const existing = await registration.pushManager.getSubscription();

                this.subscribed = !!existing
            },

            async request() {
                try {
                    this.state = await Notification.requestPermission();

                    if (this.state === 'granted') {
                        this.message = `Thanks! We'll notify you of new activity`;
                    } else if (this.state === 'denied') {
                        this.message = 'You blocked notifications in your browser settings';
                    }
                } catch (e) {
                    console.log(e);
                    this.message = 'something went wrong requesting permission';
                }
            },

            async subscribe() {
                try {
                    const registration = await navigator.serviceWorker.ready;
                    const existing = await registration.pushManager.getSubscription();

                    if (existing) {
                        this.subscribed = true;
                        this.message = 'You are already subscribed to notifications';

                        return;
                    }

                    const vapidKey = document.querySelector('meta[name="vapid-public-key"]')?.content;

                    console.log('New vapidKey:', vapidKey);

                    const applicationServerKey = urlBase64ToUint8Array(vapidKey)

                    console.log('New applicationKey:', applicationServerKey);

                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey
                    });

                    console.log('New subscription:', subscription);

                    await this.storeSubscription(subscription);

                    console.log('Done subscribing:');


                    this.subscribed = true;
                    this.message = 'You are now subscribed to notifications';


                } catch (e) {
                    console.error(e);
                    this.message = 'Unable to subscribe to notifications';
                }
            },

            async setupPushNotifications() {
                await this.request();

                if (this.state === 'granted') {
                    await this.subscribe();
                }

            },


            async storeSubscription(subscription) {

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                await fetch('/push-subscriptions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(subscription)
                });

            }
        };
    });
});
