{{-- Sidebar Navigation Links --}}
<div class="px-3 py-4 space-y-6 flex-1 overflow-y-auto">
    <div>
        <p class="px-3 text-[11px] font-semibold text-[#6B7280] uppercase tracking-wider mb-2.5">Menu Utama</p>
        <nav class="space-y-1" aria-label="Navigasi Utama">
            {{-- Dashboard Link --}}
            @php
                $isDashboardActive = request()->routeIs('*.dashboard') || request()->routeIs('dashboard');
            @endphp
            <a href="{{ route('dashboard') }}"
               class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isDashboardActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
               @if($isDashboardActive) aria-current="page" @endif>
                <svg class="w-4 h-4 shrink-0 {{ $isDashboardActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                <span>Dashboard</span>
            </a>

            @if(auth()->user()->isSuperAdmin())
                {{-- Kelola Akun --}}
                @php $isUsersActive = request()->routeIs('superadmin.users.*'); @endphp
                <a href="{{ route('superadmin.users.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isUsersActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isUsersActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isUsersActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <span>Kelola Akun</span>
                </a>

                {{-- Wilayah RT/RW --}}
                @php $isTerritoryActive = request()->routeIs('superadmin.territories.*'); @endphp
                <a href="{{ route('superadmin.territories.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isTerritoryActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isTerritoryActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isTerritoryActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <span>Wilayah RT/RW</span>
                </a>

                {{-- Audit & Integritas --}}
                @php $isAuditActive = request()->routeIs('superadmin.audit.*'); @endphp
                <a href="{{ route('superadmin.audit.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isAuditActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isAuditActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isAuditActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Audit & Integritas</span>
                </a>

                {{-- Pengaturan --}}
                @php $isSettingsActive = request()->routeIs('superadmin.settings.*'); @endphp
                <a href="{{ route('superadmin.settings.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isSettingsActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isSettingsActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isSettingsActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pengaturan</span>
                </a>
            @endif

            @if(auth()->user()->isAdmin())
                {{-- Data Warga --}}
                @php $isResidentsActive = request()->routeIs('admin.residents.*'); @endphp
                <a href="{{ route('admin.residents.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isResidentsActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isResidentsActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isResidentsActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    <span>Data Warga</span>
                </a>

                {{-- Pemilihan --}}
                @php $isElectionsActive = request()->routeIs('admin.elections.*') || request()->routeIs('admin.candidates.*') || request()->routeIs('admin.dpt.*'); @endphp
                <a href="{{ route('admin.elections.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isElectionsActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isElectionsActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isElectionsActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                    </svg>
                    <span>Pemilihan</span>
                </a>
            @endif

            @if(auth()->user()->isPengawas())
                {{-- Pantau Pemilihan --}}
                @php $isMonitoringActive = request()->routeIs('pengawas.monitoring.*'); @endphp
                <a href="{{ route('pengawas.monitoring.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isMonitoringActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isMonitoringActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isMonitoringActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Pantau Pemilihan</span>
                </a>

                {{-- Jejak Audit --}}
                @php $isObserverAuditActive = request()->routeIs('pengawas.audit.*'); @endphp
                <a href="{{ route('pengawas.audit.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isObserverAuditActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isObserverAuditActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isObserverAuditActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>Jejak Audit</span>
                </a>

                {{-- Laporan Temuan --}}
                @php $isReportsActive = request()->routeIs('pengawas.reports.*'); @endphp
                <a href="{{ route('pengawas.reports.index') }}"
                   class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-[13px] transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#2ECC71] {{ $isReportsActive ? 'bg-[#2ECC71]/15 text-[#2ECC71] font-semibold' : 'text-[#C5C9D4] hover:text-white hover:bg-white/5 font-medium' }}"
                   @if($isReportsActive) aria-current="page" @endif>
                    <svg class="w-4 h-4 shrink-0 {{ $isReportsActive ? 'text-[#2ECC71]' : 'text-[#9AA0B4] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <span>Laporan Temuan</span>
                </a>
            @endif
        </nav>
    </div>
</div>

{{-- Sidebar Footer Information --}}
<div class="p-3 border-t border-[#1F2445] bg-[#13162C]">
    <div class="flex items-center gap-2.5 px-2.5 py-2 rounded-lg bg-[#171B35] border border-[#23294C]">
        <span class="w-2 h-2 rounded-full bg-[#2ECC71] shrink-0"></span>
        <div class="text-[11px] leading-tight truncate">
            <p class="font-medium text-[#C5C9D4]">Sistem E-Voting</p>
            <p class="text-[10px] text-[#6B7280]">Audit Kriptografis Aktif</p>
        </div>
    </div>
</div>
