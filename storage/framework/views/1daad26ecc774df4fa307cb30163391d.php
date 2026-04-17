
<?php $__env->startSection('title','Ganti Password'); ?>
<?php $__env->startSection('content'); ?>
<div class="px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Keamanan Akun</h1>
            <p class="text-gray-600">Kelola pengaturan keamanan dan privasi akun Anda</p>
        </div>

        <!-- Password Change Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-green-600 to-green-700 p-6 text-white">
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-14 h-14 bg-white/20 rounded-full">
                        <i class="fas fa-lock text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">Ganti Password</h2>
                        <p class="text-green-100 text-sm">Tingkatkan keamanan akun Anda dengan password yang kuat</p>
                    </div>
                </div>
            </div>

            <!-- Card Body -->
            <div class="p-8">
                <!-- Step Indicator -->
                <div class="mb-8">
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center font-bold step-indicator" data-step="1">1</div>
                            <p class="text-xs text-gray-600 mt-2">Verifikasi</p>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 step-line"></div>
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-bold step-indicator" data-step="2">2</div>
                            <p class="text-xs text-gray-600 mt-2">Password Baru</p>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Verify Current Password -->
                <div id="step1-content" class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Verifikasi Password Saat Ini</h3>
                        <p class="text-sm text-gray-600 mb-4">Untuk keamanan, kami perlu memverifikasi password Anda sebelum melanjutkan.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Password Saat Ini</label>
                        <div class="relative">
                            <input 
                                id="current-password" 
                                type="password" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition text-sm"
                                placeholder="Masukkan password saat ini"
                                autocomplete="current-password"
                                required
                            >
                            <button 
                                type="button" 
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition toggle-password" 
                                data-target="current-password"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="current-password-error" class="text-red-600 text-sm mt-2 hidden"></div>
                    </div>

                    <button 
                        type="button" 
                        id="verify-btn" 
                        class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition flex items-center justify-center gap-2"
                    >
                        <span>Verifikasi</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- Step 2: New Password -->
                <div id="step2-content" class="space-y-6 hidden">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Password Baru</h3>
                        <p class="text-sm text-gray-600 mb-4">Password harus minimal 6 karakter dan mengandung kombinasi karakter yang kuat.</p>
                    </div>

                    <!-- Password Requirements -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm font-medium text-blue-900 mb-3">Persyaratan Password:</p>
                        <ul class="space-y-2 text-sm text-blue-800">
                            <li class="flex items-center gap-2">
                                <span id="req-length" class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                                <span>Minimal 6 karakter</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span id="req-different" class="text-gray-400"><i class="fas fa-times-circle"></i></span>
                                <span>Berbeda dengan password lama</span>
                            </li>
                        </ul>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Password Baru</label>
                        <div class="relative">
                            <input 
                                id="new-password" 
                                type="password" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition text-sm"
                                placeholder="Masukkan password baru"
                                autocomplete="new-password"
                                required
                            >
                            <button 
                                type="button" 
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700 transition toggle-password" 
                                data-target="new-password"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-900 mb-3">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input 
                                id="confirm-password" 
                                type="password" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:border-green-500 focus:outline-none transition text-sm"
                                placeholder="Konfirmasi password baru"
                                autocomplete="new-password"
                                required
                            >
                            <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                        </div>
                        <div id="confirm-password-error" class="text-red-600 text-sm mt-2 hidden"></div>
                    </div>

                    <!-- Match Indicator -->
                    <div id="password-match-indicator" class="flex items-center gap-2 text-sm text-gray-600 hidden">
                        <span id="match-icon"></span>
                        <span id="match-text"></span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3 pt-4">
                        <button 
                            type="button" 
                            id="back-btn" 
                            class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition flex items-center justify-center gap-2"
                        >
                            <i class="fas fa-arrow-left"></i>
                            <span>Kembali</span>
                        </button>
                        <button 
                            type="button" 
                            id="submit-btn" 
                            class="flex-1 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition disabled:bg-gray-400 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                            disabled
                        >
                            <i class="fas fa-check"></i>
                            <span>Simpan Password</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Footer Note -->
            <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
                <p class="text-xs text-gray-600 flex items-center gap-2">
                    <i class="fas fa-shield-alt text-green-600"></i>
                    Password Anda dienkripsi dengan standar keamanan tingkat enterprise.
                </p>
            </div>
        </div>

        <!-- Tips Section -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-semibold text-blue-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-lightbulb text-blue-600"></i>
                    Password Kuat
                </h4>
                <p class="text-sm text-blue-800">Gunakan kombinasi huruf besar, kecil, angka, dan simbol.</p>
            </div>
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                <h4 class="font-semibold text-purple-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-lock-open text-purple-600"></i>
                    Jangan Bagikan
                </h4>
                <p class="text-sm text-purple-800">Jangan pernah bagikan password dengan siapapun.</p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <h4 class="font-semibold text-amber-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-history text-amber-600"></i>
                    Update Berkala
                </h4>
                <p class="text-sm text-amber-800">Perbarui password secara berkala untuk keamanan maksimal.</p>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const step1Content = document.getElementById('step1-content');
    const step2Content = document.getElementById('step2-content');
    const verifyBtn = document.getElementById('verify-btn');
    const backBtn = document.getElementById('back-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    const currentPasswordInput = document.getElementById('current-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    
    const stepIndicators = document.querySelectorAll('.step-indicator');
    const stepLine = document.querySelector('.step-line');
    
    const currentPasswordError = document.getElementById('current-password-error');
    const confirmPasswordError = document.getElementById('confirm-password-error');
    const passwordMatchIndicator = document.getElementById('password-match-indicator');
    const matchIcon = document.getElementById('match-icon');
    const matchText = document.getElementById('match-text');
    
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });

    // Verify current password
    verifyBtn.addEventListener('click', async function () {
        const currentPassword = currentPasswordInput.value.trim();
        
        if (!currentPassword) {
            currentPasswordError.textContent = 'Password harus diisi';
            currentPasswordError.classList.remove('hidden');
            return;
        }

        verifyBtn.disabled = true;
        verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memverifikasi...';

        try {
            // Simple verification by checking basic validation
            // The actual verification will happen on submit
            const storedPassword = await verifyPasswordAsync(currentPassword);
            
            if (storedPassword) {
                currentPasswordError.classList.add('hidden');
                moveToStep2();
            } else {
                currentPasswordError.textContent = 'Password saat ini tidak sesuai';
                currentPasswordError.classList.remove('hidden');
            }
        } catch (error) {
            currentPasswordError.textContent = 'Terjadi kesalahan saat verifikasi';
            currentPasswordError.classList.remove('hidden');
        } finally {
            verifyBtn.disabled = false;
            verifyBtn.innerHTML = '<span>Verifikasi</span><i class="fas fa-arrow-right"></i>';
        }
    });

    // Verify password async - make a check request to the server
    async function verifyPasswordAsync(password) {
        try {
            const response = await fetch('/api/auth/verify-password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    current_password: password
                })
            });
            
            const data = await response.json();
            return response.ok && data.valid;
        } catch (error) {
            console.error('Verification error:', error);
            return false;
        }
    }

    function moveToStep2() {
        // Update step indicators
        stepIndicators[0].classList.remove('bg-green-600', 'text-white');
        stepIndicators[0].classList.add('bg-green-600', 'text-white');
        
        stepIndicators[1].classList.remove('bg-gray-300', 'text-gray-600');
        stepIndicators[1].classList.add('bg-green-600', 'text-white');
        
        stepLine.classList.remove('bg-gray-200');
        stepLine.classList.add('bg-green-600');

        // Hide step 1, show step 2
        step1Content.classList.add('hidden');
        step2Content.classList.remove('hidden');
        
        // Focus on new password input
        newPasswordInput.focus();
    }

    // Back button
    backBtn.addEventListener('click', function () {
        moveToStep1();
    });

    function moveToStep1() {
        stepIndicators[0].classList.remove('bg-gray-300', 'text-gray-600');
        stepIndicators[0].classList.add('bg-green-600', 'text-white');
        
        stepIndicators[1].classList.remove('bg-green-600', 'text-white');
        stepIndicators[1].classList.add('bg-gray-300', 'text-gray-600');
        
        stepLine.classList.remove('bg-green-600');
        stepLine.classList.add('bg-gray-200');

        step2Content.classList.add('hidden');
        step1Content.classList.remove('hidden');
        
        currentPasswordInput.focus();
    }

    // New password validation
    newPasswordInput.addEventListener('input', function () {
        const newPassword = this.value;
        const currentPassword = currentPasswordInput.value;
        
        // Check length
        const lengthOk = newPassword.length >= 6;
        updateRequirement('req-length', lengthOk);
        
        // Check different from current
        const differentOk = newPassword !== currentPassword && newPassword.length > 0;
        updateRequirement('req-different', differentOk);
        
        checkPasswordMatch();
    });

    // Confirm password validation
    confirmPasswordInput.addEventListener('input', function () {
        checkPasswordMatch();
    });

    function updateRequirement(id, state) {
        const el = document.getElementById(id);
        if (state) {
            el.className = 'text-green-600';
            el.innerHTML = '<i class="fas fa-check-circle"></i>';
        } else {
            el.className = 'text-gray-400';
            el.innerHTML = '<i class="fas fa-times-circle"></i>';
        }
    }

    function checkPasswordMatch() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (!confirmPassword) {
            passwordMatchIndicator.classList.add('hidden');
            submitBtn.disabled = true;
            return;
        }

        const matches = newPassword === confirmPassword;
        passwordMatchIndicator.classList.remove('hidden');
        
        if (matches) {
            matchIcon.innerHTML = '<i class="fas fa-check-circle text-green-600"></i>';
            matchText.textContent = 'Password cocok!';
            matchText.className = 'text-green-600';
            confirmPasswordError.classList.add('hidden');
            
            // Enable submit if all requirements met
            const lengthOk = newPassword.length >= 6;
            const differentOk = newPassword !== currentPasswordInput.value;
            submitBtn.disabled = !(lengthOk && differentOk && matches);
        } else {
            matchIcon.innerHTML = '<i class="fas fa-times-circle text-red-600"></i>';
            matchText.textContent = 'Password tidak cocok!';
            matchText.className = 'text-red-600';
            submitBtn.disabled = true;
        }
    }

    // Submit button
    submitBtn.addEventListener('click', async function () {
        const currentPassword = currentPasswordInput.value;
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Final validation
        if (newPassword !== confirmPassword) {
            confirmPasswordError.textContent = 'Konfirmasi password tidak sama';
            confirmPasswordError.classList.remove('hidden');
            return;
        }

        if (newPassword.length < 6) {
            confirmPasswordError.textContent = 'Password minimal 6 karakter';
            confirmPasswordError.classList.remove('hidden');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const res = await fetch('/api/auth/change-password', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    current_password: currentPassword,
                    password: newPassword,
                    password_confirmation: confirmPassword,
                })
            });

            const data = await res.json();

            if (res.ok && data.success) {
                showAlert(data.message || 'Password berhasil diubah!', 'success');
                
                // Reset form and go back to step 1
                setTimeout(() => {
                    currentPasswordInput.value = '';
                    newPasswordInput.value = '';
                    confirmPasswordInput.value = '';
                    confirmPasswordError.classList.add('hidden');
                    passwordMatchIndicator.classList.add('hidden');
                    moveToStep1();
                }, 1500);
            } else {
                const errorMsg = data.message || Object.values(data.errors || {})?.[0]?.[0] || 'Gagal mengubah password';
                confirmPasswordError.textContent = errorMsg;
                confirmPasswordError.classList.remove('hidden');
                showAlert(errorMsg, 'error');
            }
        } catch (error) {
            confirmPasswordError.textContent = 'Terjadi kesalahan saat menyimpan';
            confirmPasswordError.classList.remove('hidden');
            showAlert('Terjadi kesalahan saat menyimpan password', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check"></i><span>Simpan Password</span>';
        }
    });

    // Enter key submit on step 1
    currentPasswordInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && step1Content.classList.contains('hidden') === false) {
            verifyBtn.click();
        }
    });

    // Enter key submit on step 2
    confirmPasswordInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter' && step2Content.classList.contains('hidden') === false && !submitBtn.disabled) {
            submitBtn.click();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/account/change-password.blade.php ENDPATH**/ ?>