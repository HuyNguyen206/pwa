<x-layout title="Login">
  <div class="max-w-sm mx-auto mt-16 p-6 rounded-2xl shadow bg-white/5">
    <form method="POST" action="{{ route('login') }}" x-data>
      @csrf
      <h1 class="text-xl font-semibold mb-4">Sign in</h1>
      <label class="block text-sm">Email</label>
      <input name="email" type="email" class="w-full mt-1 mb-3 p-2 rounded bg-gray-800/40 border border-gray-700" required>
      <label class="block text-sm">Password</label>
      <input name="password" type="password" class="w-full mt-1 mb-6 p-2 rounded bg-gray-800/40 border border-gray-700" required>
      @error('email')<p class="text-red-400 text-sm mb-2">{{ $message }}</p>@enderror
      <button class="w-full py-2 rounded-2xl bg-blue-600 hover:bg-blue-500">Login</button>
    </form>
  </div>
</x-layout>