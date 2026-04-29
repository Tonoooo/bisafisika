<!-- resources/views/livewire/quiz-preparation.blade.php -->
<div class="min-h-screen flex items-center justify-center py-8 px-4" style="background-color: #f6f0f0;">
    <div class="max-w-xl w-full">
        {{-- Card utama --}}
        <div class="rounded-2xl shadow-2xl overflow-hidden" style="background-color: #f2e2b1;">
            {{-- Header --}}
            <div class="px-8 pt-8 pb-4 text-center" style="background-color: #d5c7a3;">
                <img src="{{ asset('images/einstein.png') }}" alt="Logo" class="w-20 h-20 mx-auto mb-3">
                <h1 class="text-2xl font-bold text-gray-800">Persiapan Quiz</h1>
            </div>

            {{-- Info Quiz --}}
            <div class="px-8 py-6 space-y-4">
                {{-- Bab --}}
                @if($quiz->bab)
                <div class="flex items-center p-4 rounded-xl bg-white/60">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #ccad5e;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Bab</p>
                        <p class="text-lg font-bold text-gray-800">{{ $quiz->bab->name }}</p>
                    </div>
                </div>
                @endif

                {{-- Judul Quiz --}}
                <div class="flex items-center p-4 rounded-xl bg-white/60">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center bg-blue-500">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Quiz</p>
                        <p class="text-lg font-bold text-gray-800">{{ $quiz->title }}</p>
                    </div>
                </div>

                {{-- Info baris --}}
                <div class="grid grid-cols-2 gap-3">
                    {{-- Waktu --}}
                    <div class="flex items-center p-3 rounded-xl bg-white/60">
                        <svg class="w-8 h-8 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Waktu</p>
                            <p class="text-base font-bold text-gray-800">{{ $quiz->time_limit }} menit</p>
                        </div>
                    </div>

                    {{-- Jumlah Soal --}}
                    <div class="flex items-center p-3 rounded-xl bg-white/60">
                        <svg class="w-8 h-8 text-purple-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Jumlah Soal</p>
                            <p class="text-base font-bold text-gray-800">{{ $quiz->questions->count() }} soal</p>
                        </div>
                    </div>

                    {{-- Sisa Percobaan --}}
                    <div class="flex items-center p-3 rounded-xl bg-white/60">
                        <svg class="w-8 h-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Sisa Percobaan</p>
                            <p class="text-base font-bold text-gray-800">{{ $remainingAttempts }}x</p>
                        </div>
                    </div>

                    {{-- Jumlah Soal --}}
                    <div class="flex items-center p-3 rounded-xl bg-white/60">
                        <svg class="w-8 h-8 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Pelanggaran</p>
                            <p class="text-base font-bold text-gray-800">Maks 3x</p>
                        </div>
                    </div>
                </div>

                {{-- Peringatan Anti Kecurangan --}}
                <div class="p-4 rounded-xl bg-red-50 border border-red-200">
                    <div class="flex items-start">
                        <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <div>
                            <p class="font-bold text-red-700 text-sm mb-1">⚠️ Mode Ujian Ketat</p>
                            <ul class="text-red-600 text-xs space-y-1">
                                <li>• Layar akan otomatis <strong>fullscreen</strong> saat quiz dimulai</li>
                                <li>• <strong>Dilarang</strong> pindah tab, pindah aplikasi, atau keluar fullscreen</li>
                                <li>• Setiap pelanggaran akan <strong>dicatat dan dilaporkan</strong></li>
                                <li>• Setelah <strong>3 pelanggaran</strong>, quiz akan diakhiri otomatis</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="px-8 pb-8 pt-2 space-y-3">
                @if($remainingAttempts > 0)
                    <a href="{{ route('quiz.start', $quiz->id) }}"
                       class="block w-full py-4 rounded-xl text-white font-bold text-lg shadow-lg hover:opacity-90 transition-all transform hover:scale-[1.02] cursor-pointer text-center"
                       style="background-color: #ccad5e;">
                        🚀 Mulai Quiz
                    </a>
                @else
                    <div class="w-full py-4 rounded-xl text-center text-white font-bold text-lg bg-gray-400">
                        Percobaan Habis
                    </div>
                @endif

                <a href="{{ route('quiz.list') }}"
                   class="block w-full py-3 rounded-xl text-center text-gray-600 font-medium text-base bg-white/60 hover:bg-white/80 transition-colors">
                    ← Kembali ke Daftar Quiz
                </a>
            </div>
        </div>
    </div>
</div>
