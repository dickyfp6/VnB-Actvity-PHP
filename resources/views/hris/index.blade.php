@extends('layouts.app')

@section('title', 'HRIS - Data Synchronization')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h1 class="text-3xl font-bold text-gray-900">HRIS Data Synchronization</h1>
        <p class="mt-3 text-gray-600 leading-relaxed">
            Sinkronisasi data karyawan dari sistem HRIS. Tabel di bawah menampilkan data yang telah diperbarui oleh HRIS.
            Klik tombol "Sinkronkan" untuk memperbarui data di tabel Employee.
        </p>
    </section>

    <!-- HRIS Sync Table -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Data HRIS yang Diperbarui</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee ID</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama</th>
                        <th class="px-4 py-3 text-left font-semibold">Perubahan</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">EMP001</td>
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">Department Updated</td>
                        <td class="px-4 py-3"><span class="badge badge-warning">Pending</span></td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn btn-sm btn-primary">Sinkronkan</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
