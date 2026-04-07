@extends('layouts.app')

@section('title','Profil Akun - VnB Platform')

@section('content')
<div class="space-y-6 px-4">
    <h1 class="text-2xl font-bold text-gray-900">Profil Akun</h1>

    <!-- Data Pribadi New Hire atau Manager -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Data Pribadi</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @if($role === 'manager' && $manager)
                <!-- Manager Profile Data -->
                <div>
                    <p class="text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="font-medium text-gray-900">{{ $manager->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Email</p>
                    <p class="font-medium text-gray-900">{{ $manager->email ?? $user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">WhatsApp</p>
                    <p class="font-medium text-gray-900">{{ $manager->whatsapp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Perusahaan</p>
                    <p class="font-medium text-gray-900">{{ $manager->company ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Divisi</p>
                    <p class="font-medium text-gray-900">{{ $manager->division?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Departemen</p>
                    <p class="font-medium text-gray-900">{{ $manager->department?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Posisi</p>
                    <p class="font-medium text-gray-900">{{ $manager->position?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Level</p>
                    <p class="font-medium text-gray-900">{{ $manager->level ?? '-' }}</p>
                </div>
            @elseif($role === 'new_hire' && $employee)
                <!-- New Hire Profile Data -->
                <div>
                    <p class="text-gray-500 mb-1">Nomor Karyawan</p>
                    <p class="font-medium text-gray-900">{{ $employee->employee_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Nama Lengkap</p>
                    <p class="font-medium text-gray-900">{{ $employee->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Email</p>
                    <p class="font-medium text-gray-900">{{ $employee->email ?? $user->email }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">WhatsApp</p>
                    <p class="font-medium text-gray-900">{{ $employee->whatsapp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Perusahaan</p>
                    <p class="font-medium text-gray-900">{{ $employee->company ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Divisi</p>
                    <p class="font-medium text-gray-900">{{ $employee->division?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Departemen</p>
                    <p class="font-medium text-gray-900">{{ $employee->department?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Posisi</p>
                    <p class="font-medium text-gray-900">{{ $employee->position?->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Level</p>
                    <p class="font-medium text-gray-900">{{ $employee->level ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Placement</p>
                    <p class="font-medium text-gray-900">{{ $employee->placement ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Tanggal Bergabung</p>
                    <p class="font-medium text-gray-900">{{ $employee->date_joined ? $employee->date_joined->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Tanggal Induction</p>
                    <p class="font-medium text-gray-900">{{ $employee->induction_date ? $employee->induction_date->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Status Karyawan</p>
                    <p class="font-medium text-gray-900">{{ $employee->employee_status ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 mb-1">Status VnB</p>
                    <p class="font-medium text-gray-900">{{ $employee->vnb_status ?? '-' }}</p>
                </div>
            @else
                <p class="text-gray-500">Data profil tidak ditemukan.</p>
            @endif
        </div>
    </div>

    <!-- Informasi Akun & Password -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun Platform</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm mb-6">
            <div>
                <p class="text-gray-500 mb-1">Email Login</p>
                <p class="font-medium text-gray-900">{{ $user->email }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Nama Pengguna</p>
                <p class="font-medium text-gray-900">{{ $user->name }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Nomor Telepon</p>
                <p class="font-medium text-gray-900">{{ $user->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-gray-500 mb-1">Status Akun</p>
                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: #D0EC98; color: #144600;">{{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>

        <div class="py-4 border-t">
            <a href="{{ route('my-account.change-password') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white font-semibold rounded-lg hover:from-green-700 hover:to-green-800 transition">
                <i class="fas fa-key"></i>
                <span>Ganti Password</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endsection
