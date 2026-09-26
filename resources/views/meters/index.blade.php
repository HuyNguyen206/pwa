<x-layout title="Meters">
  <div x-cloak x-show="$store.net.confirmOnline" class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold">Meters</h2>
    <a class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-500" href="{{ route('meters.create') }}">New Meter</a>
  </div>

  <ul class="space-y-2">
    @forelse($meters as $m)
      <li class="p-3 bg-white/5 rounded-lg flex items-center justify-between">
        <div>
          <div class="font-medium">{{ $m->name }} <span class="opacity-60">({{ $m->code }})</span></div>
          <div class="text-sm opacity-75">Readings: {{ $m->readings_count }}</div>
        </div>
        <div x-cloak x-show="$store.net.confirmOnline" class="space-x-2">
          <a class="px-3 py-2 rounded-md bg-blue-600 hover:bg-blue-500" href="{{ route('meters.show',$m) }}">View</a>
          <a class="px-3 py-2 rounded-md bg-sky-600 hover:bg-sky-500" href="{{ route('meters.edit',$m) }}">Edit</a>
          <form method="POST" action="{{ route('meters.destroy',$m) }}" class="inline">
            @csrf @method('DELETE')
            <button class="px-3 py-2 rounded-md bg-rose-700 hover:bg-rose-600" onclick="return confirm('Delete this meter?')">Delete</button>
          </form>
        </div>
      </li>
    @empty
      <li>No meters.</li>
    @endforelse
  </ul>

  <div class="mt-4">{{ $meters->links() }}</div>
</x-layout>
