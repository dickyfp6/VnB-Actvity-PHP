<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VnB Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        .bg-gradient-animated {
            background: linear-gradient(-45deg, #37aa05, #1a5c00, #37aa05, #144600);
            background-size: 400% 400%;
            animation: gradient-shift 15s ease infinite;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
        }

        .input-field {
            background: rgba(255, 255, 255, 0.7);
            border: 1.5px solid rgba(55, 170, 5, 0.2);
            transition: all 0.3s ease;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.95);
            border-color: #37aa05;
            outline: none;
            box-shadow: 0 0 0 3px rgba(55, 170, 5, 0.1);
        }

        .btn-login {
            background: linear-gradient(135deg, #37aa05 0%, #1a5c00 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(55, 170, 5, 0.25);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(55, 170, 5, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        /* Custom modern scrollbar - only visible on hover/scroll */
        .scrollbar-modern {
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.3) transparent;
        }

        .scrollbar-modern::-webkit-scrollbar {
            width: 6px;
        }

        .scrollbar-modern::-webkit-scrollbar-track {
            background: transparent;
        }

        .scrollbar-modern::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            transition: background 0.3s ease;
        }

        .scrollbar-modern::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .scrollbar-modern {
            transition: scrollbar-color 0.3s ease;
        }

        .scrollbar-modern:hover::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.35);
        }
    </style>
</head>
<body class="bg-gradient-animated min-h-screen flex items-center justify-center p-4">
    <!-- Background Decoration (Batik-inspired texture) -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <!-- Subtle overlapping circles -->
        <div class="absolute -top-96 -left-96 w-96 h-96 bg-green-600/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-96 -right-96 w-96 h-96 bg-green-500/5 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/3 w-80 h-80 bg-green-700/3 rounded-full blur-3xl mix-blend-multiply"></div>
    </div>

    <div class="fixed left-4 top-4 sm:left-6 sm:top-6 z-20">
        <button
            type="button"
            id="demo-fab"
            aria-expanded="false"
            aria-controls="demo-popover"
            class="group h-14 w-14 rounded-full bg-white/20 backdrop-blur-xl border border-white/25 shadow-2xl shadow-black/20 text-white flex items-center justify-center transition duration-300 hover:scale-105 hover:bg-white/30"
            title="Buka demo login"
        >
            <i class="fas fa-user-group text-xl"></i>
        </button>

        <div
            id="demo-popover"
            class="absolute left-0 top-16 w-[min(24rem,calc(100vw-2rem))] max-h-[78vh] overflow-hidden rounded-3xl border border-white/15 bg-slate-950/85 text-white shadow-[0_24px_80px_rgba(0,0,0,0.35)] backdrop-blur-xl opacity-0 pointer-events-none -translate-x-3 scale-95 transition duration-300"
        >
            <div class="flex items-start justify-between gap-4 border-b border-white/10 px-4 py-4">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-[0.35em] text-emerald-300">Demo Login</p>
                    <h2 class="mt-1 text-base font-semibold">Akun seeded siap pakai</h2>
                    <p class="mt-1 text-xs leading-5 text-white/60">Klik salah satu kartu untuk mengisi NIP dan password, lalu panel akan menutup otomatis.</p>
                </div>
                <button
                    type="button"
                    id="demo-close"
                    class="mt-1 inline-flex h-8 w-8 items-center justify-center rounded-full border border-white/10 bg-white/5 text-white/70 transition hover:bg-white/10 hover:text-white"
                    title="Tutup demo"
                >
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="max-h-[calc(78vh-5rem)] space-y-4 overflow-y-auto px-3 py-3 pb-12 scrollbar-modern">
                <?php $__currentLoopData = $demoGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <section class="rounded-2xl border border-white/10 bg-white/5 p-3">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-sm font-semibold text-white"><?php echo e($group['label']); ?></h3>
                                <p class="text-[11px] leading-4 text-white/55"><?php echo e($group['description']); ?></p>
                            </div>
                            <span class="rounded-full border border-<?php echo e($group['tone']); ?>-300/30 bg-<?php echo e($group['tone']); ?>-400/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-<?php echo e($group['tone']); ?>-200"><?php echo e(count($group['accounts'])); ?></span>
                        </div>

                        <div class="space-y-2">
                            <?php $__currentLoopData = $group['accounts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button
                                    type="button"
                                    onclick="fillDemoCredential(this)"
                                    class="demo-credential-card w-full rounded-2xl border border-white/10 bg-white/5 px-3 py-3 text-left transition hover:-translate-y-0.5 hover:border-white/25 hover:bg-white/10"
                                    data-demo-nip="<?php echo e($account['nip']); ?>"
                                    data-demo-password="<?php echo e($account['password']); ?>"
                                    data-demo-name="<?php echo e($account['name']); ?>"
                                    data-demo-role="<?php echo e($account['role_label']); ?>"
                                    data-demo-division="<?php echo e($account['division']); ?>"
                                >
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-white"><?php echo e($account['name']); ?></p>
                                            <p class="text-[11px] uppercase tracking-wider text-white/50"><?php echo e($account['role_label']); ?></p>
                                        </div>
                                        <span class="rounded-full border px-2 py-1 text-[10px] font-semibold uppercase tracking-wider <?php echo e($account['division_badge_class']); ?>"><?php echo e($account['division']); ?></span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between gap-3 text-xs text-white/70">
                                        <span class="font-semibold tracking-wide"><?php echo e($account['nip']); ?></span>
                                        <span class="font-mono text-[11px]">pwd: <?php echo e($account['password']); ?></span>
                                    </div>
                                </button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </section>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Main Login Form - Centered -->
    <div class="w-full max-w-md z-10">
        <!-- Main Card -->
        <div class="glass-effect rounded-2xl p-8 md:p-10">
            <!-- Logo / Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-green-400 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-leaf text-white text-2xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-1">VnB Platform</h1>
                <p class="text-gray-600 text-sm">Onboarding & Development System</p>
            </div>

            <!-- Form -->
            <form action="<?php echo e(route('login.post')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>

                <!-- NIP -->
                <div>
                    <label class="block text-gray-800 text-sm font-semibold mb-2">NIP</label>
                    <div class="relative">
                        <i class="fas fa-id-card absolute left-3 top-3.5 text-gray-500 text-sm"></i>
                        <input 
                            type="text" 
                            name="nip" 
                            value="<?php echo e(old('nip')); ?>" 
                            required
                            class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-sm"
                            placeholder="EMP1006"
                        >
                    </div>
                    <?php $__errorArgs = ['nip'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i><?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-gray-800 text-sm font-semibold mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3 top-3.5 text-gray-500 text-sm"></i>
                        <input 
                            id="login-password" 
                            type="password" 
                            name="password" 
                            required
                            class="input-field w-full pl-10 pr-16 py-3 rounded-lg text-sm"
                            placeholder="••••••••"
                        >
                        <button
                            type="button"
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 px-3 flex items-center justify-center text-gray-500 hover:text-gray-700 transition text-sm font-medium"
                            title="Toggle password visibility"
                        >
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i><?php echo e($message); ?>

                        </p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-2">
                    <input 
                        type="checkbox" 
                        name="remember" 
                        id="remember" 
                        class="w-4 h-4 rounded border-gray-300 text-green-600 cursor-pointer"
                    >
                    <label for="remember" class="ml-2 text-gray-700 text-sm cursor-pointer">Remember me for 30 days</label>
                </div>

                <!-- Login Button -->
                <button 
                    type="submit" 
                    class="btn-login w-full text-white font-semibold py-3 px-4 rounded-lg transition duration-300 flex items-center justify-center gap-2 mt-6"
                >
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </button>
            </form>

        </div>
    </div>

    <!-- Footer -->
    <p class="fixed bottom-6 left-0 right-0 text-center text-white text-xs opacity-80">
        © 2026 Wismilak — Values & Behavior Development Platform
    </p>

    <script>
        const demoFab = document.getElementById('demo-fab');
        const demoPopover = document.getElementById('demo-popover');
        const demoClose = document.getElementById('demo-close');
        const loginForm = document.querySelector('form');
        const nipField = document.querySelector('input[name="nip"]');
        const passwordField = document.querySelector('input[name="password"]');

        function openDemoPopover() {
            demoPopover.classList.remove('opacity-0', 'pointer-events-none', '-translate-x-3', 'scale-95');
            demoPopover.classList.add('opacity-100', 'translate-x-0', 'scale-100');
            demoFab.setAttribute('aria-expanded', 'true');
        }

        function closeDemoPopover() {
            demoPopover.classList.add('opacity-0', 'pointer-events-none', '-translate-x-3', 'scale-95');
            demoPopover.classList.remove('opacity-100', 'translate-x-0', 'scale-100');
            demoFab.setAttribute('aria-expanded', 'false');
        }

        function toggleDemoPopover() {
            const isOpen = demoFab.getAttribute('aria-expanded') === 'true';
            if (isOpen) {
                closeDemoPopover();
                return;
            }
            openDemoPopover();
        }

        function fillDemoCredential(button) {
            nipField.value = button.dataset.demoNip || '';
            passwordField.value = button.dataset.demoPassword || '';
            closeDemoPopover();
            nipField.focus();
        }

        window.fillDemoCredential = fillDemoCredential;

        demoFab?.addEventListener('click', toggleDemoPopover);
        demoClose?.addEventListener('click', closeDemoPopover);

        document.addEventListener('click', function(event) {
            if (!demoPopover || !demoFab) {
                return;
            }

            const target = event.target;
            if (!(target instanceof Node)) {
                return;
            }

            if (demoPopover.contains(target) || demoFab.contains(target)) {
                return;
            }

            closeDemoPopover();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDemoPopover();
            }
        });

        loginForm?.addEventListener('submit', function() {
            closeDemoPopover();
        });

        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const icon = this.querySelector('i');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>
<?php /**PATH C:\Users\USERR\Documents\0. Magang\Wismilak\VnB WebApp PHP\resources\views/auth/login.blade.php ENDPATH**/ ?>