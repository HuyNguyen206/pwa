<x-layout title="New Meter">
  <h2 class="text-lg font-semibold mb-3">Create Meter</h2>
  <form method="POST" action="{{ route('meters.store') }}" class="space-y-3">
    @include('meters._form', ['meter' => null])
    <button class="px-3 py-2 rounded-md bg-emerald-600 hover:bg-emerald-500">Save</button>
  </form>
</x-layout>
