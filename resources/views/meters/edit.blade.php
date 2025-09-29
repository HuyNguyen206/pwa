<x-layout :title="'Edit ' . $meter->name">
  <h2 class="text-lg font-semibold mb-3">Edit Meter</h2>
  <form method="POST" action="{{ route('meters.update',$meter) }}" class="space-y-3">
    @method('PUT')
    @include('meters._form', ['meter' => $meter])
    <button class="px-3 py-2 rounded-md bg-sky-600 hover:bg-sky-500">Update</button>
  </form>
</x-layout>
