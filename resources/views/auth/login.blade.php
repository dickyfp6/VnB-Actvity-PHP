<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VnB Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-green-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo / Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">VnB Platform</h1>
                <p class="text-gray-600 text-sm mt-2">New Hire Onboarding & Management System</p>
            </div>

            <!-- Form -->
            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <!-- Email -->
                <div class="mb-5">
                        <label class="block text-gray-700 text-sm font-semibold mb-2">Email / NIP</label>
                    <input type="text" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"
                        placeholder="user@example.com atau NH-00001">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <div class="relative">
                        <input id="login-password" type="password" name="password" required
                            class="w-full px-4 py-2 pr-16 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"
                            placeholder="••••••••">
                        <button
                            type="button"
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 px-4 flex items-center justify-center text-gray-400"
                            aria-label="Show password"
                        >Show</button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded">
                    <label for="remember" class="ml-2 text-gray-700 text-sm">Remember me</label>
                </div>

                <!-- Login Button -->
                <button type="submit" class="w-full text-white font-semibold py-2 px-4 rounded-lg transition duration-200" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
                    Sign In
                </button>
            </form>

            <!-- Demo Users Info -->
            <div class="mt-6 p-4 rounded-lg" style="background-color: #f0fdf4; border-left: 4px solid #144600;">
                <p class="text-xs text-gray-700 mb-2"><strong>Demo Credentials:</strong></p>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><code class="bg-gray-200 px-2 py-1 rounded">admin@vnb.local</code> - Admin</p>
                    <p><code class="bg-gray-200 px-2 py-1 rounded">manager@vnb.local</code> - Manager</p>
                    <p><code class="bg-gray-200 px-2 py-1 rounded">newhire@vnb.local</code> - New Hire</p>
                    <p><code class="bg-gray-200 px-2 py-1 rounded">pcx@vnb.local</code> - PCX Manager</p>
                    <p><code class="bg-gray-200 px-2 py-1 rounded">intercomm@vnb.local</code> - Intercomm</p>
                    <p>Password: <code class="bg-gray-200 px-2 py-1 rounded">password</code></p>
                </div>
            </div>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-semibold hover:underline" style="color: #144600;">Sign Up</a>
                </p>
            </div>
        </div>
    </div>
</body>
<script>
    (function () {
        const passwordInput = document.getElementById('login-password');
        const toggleButton = document.getElementById('toggle-password');

        if (!passwordInput || !toggleButton) {
            return;
        }

        toggleButton.addEventListener('click', function () {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            toggleButton.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
            toggleButton.classList.toggle('text-blue-500', isHidden);
            toggleButton.classList.toggle('text-gray-400', !isHidden);

            toggleButton.innerHTML = isHidden
                ? '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.269 2.943 9.542 7-1.273 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.935 10.935 0 0 1 12 19c-4.478 0-8.269-2.943-9.542-7a10.94 10.94 0 0 1 2.744-4.448"></path><path d="M9.9 4.24A10.94 10.94 0 0 1 12 5c4.478 0 8.269 2.943 9.542 7a10.927 10.927 0 0 1-1.4 2.872"></path><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path><path d="M3 3l18 18"></path></svg>';
        });

        toggleButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.935 10.935 0 0 1 12 19c-4.478 0-8.269-2.943-9.542-7a10.94 10.94 0 0 1 2.744-4.448"></path><path d="M9.9 4.24A10.94 10.94 0 0 1 12 5c4.478 0 8.269 2.943 9.542 7a10.927 10.927 0 0 1-1.4 2.872"></path><path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"></path><path d="M3 3l18 18"></path></svg>';
    })();
</script>
</html>
