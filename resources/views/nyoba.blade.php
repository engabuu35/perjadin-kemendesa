<?php

// ========================================
// 1. DATABASE MIGRATION
// ========================================
// File: database/migrations/xxxx_add_roles_to_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRolesToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('roles')->nullable()->after('email');
            $table->string('current_role')->nullable()->after('roles');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['roles', 'current_role']);
        });
    }
}

// ========================================
// 2. USER MODEL
// ========================================
// File: app/Models/User.php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'roles',
        'current_role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'roles' => 'array',
        'email_verified_at' => 'datetime',
    ];

    public function hasMultipleRoles()
    {
        return is_array($this->roles) && count($this->roles) > 1;
    }

    public function hasRole($role)
    {
        return is_array($this->roles) && in_array($role, $this->roles);
    }

    public function needsRoleSelection()
    {
        return $this->hasMultipleRoles() && empty($this->current_role);
    }
}

// ========================================
// 3. LOGIN CONTROLLER
// ========================================
// File: app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Reset current role saat login baru
            $user->current_role = null;
            $user->save();

            // Jika user punya multiple roles, redirect ke halaman pilih role
            if ($user->hasMultipleRoles()) {
                return redirect()->route('role.select');
            }

            // Jika hanya 1 role, langsung set dan redirect ke dashboard
            if (is_array($user->roles) && count($user->roles) === 1) {
                $user->current_role = $user->roles[0];
                $user->save();
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->withInput($request->only('username'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $user->current_role = null;
            $user->save();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

// ========================================
// 4. ROLE SELECTION CONTROLLER
// ========================================
// File: app/Http/Controllers/RoleSelectionController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleSelectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();

        // Jika user tidak punya multiple roles, redirect ke dashboard
        if (!$user->hasMultipleRoles()) {
            return redirect('/dashboard');
        }

        return view('auth.select-role', [
            'roles' => $user->roles,
            'user' => $user
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|string',
        ]);

        $user = Auth::user();

        // Validasi bahwa role yang dipilih ada dalam daftar roles user
        if (!$user->hasRole($request->role)) {
            return back()->withErrors(['role' => 'Role tidak valid.']);
        }

        // Set current role
        $user->current_role = $request->role;
        $user->save();

        return redirect()->intended('/dashboard')->with('success', 'Role berhasil dipilih!');
    }

    public function switch()
    {
        $user = Auth::user();

        // Reset current role
        $user->current_role = null;
        $user->save();

        return redirect()->route('role.select');
    }
}

// ========================================
// 5. MIDDLEWARE UNTUK CEK ROLE SELECTION
// ========================================
// File: app/Http/Middleware/EnsureRoleIsSelected.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureRoleIsSelected
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Jika user butuh pilih role tapi belum, redirect ke halaman pilih role
            if ($user->needsRoleSelection() && !$request->routeIs('role.*')) {
                return redirect()->route('role.select');
            }
        }

        return $next($request);
    }
}

// ========================================
// 6. REGISTER MIDDLEWARE
// ========================================

// UNTUK LARAVEL 11+
// File: bootstrap/app.php

use App\Http\Middleware\EnsureRoleIsSelected;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role.selected' => EnsureRoleIsSelected::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// ========================================
// ATAU UNTUK LARAVEL 10 KE BAWAH
// ========================================
// File: app/Http/Kernel.php
// Tambahkan di $middlewareAliases atau $routeMiddleware:

protected $middlewareAliases = [
    // ... middleware lainnya
    'role.selected' => \App\Http\Middleware\EnsureRoleIsSelected::class,
];

// Atau jika pakai Laravel 9 ke bawah:
protected $routeMiddleware = [
    // ... middleware lainnya
    'role.selected' => \App\Http\Middleware\EnsureRoleIsSelected::class,
];

// ========================================
// 7. ROUTES
// ========================================
// File: routes/web.php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\RoleSelectionController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Route untuk pilih role
    Route::get('/select-role', [RoleSelectionController::class, 'show'])->name('role.select');
    Route::post('/select-role', [RoleSelectionController::class, 'store'])->name('role.store');
    Route::post('/switch-role', [RoleSelectionController::class, 'switch'])->name('role.switch');
});

// Route yang butuh role sudah dipilih
Route::middleware(['auth', 'role.selected'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    // Route lainnya yang butuh authentication dan role selection
});

// ========================================
// 8. VIEW - LOGIN
// ========================================
// File: resources/views/auth/login.blade.php

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - NAMA SISTEM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .header {
            background: #d3d3d3;
            padding: 15px 30px;
            font-weight: bold;
            font-size: 18px;
            font-style: italic;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px);
            padding: 20px;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .user-icon {
            width: 120px;
            height: 120px;
            background: #e0e0e0;
            border-radius: 50%;
            margin: 0 auto 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #999;
        }
        .user-icon::before {
            content: '';
            width: 50px;
            height: 50px;
            background: #888;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .input-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
            font-style: italic;
            text-align: center;
        }
        input:focus {
            outline: none;
            border-color: #999;
        }
        .btn-login {
            width: 100%;
            padding: 15px;
            background: #d3d3d3;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .btn-login:hover {
            background: #c0c0c0;
        }
        .forgot-password {
            color: #999;
            font-style: italic;
            font-size: 14px;
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-bottom: 15px;
            background: #fadbd8;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">NAMA SISTEM</div>
    
    <div class="container">
        <div class="login-box">
            <div class="user-icon"></div>
            
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group">
                    <input type="text" name="username" placeholder="USERNAME (NIP/NIK)" 
                           value="{{ old('username') }}" required autofocus>
                </div>
                
                <div class="input-group">
                    <input type="password" name="password" placeholder="PASSWORD" required>
                </div>
                
                <button type="submit" class="btn-login">Log in</button>
                
                <a href="#" class="forgot-password">Forgot Password?</a>
            </form>
        </div>
    </div>
</body>
</html>

// ========================================
// 9. VIEW - SELECT ROLE
// ========================================
// File: resources/views/auth/select-role.blade.php

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - NAMA SISTEM</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        .header {
            background: #d3d3d3;
            padding: 15px 30px;
            font-weight: bold;
            font-size: 18px;
            font-style: italic;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 60px);
            padding: 20px;
        }
        .role-box {
            background: white;
            padding: 50px 60px;
            border-radius: 15px;
            border: 3px solid #333;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-style: italic;
            margin-bottom: 40px;
            font-size: 16px;
        }
        .select-wrapper {
            position: relative;
            margin-bottom: 30px;
        }
        select {
            width: 100%;
            padding: 15px 40px 15px 15px;
            border: 2px solid #333;
            border-radius: 8px;
            font-size: 14px;
            font-style: italic;
            background: white;
            cursor: pointer;
            appearance: none;
        }
        select:focus {
            outline: none;
            border-color: #555;
        }
        .select-wrapper::after {
            content: '▼';
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #333;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #d3d3d3;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-submit:hover {
            background: #c0c0c0;
        }
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-bottom: 15px;
            background: #fadbd8;
            padding: 10px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">NAMA SISTEM</div>
    
    <div class="container">
        <div class="role-box">
            <h2>Pilih Role Anda</h2>
            <p class="subtitle">Selamat datang, {{ $user->name }}</p>
            
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('role.store') }}">
                @csrf
                <div class="select-wrapper">
                    <select name="role" required>
                        <option value="">Pilih Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="btn-submit">Lanjutkan</button>
            </form>
        </div>
    </div>
</body>
</html>

// ========================================
// 10. CONTOH DASHBOARD VIEW
// ========================================
// File: resources/views/dashboard.blade.php

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NAMA SISTEM</title>
</head>
<body>
    <div class="header">
        <h1>Dashboard</h1>
        <p>Selamat datang, {{ Auth::user()->name }}</p>
        <p>Role aktif: {{ Auth::user()->current_role }}</p>
        
        @if(Auth::user()->hasMultipleRoles())
            <form method="POST" action="{{ route('role.switch') }}" style="display: inline;">
                @csrf
                <button type="submit">Ganti Role</button>
            </form>
        @endif
        
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
    
    <div class="content">
        <!-- Konten dashboard Anda -->
    </div>
</body>
</html>

// ========================================
// 11. SEEDER UNTUK TESTING (OPTIONAL)
// ========================================
// File: database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // User dengan single role
        User::create([
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'roles' => ['admin'],
        ]);

        // User dengan multiple roles
        User::create([
            'name' => 'Multi Role User',
            'username' => 'multi',
            'email' => 'multi@example.com',
            'password' => Hash::make('password'),
            'roles' => ['admin', 'manager', 'user'],
        ]);
    }
}

// ========================================
// CARA PENGGUNAAN:
// ========================================
// 1. Jalankan migration: php artisan migrate
// 2. (Optional) Jalankan seeder: php artisan db:seed --class=UserSeeder
// 3. Pastikan User model sudah ada kolom 'username'
// 4. Login dengan username dan password
// 5. Jika user punya multiple roles, akan otomatis redirect ke halaman pilih role
// 6. Setelah pilih role, bisa akses dashboard
// 7. User bisa ganti role kapan saja dengan tombol "Ganti Role"