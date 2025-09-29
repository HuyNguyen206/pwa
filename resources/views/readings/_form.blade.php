@props(['meter','reading' => null])
@csrf
<div class="grid gap-3 md:grid-cols-3">
  <div>
    <label class="block text-sm">Value</label>
    <input name="value" type="number" step="0.001" value="{{ old('value', $reading->value ?? '') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700" required>
  </div>
  <div>
    <label class="block text-sm">When</label>
    <input name="noted_at" type="datetime-local" value="{{ old('noted_at', optional($reading->noted_at ?? now())->format('Y-m-d\\TH:i')) }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700" required>
  </div>
  <div>
    <label class="block text-sm">Notes</label>
    <input name="notes" value="{{ old('notes', $reading->notes ?? '') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700">
  </div>
</div>
