@props(['title' => config('app.name')])
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title }}</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-full bg-gray-950 text-gray-100">
  <header class="px-4 py-3 border-b border-white/10 flex items-center justify-between">
    <h1 class="font-semibold">
      <a href="{{ route('meters.index') }}">Field Logger</a>
    </h1>
    <form method="POST" action="{{ route('logout') }}">@csrf
      <button class="px-3 py-2 rounded-md bg-white/10 hover:bg-white/20">Logout</button>
    </form>
  </header>
  <main class="p-4">
    @if(session('status'))
      <div class="mb-4 p-2 rounded-md bg-emerald-700/30 border border-emerald-700">{{ session('status') }}</div>
    @endif
    {{ $slot }}
  </main>
</body>
</html>
