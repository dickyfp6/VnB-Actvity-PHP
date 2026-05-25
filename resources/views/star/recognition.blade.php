@extends('layouts.app')

@section('title', 'STAR Recognition - VnB Platform')
@section('page_title', 'STAR Recognition')
@section('page_subtitle', 'Buka skema STAR untuk melihat acuan penilaian yang dipakai platform ini.')

@section('content')
<div class="space-y-6">
	<div class="flex justify-end">
		<button type="button" onclick="openStarSchemaPreview()" class="inline-flex items-center gap-2 rounded-full border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-800 transition hover:bg-green-100">
			<i class="fas fa-layer-group text-[10px]"></i>
			Lihat Skema STAR
		</button>
	</div>

	<div class="card-glass rounded-2xl border border-gray-200 shadow-sm p-5">
		<p class="text-sm text-gray-600">Halaman recognition akan diisi konten proses pengakuan di sini.</p>
	</div>

	@include('star.partials.schema-preview-modal')
</div>
@endsection
