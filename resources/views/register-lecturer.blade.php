<x-layouts.guest>
    <div class="container mx-auto px-4 py-8 md:mt-10">
        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Daftar sebagai Lecturer</h2>

            <form method="POST" action="{{ route('register.submit') }}">
                @csrf

                {{-- Hidden field untuk role --}}
                <input type="hidden" name="role" value="super_admin">

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nama:</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                    <input type="password" id="password" name="password" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror">
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">Konfirmasi Password:</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                 {{-- School ID diperlukan di controller, Anda mungkin ingin menambahkannya di form --}}
                 {{-- Atau menentukannya otomatis jika logikanya memungkinkan --}}
                 {{-- Contoh sederhana: dropdown pilih sekolah --}}
                 {{-- <div class="mb-4">
                     <label for="school_id" class="block text-gray-700 text-sm font-bold mb-2">Sekolah:</label>
                     <select id="school_id" name="school_id" required
                             class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('school_id') border-red-500 @enderror">
                         <option value="">-- Pilih Sekolah --</option>
                         @foreach (\App\Models\School::all() as $school) 
                             <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                         @endforeach
                     </select>
                     @error('school_id')
                         <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                     @enderror
                 </div> --}}


                <div class="flex items-center justify-between">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Daftar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.guest>
