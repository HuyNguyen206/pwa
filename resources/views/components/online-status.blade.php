<div
    class="text-white text-sm px-3 py-2 transition-colors duration-300 bg-rose-700"
    x-show="$store.net.offline"
>

    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center">
            <div class="mx-3">Offline</div>
        </div>

        <span class="text-white/80 text-xs" x-show="$store.net.lastChangedAt">
      <span class="hidden sm:inline">Offline at:</span>
      <time x-text="$store.net.lastChangedAt.toLocaleTimeString()"></time>
    </span>
    </div>
</div>
