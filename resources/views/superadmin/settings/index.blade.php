@extends('layouts.app', ['title' => 'Pengaturan Sistem'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-[#252A34] tracking-tight">Pengaturan Sistem</h1>
            <p class="text-xs sm:text-sm text-[#6B7280] mt-1">Konfigurasi operasional aplikasi, keamanan token, dan identitas lingkungan.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-[#E5E7EB] overflow-hidden">
        <form action="{{ route('superadmin.settings.update') }}" method="POST" class="p-5 sm:p-6 space-y-6">
            @csrf

            @foreach($settings as $group => $items)
                <div class="space-y-3 pt-4 first:pt-0 border-t first:border-t-0 border-[#E5E7EB]">
                    <h3 class="font-semibold text-xs uppercase tracking-wider text-[#1877F2]">
                        Kelompok: {{ ucfirst($group) }}
                    </h3>

                    <div class="space-y-3">
                        @foreach($items as $st)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3.5 rounded-lg border border-[#E5E7EB] bg-slate-50">
                                <div class="max-w-md">
                                    <label for="{{ $st->key }}" class="block text-xs sm:text-sm font-semibold text-[#252A34]">
                                        {{ ucwords(str_replace('_', ' ', $st->key)) }}
                                    </label>
                                    <p class="text-xs text-[#6B7280] mt-0.5">{{ $st->description }}</p>
                                </div>
                                <div class="w-full sm:w-64">
                                    @if($st->type === 'boolean')
                                        <select name="{{ $st->key }}" id="{{ $st->key }}" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                                            <option value="true" {{ $st->value === 'true' || $st->value === '1' ? 'selected' : '' }}>Aktif (True)</option>
                                            <option value="false" {{ $st->value === 'false' || $st->value === '0' ? 'selected' : '' }}>Nonaktif (False)</option>
                                        </select>
                                    @elseif($st->type === 'integer')
                                        <input type="number" name="{{ $st->key }}" id="{{ $st->key }}" value="{{ $st->value }}" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                                    @else
                                        <input type="text" name="{{ $st->key }}" id="{{ $st->key }}" value="{{ $st->value }}" class="w-full px-3 py-2 text-xs sm:text-sm border border-[#E5E7EB] rounded-lg bg-white text-[#252A34] focus:outline-none focus:border-[#1877F2]">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="pt-4 border-t border-[#E5E7EB] flex justify-end">
                <button type="submit" class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold text-white bg-[#1877F2] hover:bg-[#166fe5] shadow-xs transition">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
