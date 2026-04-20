@extends('layouts.app')

@section('title', 'VNB Approval - Activity Review')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h1 class="text-3xl font-bold text-gray-900">VNB Activity Approval</h1>
        <p class="mt-3 text-gray-600 leading-relaxed">
            Review dan approve VNB activities yang telah disubmit oleh karyawan.
            Sebagai manager, Anda dapat menerima atau menolak activity dengan feedback.
        </p>
    </section>

    <!-- Pending Approvals -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Pending Approvals</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee</th>
                        <th class="px-4 py-3 text-left font-semibold">Activity</th>
                        <th class="px-4 py-3 text-left font-semibold">Submitted</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                        <th class="px-4 py-3 text-center font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">John Doe</td>
                        <td class="px-4 py-3">Project Leadership</td>
                        <td class="px-4 py-3">2 days ago</td>
                        <td class="px-4 py-3"><span class="badge badge-info">Pending Review</span></td>
                        <td class="px-4 py-3 text-center">
                            <button class="btn btn-sm btn-success">Review</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    <!-- Approved Activities -->
    <section class="card-glass rounded-2xl p-8 border border-white/60">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Approved Activities</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="px-4 py-3 text-left font-semibold">Employee</th>
                        <th class="px-4 py-3 text-left font-semibold">Activity</th>
                        <th class="px-4 py-3 text-left font-semibold">Approved Date</th>
                        <th class="px-4 py-3 text-left font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                        <td class="px-4 py-3">Jane Smith</td>
                        <td class="px-4 py-3">Training Completion</td>
                        <td class="px-4 py-3">1 week ago</td>
                        <td class="px-4 py-3"><span class="badge badge-success">Approved</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
