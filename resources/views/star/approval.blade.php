@extends('layouts.app')

@section('title', 'STAR Approval - VnB Platform')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h1 class="text-3xl font-bold text-gray-900">STAR Approval</h1>
        <p class="mt-3 text-gray-600 leading-relaxed">
            Proses persetujuan dan penandatanganan recognition/achievement.
            Fitur ini memungkinkan Manager, Direktur Utama, dan PCX untuk memberikan approval dan ttd (tanda tangan digital).
        </p>
    </section>

    <!-- Approval Requests -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Pending Approvals</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee</th>
                        <th class="px-4 py-3 text-left font-semibold">Achievement</th>
                        <th class="px-4 py-3 text-center font-semibold">Points</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">Project Completion</td>
                        <td class="px-4 py-3 text-center">50</td>
                        <td class="px-4 py-3"><span class="badge badge-info">Pending TTD</span></td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn btn-sm btn-success">Approve & TTD</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Approval Guidelines -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Panduan Approval</h2>
        <div class="space-y-3 text-gray-600">
            <p><strong>1. Review Submission:</strong> Lihat detail achievement yang diajukan.</p>
            <p><strong>2. Assign TTD:</strong> Berikan tanda tangan digital untuk approval.</p>
            <p><strong>3. Point Calculation:</strong> Sistem akan otomatis menghitung points berdasarkan schema.</p>
            <p><strong>Catatan:</strong> Sistem ttd saat ini berbasis softfile sederhana untuk efficiency.</p>
        </div>
    </section>
</div>
@endsection
