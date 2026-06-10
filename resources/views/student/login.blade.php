@extends('layouts.app')

@section('title', 'Student Login')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold mb-6 text-center">Student Login</h2>
    <form method="POST" action="{{ route('student.login') }}">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Student ID</label>
            <input type="text" name="student_id" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Password</label>
            <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
        </div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Login</button>
        <p class="mt-4 text-center text-gray-600">No account? <a href="{{ route('student.register') }}" class="text-blue-600 hover:underline">Register</a></p>
    </form>
</div>
@endsection