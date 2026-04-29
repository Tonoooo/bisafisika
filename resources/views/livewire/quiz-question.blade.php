<!-- resources/views/livewire/quiz-question.blade.php -->
<div>
    {{-- ==================== OVERLAY "KLIK UNTUK MULAI" (Fullscreen trigger) ==================== --}}
    <div wire:ignore
         x-data="quizProctor({{ $violationCount }}, {{ $userQuizId }})"
         x-init="$nextTick(() => { typesetMath(); })"
         id="proctor-layer">

        {{-- Overlay start: user harus klik untuk masuk fullscreen --}}
        <div x-show="showStart" x-cloak
             style="display:none;"
             class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90">
            <div class="text-center p-8 max-w-md mx-4">
                <div class="mb-4">
                    <svg class="w-16 h-16 mx-auto text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-2">Quiz Siap Dimulai</h2>
                <p class="text-gray-300 mb-6">Klik tombol di bawah untuk masuk mode fullscreen dan memulai quiz.</p>
                <button @click="enterFullscreenAndStart()"
                        class="px-8 py-4 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition-colors text-lg shadow-lg cursor-pointer">
                    🖥️ Masuk Fullscreen & Mulai
                </button>
            </div>
        </div>

        {{-- Overlay peringatan kecurangan --}}
        <div x-show="showWarning" x-cloak
             style="display:none;"
             class="fixed inset-0 z-[9999] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/92"></div>
            <div class="relative text-center p-8 max-w-lg mx-4">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-red-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-red-500 mb-3">⚠️ Kecurangan Terdeteksi!</h2>
                <p class="text-white text-lg mb-2">Anda terdeteksi meninggalkan halaman ujian.</p>
                <p class="text-gray-300 text-base mb-6">
                    Pelanggaran ke-<span class="text-red-400 font-bold text-xl" x-text="violations"></span> dari 3.
                    <br>
                    <span class="text-red-400 font-semibold" x-show="violations >= 2">Peringatan terakhir! Pelanggaran berikutnya akan mengakhiri quiz Anda secara otomatis.</span>
                </p>
                <button @click="dismissWarning()"
                        class="px-8 py-3 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition-colors text-lg shadow-lg cursor-pointer">
                    Kembali ke Quiz (Fullscreen)
                </button>
            </div>
        </div>

        {{-- Overlay quiz diakhiri --}}
        <div x-show="quizEnded" x-cloak
             style="display:none;"
             class="fixed inset-0 z-[9999] flex items-center justify-center">
            <div class="absolute inset-0 bg-black/95"></div>
            <div class="relative text-center p-8 max-w-lg mx-4">
                <div class="mb-6">
                    <svg class="w-24 h-24 mx-auto text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-red-600 mb-3">Quiz Diakhiri!</h2>
                <p class="text-white text-lg mb-6">Quiz Anda telah diakhiri secara otomatis karena <strong class="text-red-400">3 pelanggaran</strong>.</p>
                <a href="{{ route('quiz.results', $userQuizId) }}"
                   class="inline-block px-8 py-3 bg-gray-700 text-white font-bold rounded-lg hover:bg-gray-600 transition-colors text-lg cursor-pointer">
                    Lihat Hasil Quiz
                </a>
            </div>
        </div>

        {{-- Indikator pelanggaran di navbar --}}
        <div class="fixed top-0 right-0 z-50" style="right: 120px; top: 8px;">
            <div class="flex items-center text-sm font-medium"
                 :class="violations > 0 ? 'text-red-600' : 'text-green-600'">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span x-text="violations + '/3'"></span>
            </div>
        </div>
    </div>

    {{-- ==================== HEADER / NAVBAR ==================== --}}
    <div class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200" style="background-color: #f2e2b1f0;">
        <div class="flex items-center justify-between px-4 py-2" style="background-color: #f2e2b1f0;">
            <button wire:click="cancelQuiz" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 cursor-pointer">
                <x-heroicon-o-x-circle class="w-5 h-5 mr-1.5" />
                Cancel
            </button>
            <div class="flex items-center">
                <img src="{{ asset('images/banner_logo.png') }}" alt="Logo" class="h-12">
            </div>
            <div class="flex items-center">
                <div class="flex items-center text-red-600 font-medium">
                    <x-heroicon-o-clock class="w-5 h-5 mr-1.5" />
                    <span x-data="{ timeLeft: {{ $timeLeft }} }"
                          x-init="
                            let timer = setInterval(() => {
                                if (timeLeft > 0) {
                                    timeLeft--;
                                    if (timeLeft <= 300) {
                                        $el.classList.add('text-red-600', 'font-bold');
                                    }
                                } else {
                                    clearInterval(timer);
                                    window.location.href = '{{ route('quiz.results', $userQuizId) }}';
                                }
                            }, 1000);
                          "
                          x-text="Math.floor(timeLeft / 60) + ':' + (timeLeft % 60).toString().padStart(2, '0')"
                          class="transition-colors duration-300">
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ==================== KONTEN SOAL ==================== --}}
    <div class="pt-4">
        <div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
            <div class="p-6 shadow sm:rounded-lg" style="background-color: #f2e2b1f0;"
                 wire:key="question-{{ $questionIndex }}">
                <h1 class="mb-4 text-2xl font-semibold text-gray-900">{{ $question->userQuiz->quiz->title }}</h1>
                <p class="mb-6 text-gray-600">Question {{ $questionIndex + 1 }} of {{ $totalQuestions }}</p>
                <div class="mb-6">
                    <p class="mb-4 text-lg" id="question-text">
                        {!! preg_replace_callback('/\d+\.\d+/', function($m) { return number_format($m[0], 2, '.', ''); }, $question->question_text)  !!}
                    </p>
                    @if($question->image_path)
                        <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image" class="mb-4 rounded-lg shadow-md">
                    @endif
                    <div id="answer-options">
                        @foreach($question->question->shuffled_answers as $answerOption)
                            <div class="mb-2">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio"
                                           name="quiz_answer"
                                           value="{{ $answerOption['content'] }}"
                                           @if($answer === $answerOption['content']) checked @endif
                                           class="text-blue-600 form-radio">
                                    <span class="ml-2 text-gray-700">
                                        {!! preg_replace_callback('/\d+\.\d+/', function($m) { return number_format($m[0], 2, '.', ''); }, '$$' . str_replace(' ', '\\ ', $answerOption['content']) . '$$') !!}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-between">
                    @if($questionIndex > 0)
                        <button onclick="navigateQuiz('previous')" type="button"
                           class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 cursor-pointer">
                            Previous Question
                        </button>
                    @else
                        <div></div>
                    @endif

                    @if($questionIndex + 1 < $totalQuestions)
                        <button onclick="navigateQuiz('next')" type="button" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                            Next Question
                        </button>
                    @else
                        <button onclick="navigateQuiz('finish')" type="button" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 cursor-pointer">
                            Finish Quiz
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function navigateQuiz(action) {
            var selected = document.querySelector('input[name="quiz_answer"]:checked');
            var answer = selected ? selected.value : null;

            if (action === 'next') {
                @this.nextQuestion(answer);
            } else if (action === 'previous') {
                @this.previousQuestion(answer);
            } else if (action === 'finish') {
                @this.finishQuiz(answer);
            }
        }
    </script>

    {{-- ==================== MATHJAX (konfigurasi dulu, baru load) ==================== --}}
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$$', '$$'], ['\\(', '\\)']],
                displayMath: [['\\[', '\\]']],
            },
            startup: {
                typeset: false
            }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    {{-- ==================== SCRIPT ANTI-KECURANGAN + MATHJAX RENDER ==================== --}}
    <script>
        // Fungsi global untuk render MathJax
        function typesetMathGlobal() {
            if (window.MathJax && MathJax.typesetPromise) {
                // Reset MathJax agar bisa re-scan elemen yang sudah diproses
                MathJax.startup.document.clear();
                MathJax.startup.document.updateDocument();
                MathJax.typesetPromise().catch(function(err) {
                    console.log('MathJax typeset error:', err);
                });
            }
        }

        // Render MathJax saat MathJax selesai dimuat
        if (window.MathJax && MathJax.startup) {
            MathJax.startup.promise.then(function() {
                typesetMathGlobal();
            });
        }

        // Render ulang setelah Livewire update (pindah soal)
        document.addEventListener('livewire:init', function() {
            Livewire.on('questionChanged', () => {
                setTimeout(typesetMathGlobal, 300);
            });
        });


        // Fallback: render saat DOM content loaded
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(typesetMathGlobal, 300);
        });

        function quizProctor(initialViolations, userQuizId) {
            const sessionKey = 'quiz_active_' + userQuizId;
            const isResuming = sessionStorage.getItem(sessionKey) === 'true';

            return {
                violations: initialViolations || 0,
                showWarning: false,
                showStart: !isResuming,
                quizEnded: false,
                isProcessing: false,
                proctorActive: isResuming,
                userQuizId: userQuizId,
                lastViolationTime: 0, // Timestamp pelanggaran terakhir

                init() {
                    if (isResuming) {
                        this.startListeners();
                        this.typesetMath();
                        this.requestFullscreen();
                    }
                },

                typesetMath() {
                    setTimeout(typesetMathGlobal, 200);
                },

                enterFullscreenAndStart() {
                    const el = document.documentElement;
                    const self = this;

                    const activate = () => {
                        sessionStorage.setItem(sessionKey, 'true');
                        self.showStart = false;
                        self.proctorActive = true;
                        self.typesetMath();
                        self.startListeners();
                    };

                    if (el.requestFullscreen) {
                        el.requestFullscreen().then(activate).catch(activate);
                    } else if (el.webkitRequestFullscreen) {
                        el.webkitRequestFullscreen();
                        activate();
                    } else {
                        activate();
                    }
                },

                startListeners() {
                    const self = this;

                    // Deteksi pindah tab (Page Visibility API)
                    document.addEventListener('visibilitychange', function() {
                        if (document.hidden && !self.quizEnded && self.proctorActive) {
                            self.handleViolation('tab_switch');
                        }
                    });

                    // Deteksi keluar fullscreen
                    document.addEventListener('fullscreenchange', function() {
                        if (!document.fullscreenElement && !self.quizEnded && self.proctorActive) {
                            self.handleViolation('fullscreen_exit');
                        }
                    });

                    // Deteksi pindah aplikasi (window blur)
                    // Hanya catat jika BUKAN disebabkan oleh fullscreen exit (yang sudah dicatat)
                    window.addEventListener('blur', function() {
                        if (!self.quizEnded && self.proctorActive) {
                            // Jika baru saja ada pelanggaran dalam 3 detik terakhir, abaikan blur
                            const now = Date.now();
                            if (now - self.lastViolationTime < 3000) {
                                return;
                            }
                            self.handleViolation('window_blur');
                        }
                    });
                },

                requestFullscreen() {
                    const el = document.documentElement;
                    if (el.requestFullscreen) {
                        el.requestFullscreen().catch(() => {});
                    } else if (el.webkitRequestFullscreen) {
                        el.webkitRequestFullscreen();
                    }
                },

                handleViolation(type) {
                    // Cegah duplikat: abaikan jika sedang proses atau baru terjadi < 3 detik
                    const now = Date.now();
                    if (this.isProcessing || this.quizEnded || !this.proctorActive) return;
                    if (now - this.lastViolationTime < 3000) return;

                    this.isProcessing = true;
                    this.lastViolationTime = now;

                    this.violations++;
                    this.showWarning = true;

                    @this.call('recordViolation', type);

                    if (this.violations >= 3) {
                        this.showWarning = false;
                        this.quizEnded = true;
                        sessionStorage.removeItem(sessionKey);
                        if (document.fullscreenElement) {
                            document.exitFullscreen().catch(() => {});
                        }
                    }

                    // Reset processing flag setelah 3 detik (sama dengan cooldown)
                    setTimeout(() => {
                        this.isProcessing = false;
                    }, 3000);
                },

                dismissWarning() {
                    this.showWarning = false;
                    this.requestFullscreen();
                    this.typesetMath();
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        #mobile-menu a[href="/admin"] { display: none !important; }
    </style>
</div>
