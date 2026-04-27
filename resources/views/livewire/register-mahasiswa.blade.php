<div>
    <form wire:submit.prevent="register" class="space-y-5">
        <input type="hidden" wire:model="role" value="mahasiswa">
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Nama</label>
            <input type="text" wire:model="name" id="name" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500" required />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Email</label>
            <input type="email" wire:model="email" id="email" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500" required />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Password</label>
            <input type="password" wire:model="password" id="password" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500" required />
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="school_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Program Studi</label>
            <select wire:model="school_id" id="school_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500">
                <option value="">Pilih Program Studi</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
                <option value="">Lainnya</option>
            </select>
            @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="level" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Tahun Angkatan</label>
            <input type="number" wire:model="level" id="level" min="2000" max="2100" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500" placeholder="Contoh: 2023" />
            @error('level') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="class" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Kelas</label>
            <select wire:model="class" id="class" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500">
                <option value="">Pilih Kelas</option>
                @foreach (range('a', 'z') as $letter)
                    <option value="{{ $letter }}">{{ strtoupper($letter) }}</option>
                @endforeach
                    <option value="">Lainnya</option>
            </select>
            @error('class') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="pt-4">
            <button type="submit" class="w-full p-3 rounded-md text-white font-semibold hover:opacity-90 transition shadow-md" style="background-color: #ccad5e;">
                Daftar
            </button>
        </div>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{ route('filament.admin.auth.login') }}" class="text-primary-600 hover:underline bold" style="color: #5c70ca; font-weight: bold;">Login di sini</a></p>
        </div>
    </form>
</div> 