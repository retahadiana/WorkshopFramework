@extends('layout.master')

@section('title','Login')

@section('content')
    <div class="max-w-md mx-auto">
        <h2 class="text-2xl font-medium mb-4">Login</h2>

        @if($errors->any())
            <div class="mb-4 text-red-600">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-3">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" class="w-full px-3 py-2 border rounded" required>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-[#1b1b18] text-white rounded">Login</button>
                <a href="/" class="text-sm text-muted">Back</a>
            </div>
        </form>
    </div>
@endsection
