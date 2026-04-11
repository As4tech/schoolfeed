@php use Illuminate\Support\Facades\Storage; @endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $schoolName ?? config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="text-center mb-8">
                @if($schoolLogo)
                    <a href="{{ route('login', ['school' => $school->slug]) }}">
                        <img src="{{ Storage::url($schoolLogo) }}" alt="{{ $schoolName }}" class="w-24 h-24 mx-auto mb-4 rounded-lg shadow-md">
                    </a>
                @else
                    <a href="{{ $school ? route('login', ['school' => $school->slug]) : '/' }}">
                        <x-application-logo class="w-20 h-20 mx-auto mb-4 fill-current text-gray-500" />
                    </a>
                @endif
                
                @if($schoolName)
                    <h1 class="text-2xl font-bold text-gray-800">{{ $schoolName }}</h1>
                    <p class="text-sm text-gray-600 mt-1">Sign in to your account</p>
                @endif
            </div>

            <div class="w-full sm:max-w-md px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
            
            @if($school)
                <div class="mt-6 text-center">
                    <a href="{{ route('login', ['school' => $school->slug]) }}" class="text-sm text-gray-600 hover:text-gray-900">
                        © {{ date('Y') }} {{ $schoolName }}
                    </a>
                </div>
            @endif
        </div>
    </body>
</html>
