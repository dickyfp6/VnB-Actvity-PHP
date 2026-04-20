@extends('layouts.app')

@section('title', 'STAR Recognition - VnB Platform')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h1 class="text-3xl font-bold text-gray-900">STAR Recognition</h1>
        <p class="mt-3 text-gray-600 leading-relaxed">
            Lihat dan kelola recognition yang diterima dalam platform.
        </p>
    </section>

    <!-- Recognition List -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Recognition List</h2>
        <p class="text-gray-600">Daftar recognition akan ditampilkan di sini.</p>
    </section>
</div>
@endsection
