<x-layout :title="'Edit Reading — ' . $meter->name">
  <h2 class="text-lg font-semibold mb-3">Edit Reading</h2>
  <form method="POST" action="{{ route('meters.readings.update', [$meter,$reading]) }}" class="space-y-3">
    @method('PUT')
    @include('readings._form', ['meter' => $meter, 'reading' => $reading])
    <button class="px-3 py-2 rounded-md bg-sky-600 hover:bg-sky-500">Update</button>
  </form>
</x-layout>
