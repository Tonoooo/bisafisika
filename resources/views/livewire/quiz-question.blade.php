<!-- resources/views/livewire/quiz-question.blade.php -->
<div>
    {{-- ==================== OVERLAY "KLIK UNTUK MULAI" (Fullscreen trigger) ==================== --}}
    <div wire:ignore
         x-data="quizProctor({{ $violationCount }}, {{ $userQuizId }})"
         x-init="$nextTick(() => { typesetMath(); })"
         id="proctor-layer">

        {{-- Overlay start: user harus klik untuk masuk fullscreen --}}
        <div x-show="showStart" x-cloak>
            <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.90);">
                <div style="text-align:center; padding:2rem; max-width:28rem; margin:0 1rem;">
                    <div style="margin-bottom:1rem;">
                        <svg style="width:4rem; height:4rem; margin:0 auto; color:#4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.5rem; font-weight:700; color:#ffffff; margin-bottom:0.5rem;">Quiz Siap Dimulai</h2>
                    <p style="color:#d1d5db; margin-bottom:1.5rem;">Klik tombol di bawah untuk masuk mode fullscreen dan memulai quiz.</p>
                    <button @click="enterFullscreenAndStart()"
                            style="padding:1rem 2rem; background-color:#16a34a; color:#ffffff; font-weight:700; border-radius:0.75rem; font-size:1.125rem; box-shadow:0 4px 14px rgba(22,163,74,0.4); cursor:pointer; border:none; transition:background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#15803d'" onmouseout="this.style.backgroundColor='#16a34a'">
                        🖥️ Masuk Fullscreen & Mulai
                    </button>
                </div>
            </div>
        </div>

        {{-- Overlay peringatan kecurangan --}}
        <div x-show="showWarning" x-cloak>
            <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; display:flex; align-items:center; justify-content:center;">
                <div style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.92);"></div>
                <div style="position:relative; text-align:center; padding:2rem; max-width:32rem; margin:0 1rem;">
                    <div style="margin-bottom:1.5rem;">
                        <svg style="width:6rem; height:6rem; margin:0 auto; color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.875rem; font-weight:700; color:#ef4444; margin-bottom:0.75rem;">⚠️ Kecurangan Terdeteksi!</h2>
                    <p style="color:#ffffff; font-size:1.125rem; margin-bottom:0.5rem;">Anda terdeteksi meninggalkan halaman ujian.</p>
                    <p style="color:#d1d5db; font-size:1rem; margin-bottom:1.5rem;">
                        Pelanggaran ke-<span style="color:#f87171; font-weight:700; font-size:1.25rem;" x-text="violations"></span> dari 3.
                        <br>
                        <span style="color:#f87171; font-weight:600;" x-show="violations >= 2">Peringatan terakhir! Pelanggaran berikutnya akan mengakhiri quiz Anda secara otomatis.</span>
                    </p>
                    <button @click="dismissWarning()"
                            style="padding:0.75rem 2rem; background-color:#dc2626; color:#ffffff; font-weight:700; border-radius:0.5rem; font-size:1.125rem; box-shadow:0 4px 14px rgba(220,38,38,0.4); cursor:pointer; border:none; transition:background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#b91c1c'" onmouseout="this.style.backgroundColor='#dc2626'">
                        Kembali ke Quiz (Fullscreen)
                    </button>
                </div>
            </div>
        </div>

        {{-- Overlay quiz diakhiri --}}
        <div x-show="quizEnded" x-cloak>
            <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; display:flex; align-items:center; justify-content:center;">
                <div style="position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.95);"></div>
                <div style="position:relative; text-align:center; padding:2rem; max-width:32rem; margin:0 1rem;">
                    <div style="margin-bottom:1.5rem;">
                        <svg style="width:6rem; height:6rem; margin:0 auto; color:#dc2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.875rem; font-weight:700; color:#dc2626; margin-bottom:0.75rem;">Quiz Diakhiri!</h2>
                    <p style="color:#ffffff; font-size:1.125rem; margin-bottom:1.5rem;">Quiz Anda telah diakhiri secara otomatis karena <strong style="color:#f87171;">3 pelanggaran</strong>.</p>
                    <a href="{{ route('quiz.results', $userQuizId) }}"
                       style="display:inline-block; padding:0.75rem 2rem; background-color:#374151; color:#ffffff; font-weight:700; border-radius:0.5rem; font-size:1.125rem; text-decoration:none; cursor:pointer;"
                       onmouseover="this.style.backgroundColor='#4b5563'" onmouseout="this.style.backgroundColor='#374151'">
                        Lihat Hasil Quiz
                    </a>
                </div>
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
                proctorActive: isResuming,
                userQuizId: userQuizId,

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

                    // SATU-SATUNYA trigger pelanggaran: Page Visibility API
                    // Ini mendeteksi: pindah tab, Alt+Tab, minimize, klik aplikasi lain
                    document.addEventListener('visibilitychange', function() {
                        if (document.hidden && !self.quizEnded && self.proctorActive) {
                            self.violations++;
                            self.showWarning = true;
                            @this.call('recordViolation', 'tab_switch');

                            if (self.violations >= 3) {
                                self.showWarning = false;
                                self.quizEnded = true;
                                sessionStorage.removeItem(sessionKey);
                                if (document.fullscreenElement) {
                                    document.exitFullscreen().catch(() => {});
                                }
                            }
                        }
                    });

                    // Fullscreen exit: BUKAN pelanggaran, hanya auto re-enter
                    // Jika user tekan Escape, otomatis masuk fullscreen lagi
                    document.addEventListener('fullscreenchange', function() {
                        if (!document.fullscreenElement && !self.quizEnded && self.proctorActive && !self.showWarning) {
                            // Coba masuk fullscreen lagi otomatis
                            setTimeout(() => {
                                if (!self.quizEnded && self.proctorActive && !self.showWarning) {
                                    self.requestFullscreen();
                                }
                            }, 500);
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
