<div class="space-y-6">

    <!-- Nama Lengkap -->
    <div>
        <label class="font-semibold text-gray-700">Nama Lengkap</label>
        <input type="text" name="nama_lengkap"
            value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}"
            class="w-full p-4 bg-gray-50 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 outline-none">
    </div>

    <!-- Email -->
    <div>
        <label class="font-semibold text-gray-700">Email</label>
        <input type="email" name="email"
            value="{{ old('email', $user->email ?? '') }}"
            class="w-full p-4 bg-gray-50 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 outline-none">
    </div>

    <!-- Nomor HP -->
    <div>
        <label class="font-semibold text-gray-700">Nomor HP</label>
        <input type="text" name="no_hp"
            value="{{ old('no_hp', $user->no_hp ?? '') }}"
            class="w-full p-4 bg-gray-50 rounded-xl border border-gray-300 focus:ring focus:ring-blue-200 outline-none">
    </div>
</div>
