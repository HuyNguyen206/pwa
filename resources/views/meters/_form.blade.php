@props(['meter' => null])
@csrf
<div class="grid gap-3 md:grid-cols-2">
  <div>
    <label class="block text-sm">Code</label>
    <input name="code" value="{{ old('code',$meter->code ?? '') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700" required>
    @error('code')<p class="text-red-400 text-sm">{{ $message }}</p>@enderror
  </div>
  <div>
    <label class="block text-sm">Name</label>
    <input name="name" value="{{ old('name',$meter->name ?? '') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700" required>
    @error('name')<p class="text-red-400 text-sm">{{ $message }}</p>@enderror
  </div>
  <div>
    <label class="block text-sm">Unit</label>
    <input name="unit" value="{{ old('unit',$meter->unit ?? 'bbl') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700">
  </div>
  <div>
    <label class="block text-sm">Latitude</label>
    <input name="location_lat" type="number" step="0.000001" value="{{ old('location_lat',$meter->location_lat ?? '') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700">
  </div>
  <div>
    <label class="block text-sm">Longitude</label>
    <input name="location_lng" type="number" step="0.000001" value="{{ old('location_lng',$meter->location_lng ?? '') }}" class="w-full p-2 rounded-md bg-gray-800/40 border border-gray-700">
  </div>
</div>
