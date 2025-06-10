<div>
    <form wire:submit.prevent="register" class="space-y-5">
        <input type="hidden" wire:model="role" value="guru">
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
            <label for="school_id" class="block text-sm font-semibold text-gray-700 dark:text-gray-800 mb-1">Sekolah</label>
            <select wire:model="school_id" id="school_id" class="block w-full border-gray-300 rounded-lg shadow-sm p-3 bg-white focus:border-primary-500 focus:ring-primary-500">
                <option value="">Pilih Sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
                    <option value="">Lainnya</option>
            </select>
            @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="pt-4">
            <button type="submit" class="w-full p-3 rounded-md text-white font-semibold hover:opacity-90 transition shadow-md" style="background-color: #d5c7a3;">
                Daftar
            </button>
        </div>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{ route('filament.admin.auth.login') }}" class="text-primary-600 hover:underline bold" style="color: #5c70ca; font-weight: bold;">Login di sini</a></p>
        </div>
    </form>
</div> 