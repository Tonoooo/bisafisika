<div>
    <form wire:submit.prevent="register">
        <input type="hidden" wire:model="role" value="guru">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Nama</label>
            <input type="text" wire:model="name" id="name" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required />
            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="mt-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" wire:model="email" id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required />
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" wire:model="password" id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required />
            @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="mt-4">
            <label for="school_id" class="block text-sm font-medium text-gray-700">Sekolah</label>
            <select wire:model="school_id" id="school_id" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" required>
                <option value="">Pilih Sekolah</option>
                @foreach ($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                @endforeach
            </select>
            @error('school_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <div class="mt-6">
            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                Daftar
            </button>
        </div>
    </form>
</div> 