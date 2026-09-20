<x-layout :title="$meter->name">
    <div class="flex space-y-2 justify-between">
        <div>
            <div><span class="opacity-60">Code:</span> {{ $meter->code }}</div>
            <div><span class="opacity-60">Unit:</span> {{ $meter->unit }}</div>
            <div><span class="opacity-60">Location:</span> {{ $meter->location_lat ?? '—' }}, {{ $meter->location_lng ?? '—' }}</div>

        </div>
        <img src="{{ asset('images/meter-' . $meter->id . '.png') }}"
             width="64" height="64"
             class="object-contain block"
        />
    </div>

    <hr class="my-4 border-white/10">

    <h3 class="font-semibold mb-2">Add Reading</h3>
    <form method="POST" action="{{ route('meters.readings.store', $meter) }}" class="space-y-3">
        @include('readings._form', ['meter' => $meter])
        <button class="px-3 py-2 rounded-md bg-emerald-600 hover:bg-emerald-500">Add</button>
    </form>

    <h3 class="font-semibold mt-6 mb-2">History</h3>
    <ul class="space-y-2">
        @forelse($meter->readings()->latest('noted_at')->get() as $r)
            <li class="p-3 bg-white/5 rounded-lg flex items-center justify-between">
                <div>
                    <div class="font-medium opacity-75">{{ $r->value }} {{ $meter->unit }} — {{ $r->notes }}</div>
                    <div class="text-sm">{{ $r->noted_at->format('Y-m-d H:i') }}</div>
                </div>
                <div class="space-x-2">
                    <a class="px-3 py-2 rounded-md bg-sky-600 hover:bg-sky-500" href="{{ route('meters.readings.edit', [$meter, $r]) }}">Edit</a>
                    <form method="POST" action="{{ route('meters.readings.destroy', [$meter, $r]) }}" class="inline">
                        @csrf @method('DELETE')
                        <button class="px-3 py-2 rounded-md bg-rose-700 hover:bg-rose-600" onclick="return confirm('Delete this reading?')">Delete</button>
                    </form>
                </div>
            </li>
        @empty
            <li>No readings yet.</li>
        @endforelse
    </ul>
</x-layout>
