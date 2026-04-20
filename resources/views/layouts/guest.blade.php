<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="auth-shell">
            <div class="auth-wrapper">
                <div class="auth-grid">
                    <aside class="auth-brand">
                        <div>
                            <a href="/" class="auth-logo">
                                <x-application-logo class="auth-logo-mark" />
                                <span class="auth-logo-copy">
                                    <span class="auth-logo-name">SolarPV</span>
                                    <span class="auth-logo-tag">Smart Energy System</span>
                                </span>
                            </a>

                            <p class="auth-kicker">SolarPV Platform</p>
                            <h1 class="auth-title">A solar-powered interface inspired by panels, light, and engineering clarity.</h1>
                            <p class="auth-description">
                                Access your workspace through a visual system shaped around photovoltaic grids, control surfaces, and warm solar energy accents.
                            </p>

                            <div class="auth-highlights">
                                <div class="auth-highlight">
                                    Track installations, production logic, and solar project workflows in one focused professional environment.
                                </div>
                                <div class="auth-highlight">
                                    Panel-inspired textures, darker surfaces, and sun-toned highlights designed to match the SolarPV project identity.
                                </div>
                            </div>
                        </div>

                        <div class="text-sm text-slate-300/90">
                            Reliable tools for engineers, users, and solar energy decisions.
                        </div>
                    </aside>

                    <main class="auth-panel">
                        <div class="auth-panel-inner">
                            <div class="auth-panel-header">
                                <p class="auth-panel-kicker">Secure Access</p>
                                <h2 class="auth-panel-title">Welcome back</h2>
                                <p class="auth-panel-copy">
                                    Sign in to continue managing your photovoltaic projects, reports, and solar studies.
                                </p>
                            </div>

                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
