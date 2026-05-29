<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - WisCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-emerald-50 to-green-100 min-h-screen flex items-center justify-center py-8">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <!-- Logo / Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">WisCore</h1>
                <p class="text-gray-600 text-sm mt-2">Create Your Account</p>
            </div>

            <!-- Form -->
            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <!-- Name -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"
                        placeholder="John Doe">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"
                        placeholder="user@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Role</label>
                    <select name="role" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'">
                        <option value="">-- Select Role --</option>
                        <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Employee</option>
                        <option value="manager" {{ old('role') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="pcx_manager" {{ old('role') === 'pcx_manager' ? 'selected' : '' }}>PCX Manager</option>
                        <option value="intercomm" {{ old('role') === 'intercomm' ? 'selected' : '' }}>Intercomm</option>
                        <option value="direktur_utama" {{ old('role') === 'direktur_utama' ? 'selected' : '' }}>Direktur Utama</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" style="outline: none;" onmouseover="this.style.outline='2px solid #37AA05'" onmouseout="this.style.outline='none'"
                        placeholder="••••••••">
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Register Button -->
                <button type="submit" class="w-full text-white font-semibold py-2 px-4 rounded-lg transition duration-200" style="background-color: #144600; cursor: pointer;" onmouseover="this.style.backgroundColor='#37AA05'" onmouseout="this.style.backgroundColor='#144600'">
                    Create Account
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 text-sm">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-semibold hover:underline" style="color: #144600;">Sign In</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
