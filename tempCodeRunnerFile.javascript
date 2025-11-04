import React, { useState } from 'react';
import { ChevronDown } from 'lucide-react';

export default function LoginRoleSystem() {
  const [currentPage, setCurrentPage] = useState('login');
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [selectedRole, setSelectedRole] = useState('');
  const [isDropdownOpen, setIsDropdownOpen] = useState(false);

  // Data dummy untuk validasi login
  const validUsers = {
    'admin': 'admin123',
    'user1': 'pass123'
  };

  // Role options
  const roles = ['Admin', 'Manager', 'Staff', 'User'];

  const handleLogin = () => {
    if (validUsers[username] === password) {
      setCurrentPage('selectRole');
    } else {
      alert('Username atau password salah!');
    }
  };

  const handleRoleSubmit = () => {
    if (selectedRole) {
      alert(`Berhasil login sebagai ${selectedRole}!\n\nUsername: ${username}\nRole: ${selectedRole}`);
      // Di sini bisa redirect ke dashboard atau halaman utama
    } else {
      alert('Silakan pilih role terlebih dahulu!');
    }
  };

  const handleForgotPassword = () => {
    alert('Fitur reset password akan dikirim ke email Anda.');
  };

  const handleKeyPress = (e, action) => {
    if (e.key === 'Enter') {
      action();
    }
  };

  if (currentPage === 'login') {
    return (
      <div className="min-h-screen bg-gray-100 flex items-center justify-center p-4">
        <div className="w-full max-w-md">
          <div className="bg-gray-300 px-6 py-4 rounded-t-lg">
            <h1 className="text-xl font-bold italic">NAMA SISTEM</h1>
          </div>
          
          <div className="bg-white px-8 py-12 rounded-b-lg shadow-lg">
            <div className="flex justify-center mb-8">
              <div className="w-32 h-32 bg-gray-400 rounded-full flex items-center justify-center">
                <div className="w-12 h-12 bg-gray-500 rounded-full mb-2"></div>
              </div>
            </div>

            <div className="space-y-4">
              <div>
                <input
                  type="text"
                  placeholder="USERNAME (NIP/NIK)"
                  value={username}
                  onChange={(e) => setUsername(e.target.value)}
                  onKeyPress={(e) => handleKeyPress(e, handleLogin)}
                  className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-gray-400 text-center italic"
                />
              </div>

              <div>
                <input
                  type="password"
                  placeholder="PASSWORD"
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  onKeyPress={(e) => handleKeyPress(e, handleLogin)}
                  className="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-gray-400 text-center italic"
                />
              </div>

              <button
                onClick={handleLogin}
                className="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 rounded-lg transition-colors italic"
              >
                Log in
              </button>

              <div className="text-center">
                <button
                  onClick={handleForgotPassword}
                  className="text-gray-500 hover:text-gray-700 text-sm italic"
                >
                  Forgot Password?
                </button>
              </div>
            </div>

            <div className="mt-6 p-3 bg-blue-50 border border-blue-200 rounded">
              <p className="text-xs text-gray-600">
                <strong>Demo credentials:</strong><br/>
                Username: admin | Password: admin123<br/>
                Username: user1 | Password: pass123
              </p>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-100 flex items-center justify-center p-4">
      <div className="w-full max-w-md">
        <div className="bg-gray-300 px-6 py-4 rounded-t-lg">
          <h1 className="text-xl font-bold italic">NAMA SISTEM</h1>
        </div>
        
        <div className="bg-white px-8 py-12 rounded-b-lg shadow-lg">
          <div className="border-2 border-gray-300 rounded-2xl p-8">
            <h2 className="text-3xl font-bold text-gray-700 text-center mb-2">
              Pilih Role Anda
            </h2>
            <p className="text-gray-500 text-center mb-8 italic">
              Selamat datang, {username}
            </p>

            <div className="space-y-6">
              <div className="relative">
                <button
                  onClick={() => setIsDropdownOpen(!isDropdownOpen)}
                  className="w-full px-4 py-3 border-2 border-gray-300 rounded-full focus:outline-none focus:border-gray-400 text-left text-gray-500 italic flex items-center justify-between"
                >
                  <span>{selectedRole || 'Pilih Role'}</span>
                  <ChevronDown className={`w-5 h-5 transition-transform ${isDropdownOpen ? 'rotate-180' : ''}`} />
                </button>

                {isDropdownOpen && (
                  <div className="absolute top-full left-0 right-0 mt-2 bg-white border-2 border-gray-300 rounded-lg shadow-lg z-10">
                    {roles.map((role) => (
                      <button
                        key={role}
                        onClick={() => {
                          setSelectedRole(role);
                          setIsDropdownOpen(false);
                        }}
                        className="w-full px-4 py-3 text-left hover:bg-gray-100 first:rounded-t-lg last:rounded-b-lg transition-colors"
                      >
                        {role}
                      </button>
                    ))}
                  </div>
                )}
              </div>

              <button
                onClick={handleRoleSubmit}
                className="w-full bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 rounded-full transition-colors italic"
              >
                Lanjutkan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}