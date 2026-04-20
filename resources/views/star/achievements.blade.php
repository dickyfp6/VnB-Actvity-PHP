@extends('layouts.app')

@section('title', 'STAR Achievements - VnB Platform')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h1 class="text-3xl font-bold text-gray-900">STAR Achievements</h1>
        <p class="mt-3 text-gray-600 leading-relaxed">
            Kelola achievement dan pencapaian dalam platform.
        </p>
    </section>

    <!-- Achievements List -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Achievements List</h2>
        <p class="text-gray-600">Daftar achievements akan ditampilkan di sini.</p>
    </section>
</div>
@endsection
