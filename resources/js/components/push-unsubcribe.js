document.addEventListener('alpine:init', () => {
    Alpine.data('pushUnsubscribe', () => {
        return {
            async disable() {
                if (!('Notification' in window)) {
                    alert('This browser does not support notifications');
                    return;
                }

                try {
                    const registration = await navigator.serviceWorker.ready;
                    const subscription = await registration.pushManager.getSubscription();

                    if (!subscription) {
                        alert('You are not subscribed to notifications');
                        return;
                    }

                    const endpoint = subscription.endpoint
                    const unsubbed = await subscription.unsubscribe()

                    if (!unsubbed) {
                        console.warn(`Failed to unsubscribe from ${endpoint} in client side`);
                    }

                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const res = await fetch('/push-subscriptions', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            endpoint: endpoint
                        }),
                        credentials: 'same-origin'
                    });

                    alert('You have been unsubscribed from notifications in this device');
                } catch (error) {
                    console.error('Error occurred while unsubscribing:', error);
                    alert('An error occurred while unsubscribing from notifications');
                }
            }

        };
    })
});
