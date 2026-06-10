Hello {{ $user->first_name ?? $user->name }}!

Welcome to {{ config('app.name') }}!

Your account has been created.

Student ID: {{ $user->student_id }}
Email: {{ $user->email }}

Login here: {{ url('/') }}

Thank you!