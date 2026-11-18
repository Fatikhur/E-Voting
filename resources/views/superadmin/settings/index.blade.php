@extends('layouts.app', ['title' => 'Pengaturan Sistem'])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pengaturan Sistem</h1>
            <p class="text-sm text-slate-500 mt-1">Konfigurasi operasional aplikasi, keamanan token, dan identitas lingkungan.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form action="{{ route('superadmin.settings.update') }}" method="POST" class="p-6 sm:p-8 space-y-6">
            @csrf

            @foreach($settings as $group => $items)
                <div class="space-y-4 pt-4 first:pt-0 border-t first:border-t-0 border-slate-100">
                    <h3 class="font-bold text-base text-slate-800 uppercase tracking-wide text-xs text-blue-600">
                        Kelompok: {{ ucfirst($group) }}
                    </h3>

                    <div class="space-y-4">
                        @foreach($items as $st)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                                <div class="max-w-md">
                                    <label for="{{ $st->key }}" class="block text-sm font-bold text-slate-800">
                                        {{ ucwords(str_replace('_', ' ', $st->key)) }}
                                    </label>
                                    <p class="text-xs text-slate-500 mt-0.5">{{ $st->description }}</p>
                                </div>
                                <div class="w-full sm:w-64">
                                    @if($st->type === 'boolean')
                                        <select name="{{ $st->key }}" id="{{ $st->key }}" class="w-full px-3 py-2 text-sm border rounded-lg bg-white">
                                            <option value="true" {{ $st->value === 'true' || $st->value === '1' ? 'selected' : '' }}>Aktif (True)</option>
                                            <option value="false" {{ $st->value === 'false' || $st->value === '0' ? 'selected' : '' }}>Nonaktif (False)</option>
                                        </select>
                                    @elseif($st->type === 'integer')
                                        <input type="number" name="{{ $st->key }}" id="{{ $st->key }}" value="{{ $st->value }}" class="w-full px-3 py-2 text-sm border rounded-lg bg-white">
                                    @else
                                        <input type="text" name="{{ $st->key }}" id="{{ $st->key }}" value="{{ $st->value }}" class="w-full px-3 py-2 text-sm border rounded-lg bg-white">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="pt-6 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition">
                    Simpan Semua Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
