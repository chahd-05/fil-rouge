<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'SolarPV') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .solar-flow-diagram {
                position: relative;
                overflow: hidden;
                background:
                    radial-gradient(circle at 10% 8%, rgba(251, 191, 36, 0.5), transparent 22%),
                    radial-gradient(circle at 86% 16%, rgba(255, 255, 255, 0.08), transparent 16%),
                    linear-gradient(180deg, #6f7f93 0%, #59697d 38%, #51453d 72%, #382d2a 100%);
            }

            .solar-flow-diagram::after {
                content: "";
                position: absolute;
                inset: 0;
                background-image:
                    linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
                background-size: 52px 52px;
                pointer-events: none;
            }

            .solar-flow-diagram svg {
                position: relative;
                z-index: 1;
            }

            .cloud-drift-slow {
                animation: cloudDrift 22s linear infinite;
            }

            .cloud-drift-fast {
                animation: cloudDrift 14s linear infinite;
            }

            .sun-ray {
                animation: sunRayPulse 4s ease-in-out infinite;
                transform-origin: 82px 66px;
            }

            .energy-line {
                stroke-linecap: round;
                stroke-linejoin: round;
                stroke-dasharray: 10 14;
                animation: energyFlow 2.1s linear infinite;
            }

            .energy-line.delay-1 {
                animation-delay: .45s;
            }

            .energy-line.delay-2 {
                animation-delay: .9s;
            }

            .energy-line.delay-3 {
                animation-delay: 1.35s;
            }

            .sun-rotate {
                transform-origin: 82px 66px;
                animation: sunSpin 16s linear infinite;
            }

            .sun-pulse {
                transform-origin: 82px 66px;
                animation: sunPulse 3s ease-in-out infinite;
            }

            .panel-shine {
                animation: panelShine 3.8s ease-in-out infinite;
            }

            .house-glow {
                animation: houseGlow 2.8s ease-in-out infinite;
            }

            .power-dot {
                animation: powerBlink 2s ease-in-out infinite;
            }

            .power-dot.delay-1 {
                animation-delay: .6s;
            }

            .power-dot.delay-2 {
                animation-delay: 1.2s;
            }

            @keyframes energyFlow {
                from { stroke-dashoffset: 0; }
                to { stroke-dashoffset: -48; }
            }

            @keyframes cloudDrift {
                from { transform: translateX(-18px); }
                50% { transform: translateX(16px); }
                to { transform: translateX(-18px); }
            }

            @keyframes sunSpin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }

            @keyframes sunPulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.06); opacity: .88; }
            }

            @keyframes panelShine {
                0%, 100% { opacity: .18; transform: translateX(-10px); }
                50% { opacity: .5; transform: translateX(12px); }
            }

            @keyframes sunRayPulse {
                0%, 100% { opacity: .35; }
                50% { opacity: .8; }
            }

            @keyframes houseGlow {
                0%, 100% { opacity: .25; }
                50% { opacity: .6; }
            }

            @keyframes powerBlink {
                0%, 100% { opacity: .35; transform: scale(1); }
                50% { opacity: 1; transform: scale(1.2); }
            }
        </style>
    </head>
    <body class="min-h-screen bg-slate-950 text-white">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(56,189,248,0.20),transparent_28%),radial-gradient(circle_at_85%_15%,rgba(245,158,11,0.18),transparent_24%),linear-gradient(135deg,#07111f_0%,#0b1f33_45%,#102c45_100%)]"></div>
            <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(rgba(148,163,184,0.18) 1px, transparent 1px), linear-gradient(90deg, rgba(148,163,184,0.18) 1px, transparent 1px); background-size: 88px 88px;"></div>

            <div class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-8 lg:px-10">
                <header class="flex items-center justify-between">
                    <a href="/" class="inline-flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 backdrop-blur-sm">
                        <x-application-logo class="h-11 w-11" />
                        <div>
                            <div class="text-lg font-semibold uppercase tracking-[0.18em]">SolarPV</div>
                            <div class="text-xs uppercase tracking-[0.28em] text-sky-200/80">Smart Energy System</div>
                        </div>
                    </a>

                    @if (Route::has('login'))
                        <nav class="flex items-center gap-3 text-sm">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="rounded-xl border border-amber-400/30 bg-amber-400/10 px-4 py-2 font-medium text-amber-200 transition hover:bg-amber-400/20">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="rounded-xl px-4 py-2 text-slate-200 transition hover:bg-white/5">
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-white transition hover:bg-white/10">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </header>

                <main class="flex flex-1 items-center py-10">
                    <div class="grid w-full items-center gap-8 overflow-hidden rounded-[2rem] border border-white/10 bg-white/8 p-4 shadow-2xl backdrop-blur-xl lg:grid-cols-[0.95fr_1.05fr] lg:p-6">
                        <section class="rounded-[1.6rem] border border-white/10 bg-slate-950/55 p-6 backdrop-blur-sm sm:p-8">
                            <p class="text-xs font-semibold uppercase tracking-[0.34em] text-amber-300">Solar Monitoring Platform</p>
                            <h1 class="mt-4 text-4xl font-semibold leading-tight text-white sm:text-5xl">
                                Understand a small solar panel installation in one clear interface.
                            </h1>
                            <p class="mt-5 max-w-xl text-base leading-8 text-slate-300">
                                SolarPV helps users and engineers follow project data, energy logic, and installation flow through a cleaner solar-focused experience.
                            </p>

                            <div class="mt-8 grid gap-4 sm:grid-cols-3">
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-2xl font-semibold text-amber-300">01</div>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">Panels capture sunlight and convert it into DC electricity.</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-2xl font-semibold text-sky-300">02</div>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">The inverter transforms energy into usable AC power for the site.</p>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                                    <div class="text-2xl font-semibold text-emerald-300">03</div>
                                    <p class="mt-2 text-sm leading-6 text-slate-300">Production, savings, and project performance can then be tracked.</p>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-wrap gap-3">
                                <a href="{{ route('login') }}" class="rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-slate-950 transition hover:bg-amber-300">
                                    Access Platform
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="rounded-2xl border border-sky-300/20 bg-sky-400/10 px-5 py-3 text-sm font-semibold text-sky-100 transition hover:bg-sky-400/15">
                                        Create Account
                                    </a>
                                @endif
                            </div>
                        </section>

                        <section class="rounded-[1.6rem] border border-white/10 bg-slate-950/40 p-4 backdrop-blur-sm">
                            <div class="mb-4 flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.32em] text-sky-300">Animated Solar Flow</p>
                                    <h2 class="mt-2 text-2xl font-semibold text-white">How energy moves through a small solar installation</h2>
                                </div>
                                <span class="rounded-full border border-amber-300/20 bg-amber-400/10 px-3 py-1 text-xs font-medium text-amber-200">
                                    Motion View
                                </span>
                            </div>

                            <div class="solar-flow-diagram overflow-hidden rounded-[1.5rem] border border-white/10 shadow-2xl">
                                <div class="aspect-video w-full">
                                    <svg viewBox="0 0 640 360" class="h-full w-full" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Detailed animated diagram showing solar energy from the sun to solar panels, inverter, net meter, house, and utility grid.">
                                        <defs>
                                            <linearGradient id="skyGlow" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#fbbf24" stop-opacity="0.35" />
                                                <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                                            </linearGradient>
                                            <linearGradient id="panelFill" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#172554" />
                                                <stop offset="100%" stop-color="#2563eb" />
                                            </linearGradient>
                                            <linearGradient id="roofFill" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#52525b" />
                                                <stop offset="100%" stop-color="#18181b" />
                                            </linearGradient>
                                            <linearGradient id="wallFill" x1="0%" y1="0%" x2="100%" y2="100%">
                                                <stop offset="0%" stop-color="#f8fafc" />
                                                <stop offset="100%" stop-color="#dbeafe" />
                                            </linearGradient>
                                            <linearGradient id="wireYellow" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" stop-color="#fde047" />
                                                <stop offset="100%" stop-color="#f59e0b" />
                                            </linearGradient>
                                            <linearGradient id="wireBlue" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" stop-color="#67e8f9" />
                                                <stop offset="100%" stop-color="#38bdf8" />
                                            </linearGradient>
                                            <linearGradient id="wireWhite" x1="0%" y1="0%" x2="100%" y2="0%">
                                                <stop offset="0%" stop-color="#e2e8f0" />
                                                <stop offset="100%" stop-color="#ffffff" />
                                            </linearGradient>
                                            <filter id="softGlow">
                                                <feGaussianBlur stdDeviation="4" result="blur" />
                                                <feMerge>
                                                    <feMergeNode in="blur"/>
                                                    <feMergeNode in="SourceGraphic"/>
                                                </feMerge>
                                            </filter>
                                            <filter id="groundBlur">
                                                <feGaussianBlur stdDeviation="8" />
                                            </filter>
                                        </defs>

                                        <rect width="640" height="180" fill="url(#skyGlow)" />
                                        <ellipse cx="300" cy="332" rx="235" ry="18" fill="#000000" opacity="0.22" filter="url(#groundBlur)" />
                                        <polygon points="62,302 452,302 584,338 194,338" fill="#467b34" opacity="0.98" />
                                        <path d="M0 280 C130 248, 254 250, 640 286" stroke="rgba(255,255,255,0.06)" stroke-width="2" fill="none" />
                                        <path d="M0 312 C180 292, 340 294, 640 322" stroke="rgba(255,255,255,0.05)" stroke-width="2" fill="none" />

                                        <g class="cloud-drift-slow" opacity="0.95">
                                            <ellipse cx="372" cy="34" rx="42" ry="18" fill="#f8fafc" />
                                            <ellipse cx="338" cy="38" rx="28" ry="14" fill="#f8fafc" />
                                            <ellipse cx="405" cy="38" rx="34" ry="16" fill="#f8fafc" />
                                            <ellipse cx="444" cy="34" rx="28" ry="13" fill="#f8fafc" />
                                        </g>
                                        <g class="cloud-drift-fast" opacity="0.82">
                                            <ellipse cx="500" cy="52" rx="36" ry="15" fill="#f1f5f9" />
                                            <ellipse cx="472" cy="56" rx="24" ry="12" fill="#f1f5f9" />
                                            <ellipse cx="528" cy="57" rx="28" ry="12" fill="#f1f5f9" />
                                        </g>

                                        <g class="sun-pulse">
                                            <circle cx="82" cy="66" r="44" fill="#fbbf24" opacity="0.4" filter="url(#softGlow)" />
                                            <circle cx="82" cy="66" r="28" fill="#fde047" />
                                        </g>
                                        <g class="sun-rotate" stroke="#facc15" stroke-width="7">
                                            <line x1="82" y1="10" x2="82" y2="-10" />
                                            <line x1="82" y1="142" x2="82" y2="122" />
                                            <line x1="26" y1="66" x2="6" y2="66" />
                                            <line x1="158" y1="66" x2="138" y2="66" />
                                            <line x1="45" y1="29" x2="31" y2="15" />
                                            <line x1="119" y1="103" x2="133" y2="117" />
                                            <line x1="45" y1="103" x2="31" y2="117" />
                                            <line x1="119" y1="29" x2="133" y2="15" />
                                        </g>
                                        <g class="sun-ray" opacity="0.6">
                                            <path d="M126 92 C170 108, 176 120, 206 138" stroke="#fde68a" stroke-width="4" fill="none" />
                                            <path d="M132 70 C178 82, 220 106, 258 126" stroke="#fde68a" stroke-width="4" fill="none" />
                                        </g>

                                        <g>
                                            <polygon points="164,126 366,126 456,176 258,176" fill="url(#roofFill)" />
                                            <polygon points="170,132 356,132 438,176 258,176" fill="#3f3f46" />
                                            <polygon points="164,126 116,176 258,176" fill="#71717a" />
                                            <polygon points="116,176 116,292 258,292 258,176" fill="url(#wallFill)" />
                                            <polygon points="258,176 438,176 438,300 258,300" fill="#f8fafc" />
                                            <polygon points="116,292 258,292 236,324 92,324" fill="#d4d4d8" />
                                            <polygon points="258,300 438,300 408,334 236,334" fill="#e5e7eb" />
                                            <rect x="300" y="184" width="54" height="84" fill="#cbd5e1" />
                                            <rect x="306" y="190" width="42" height="72" fill="#1e293b" />
                                            <line x1="327" y1="190" x2="327" y2="262" stroke="#94a3b8" />
                                            <line x1="306" y1="226" x2="348" y2="226" stroke="#94a3b8" />
                                            <rect x="398" y="202" width="22" height="44" rx="3" fill="#e2e8f0" />
                                            <circle cx="408" cy="224" r="2" fill="#94a3b8" />
                                            <circle cx="414" cy="224" r="2" fill="#94a3b8" />
                                        </g>

                                        <g transform="translate(182 138) skewX(-26)">
                                            <rect x="0" y="0" width="64" height="34" rx="3" fill="url(#panelFill)" stroke="#dbeafe" stroke-width="2.2" />
                                            <rect x="72" y="0" width="64" height="34" rx="3" fill="url(#panelFill)" stroke="#dbeafe" stroke-width="2.2" />
                                            <rect x="144" y="0" width="64" height="34" rx="3" fill="url(#panelFill)" stroke="#dbeafe" stroke-width="2.2" />
                                            <g stroke="rgba(255,255,255,0.45)" stroke-width="1.4">
                                                <line x1="9" y1="8" x2="55" y2="8" />
                                                <line x1="9" y1="17" x2="55" y2="17" />
                                                <line x1="9" y1="26" x2="55" y2="26" />
                                                <line x1="81" y1="8" x2="127" y2="8" />
                                                <line x1="81" y1="17" x2="127" y2="17" />
                                                <line x1="81" y1="26" x2="127" y2="26" />
                                                <line x1="153" y1="8" x2="199" y2="8" />
                                                <line x1="153" y1="17" x2="199" y2="17" />
                                                <line x1="153" y1="26" x2="199" y2="26" />
                                                <line x1="22" y1="4" x2="22" y2="30" />
                                                <line x1="42" y1="4" x2="42" y2="30" />
                                                <line x1="94" y1="4" x2="94" y2="30" />
                                                <line x1="114" y1="4" x2="114" y2="30" />
                                                <line x1="166" y1="4" x2="166" y2="30" />
                                                <line x1="186" y1="4" x2="186" y2="30" />
                                            </g>
                                            <rect class="panel-shine" x="-10" y="0" width="28" height="34" fill="rgba(255,255,255,0.22)" />
                                            <line x1="18" y1="34" x2="12" y2="58" stroke="#cbd5e1" stroke-width="2.5" />
                                            <line x1="132" y1="34" x2="126" y2="58" stroke="#cbd5e1" stroke-width="2.5" />
                                        </g>

                                        <g fill="#ffffff" font-size="17" font-weight="700">
                                            <text x="58" y="116">Solar Panel</text>
                                            <text x="232" y="72">Mounting System</text>
                                            <text x="392" y="80">Net Meter</text>
                                            <text x="594" y="78">Grid</text>
                                            <text x="228" y="164">Inverter</text>
                                            <text x="414" y="226">House</text>
                                        </g>
                                        <g stroke="#dbeafe" stroke-width="2.4">
                                            <path d="M124 120 L192 146" />
                                            <path d="M304 76 L274 134" />
                                            <path d="M430 82 L434 138" />
                                            <path d="M584 82 L536 124" />
                                            <path d="M448 230 L390 266" />
                                        </g>

                                        <circle cx="274" cy="134" r="11" fill="none" stroke="#dbeafe" stroke-width="3" />
                                        <circle cx="274" cy="134" r="4" fill="#60a5fa" />

                                        <g>
                                            <rect x="244" y="176" width="84" height="60" rx="8" fill="#f8fafc" />
                                            <rect x="254" y="189" width="22" height="8" rx="2" fill="#a3e635" />
                                            <rect x="282" y="189" width="32" height="8" rx="2" fill="#e5e7eb" />
                                            <rect x="264" y="206" width="46" height="11" rx="2" fill="#1f2937" />
                                            <rect x="258" y="222" width="58" height="5" rx="2.5" fill="#94a3b8" />
                                            <circle cx="292" cy="247" r="2.3" fill="#334155" />
                                            <circle cx="304" cy="247" r="2.3" fill="#334155" />
                                        </g>

                                        <g>
                                            <rect x="416" y="104" width="36" height="56" rx="4" fill="#8b5e3c" />
                                            <rect x="422" y="110" width="24" height="44" rx="2" fill="#fafaf9" />
                                            <rect x="426" y="118" width="16" height="12" rx="2" fill="#cbd5e1" />
                                            <path d="M434 132V148" stroke="#94a3b8" stroke-width="2" />
                                            <path d="M428 140H440" stroke="#94a3b8" stroke-width="2" />
                                        </g>

                                        <g>
                                            <rect x="552" y="66" width="8" height="188" rx="4" fill="#d4d4d8" />
                                            <path d="M556 78 L602 104 L566 104 L616 132 L574 132 L624 156 L582 156" stroke="#e5e7eb" stroke-width="2.5" fill="none" />
                                            <path d="M556 78 L520 104 L548 104 L502 132 L540 132 L494 156 L532 156" stroke="#e5e7eb" stroke-width="2.5" fill="none" />
                                            <path d="M602 104 C612 100, 620 103, 626 114" stroke="#e5e7eb" stroke-width="2" fill="none" />
                                            <path d="M502 132 C492 128, 484 131, 478 142" stroke="#e5e7eb" stroke-width="2" fill="none" />
                                            <path d="M624 156 C632 154, 638 158, 640 166" stroke="#e5e7eb" stroke-width="2" fill="none" />
                                        </g>

                                        <g>
                                            <ellipse cx="430" cy="286" rx="96" ry="42" fill="#254f1f" />
                                            <ellipse cx="430" cy="290" rx="88" ry="34" fill="#63a63e" />
                                            <polygon points="394,264 430,230 470,264 470,296 394,296" fill="#f8fafc" />
                                            <polygon points="387,264 430,218 478,264" fill="#5b4635" />
                                            <rect x="420" y="258" width="18" height="38" fill="#1f2937" />
                                            <rect x="397" y="270" width="16" height="16" fill="#dbeafe" />
                                            <rect x="445" y="270" width="16" height="16" fill="#dbeafe" />
                                            <ellipse class="house-glow" cx="430" cy="296" rx="48" ry="10" fill="#fef08a" />
                                            <circle cx="370" cy="268" r="22" fill="#3f7c2f" />
                                            <circle cx="505" cy="280" r="18" fill="#3f7c2f" />
                                        </g>

                                        <g>
                                            <path d="M214 176 L214 244 L244 244" stroke="#ffffff" stroke-width="3" fill="none" />
                                            <path d="M328 206 L388 206 L388 156 L416 156" stroke="#ffffff" stroke-width="3" fill="none" />
                                            <path d="M434 160 L434 216 L430 216 L430 244" stroke="#ffffff" stroke-width="3" fill="none" />
                                            <path d="M434 160 L434 206 L552 206" stroke="#ffffff" stroke-width="3" fill="none" />
                                        </g>

                                        <path d="M214 176 L214 244 L244 244" stroke="url(#wireYellow)" stroke-width="6" fill="none" opacity="0.38" />
                                        <path class="energy-line" d="M214 176 L214 244 L244 244" stroke="url(#wireYellow)" stroke-width="6" fill="none" />

                                        <path d="M328 206 L388 206 L388 156 L416 156" stroke="#ffffff" stroke-width="6" fill="none" opacity="0.35" />
                                        <path class="energy-line delay-1" d="M328 206 L388 206 L388 156 L416 156" stroke="#ffffff" stroke-width="6" fill="none" />

                                        <path d="M434 160 L434 216 L430 216 L430 244" stroke="url(#wireBlue)" stroke-width="6" fill="none" opacity="0.42" />
                                        <path class="energy-line delay-2" d="M434 160 L434 216 L430 216 L430 244" stroke="url(#wireBlue)" stroke-width="6" fill="none" />

                                        <path d="M434 160 L434 206 L552 206" stroke="url(#wireWhite)" stroke-width="6" fill="none" opacity="0.42" />
                                        <path class="energy-line delay-3" d="M434 160 L434 206 L552 206" stroke="url(#wireWhite)" stroke-width="6" fill="none" />

                                        <text x="340" y="190" fill="#ffffff" font-size="13" font-weight="700">STEP 2</text>
                                        <text x="184" y="228" fill="#fde047" font-size="13" font-weight="700">STEP 1</text>
                                        <text x="442" y="212" fill="#38bdf8" font-size="13" font-weight="700">STEP 3</text>
                                        <text x="482" y="196" fill="#ffffff" font-size="13" font-weight="700">STEP 4</text>
                                        <text x="334" y="176" fill="#f59e0b" font-size="13" font-weight="800">DC</text>
                                        <text x="442" y="230" fill="#38bdf8" font-size="13" font-weight="800">AC</text>

                                        <circle class="power-dot" cx="214" cy="224" r="4.5" fill="#fde047" />
                                        <circle class="power-dot delay-1" cx="386" cy="206" r="4.5" fill="#ffffff" />
                                        <circle class="power-dot delay-2" cx="432" cy="226" r="4.5" fill="#38bdf8" />
                                        <circle class="power-dot delay-3" cx="508" cy="206" r="4.5" fill="#ffffff" />
                                    </svg>
                                </div>
                            </div>

                            <div class="mt-4 rounded-2xl border border-white/10 bg-white/5 p-4 text-sm leading-7 text-slate-300">
                                This detailed animation makes the route obvious for any viewer: sunlight hits the solar panels, DC energy goes to the inverter, AC power feeds the house, and surplus can continue to the grid through the net meter.
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
