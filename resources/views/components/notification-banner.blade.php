@auth

    <div
        x-data="notificationBanner"
        x-show="state !== 'granted'"
        class="mx-4 my-4 p-4 border border-amber-400/40 bg-amber-500/10 rounded text-sm">

        <p class="font-semibold mb-1">Enable Notifications</p>

        <p class="text-xs text-amber-100/80 mb-3">
            We can send you notifications when there's new field activity.
        </p>

        <div class="flex items-center gap-2">
            <button
                @click="request()"
                class="px-3 py-1.5 rounded border border-amber-300/70 text-xs font-semibold hover:bg-amber-300/10"
                x-text="state === 'default' ? 'Do you want to enable notifications?' : 'Try Again'"
            >
            </button>

            <span class="text-[11px] text-amber-100/70" x-text="message"></span>
        </div>

    </div>

@endauth
