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
            <form action="{{ route('login.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email / NIP -->
                <div>
                    <label class="block text-gray-800 text-sm font-semibold mb-2">Email / NIP</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3 top-3.5 text-gray-500 text-sm"></i>
                        <input 
                            type="text" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required
                            class="input-field w-full pl-10 pr-4 py-3 rounded-lg text-sm"
                            placeholder="user@example.com atau NH-00001"
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p>
                    @enderror
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
                    @error('password')
                        <p class="text-red-500 text-xs mt-2 flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>{{ $message }}
                        </p>
                    @enderror
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

            <!-- Demo Credentials Section -->
            <div class="mt-8 pt-8 border-t border-white/20">
                <p class="text-gray-700 text-xs font-semibold uppercase tracking-wider mb-3">Demo Accounts</p>
                <div class="space-y-2">
                    <button type="button" onclick="document.querySelector('input[name=email]').value='newhire@vnb.id'; document.querySelector('input[name=password]').value='password'" class="w-full text-left p-3 rounded-lg bg-gradient-to-r from-green-100 to-green-50 border border-green-300 hover:shadow-md hover:border-green-400 transition">
                        <span class="block text-sm font-semibold text-green-900">New Hire</span>
                        <span class="text-xs text-green-700">newhire@vnb.id</span>
                    </button>
                    <button type="button" onclick="document.querySelector('input[name=email]').value='manager@vnb.id'; document.querySelector('input[name=password]').value='password'" class="w-full text-left p-3 rounded-lg bg-gradient-to-r from-green-100 to-green-50 border border-green-300 hover:shadow-md hover:border-green-400 transition">
                        <span class="block text-sm font-semibold text-green-900">Manager</span>
                        <span class="text-xs text-green-700">manager@vnb.id</span>
                    </button>
                    <button type="button" onclick="document.querySelector('input[name=email]').value='pcx@vnb.id'; document.querySelector('input[name=password]').value='password'" class="w-full text-left p-3 rounded-lg bg-gradient-to-r from-green-100 to-green-50 border border-green-300 hover:shadow-md hover:border-green-400 transition">
                        <span class="block text-sm font-semibold text-green-900">PCX Manager</span>
                        <span class="text-xs text-green-700">pcx@vnb.id</span>
                    </button>
                    <button type="button" onclick="document.querySelector('input[name=email]').value='intercomm@vnb.id'; document.querySelector('input[name=password]').value='password'" class="w-full text-left p-3 rounded-lg bg-gradient-to-r from-green-100 to-green-50 border border-green-300 hover:shadow-md hover:border-green-400 transition">
                        <span class="block text-sm font-semibold text-green-900">Intercomm</span>
                        <span class="text-xs text-green-700">intercomm@vnb.id</span>
                    </button>
                </div>
                <p class="text-center text-gray-600 text-xs mt-2">All passwords: <span class="font-mono font-semibold">password</span></p>
            </div>
        </div>

        <!-- Footer -->
        <p class="text-center text-white text-xs mt-6 opacity-80">
            © 2026 Wismilak — Values & Behavior Development Platform
        </p>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordField = document.getElementById('login-password');
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
