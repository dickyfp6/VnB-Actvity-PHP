@extends('layouts.app')
@section('title','Ganti Password')
@section('content')
<div class="px-4">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm p-6">
        <h1 class="text-xl font-bold text-gray-800 mb-1">Ganti Password</h1>
        <p class="text-sm text-gray-500 mb-5">Ubah password akun Anda secara mandiri.</p>

        <form id="change-password-form" class="space-y-3">
            <div>
                <label class="block text-sm text-gray-700 mb-1">Password Saat Ini</label>
                <input id="current-password" type="password" class="w-full border rounded-lg px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Password Baru</label>
                <input id="new-password" type="password" class="w-full border rounded-lg px-3 py-2 text-sm" minlength="6" required>
            </div>
            <div>
                <label class="block text-sm text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input id="confirm-password" type="password" class="w-full border rounded-lg px-3 py-2 text-sm" minlength="6" required>
            </div>

            <div class="pt-2 flex justify-end">
                <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm" style="background-color:#144600;">Simpan Password</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('change-password-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (newPassword !== confirmPassword) {
        showAlert('Konfirmasi password tidak sama', 'error');
        return;
    }

    const res = await apiPost('/api/auth/change-password', {
        current_password: currentPassword,
        password: newPassword,
        password_confirmation: confirmPassword,
    }, 'POST');

    if (res && res.success === true) {
        showAlert(res.message || 'Password berhasil diubah');
        document.getElementById('change-password-form').reset();
    } else {
        const firstError = res?.errors ? Object.values(res.errors)[0]?.[0] : null;
        showAlert(firstError || res?.message || res?.error || 'Gagal mengubah password', 'error');
    }
});
</script>
@endpush
