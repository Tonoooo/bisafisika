<!-- resources/views/livewire/quiz-question.blade.php -->
<div>
    {{-- ==================== PROCTORING OVERLAY ==================== --}}
    <div wire:ignore
         x-data="quizProctor({{ $violationCount }}, {{ $userQuizId }})"
         x-init="$nextTick(() => { typesetMath(); })"
         id="proctor-layer">

        {{-- Overlay start --}}
        <div x-show="showStart" x-cloak>
            <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.92);">
                <div style="text-align:center;padding:2rem;max-width:28rem;margin:0 1rem;">
                    <div style="width:5rem;height:5rem;margin:0 auto 1.5rem;background:rgba(74,222,128,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:3rem;height:3rem;color:#4ade80;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.5rem;font-weight:700;color:#fff;margin-bottom:0.5rem;">Quiz Siap Dimulai</h2>
                    <p style="color:#d1d5db;margin-bottom:1.5rem;">Klik tombol di bawah untuk masuk mode fullscreen dan memulai quiz.</p>
                    <button @click="enterFullscreenAndStart()"
                            style="padding:1rem 2rem;background:#16a34a;color:#fff;font-weight:700;border-radius:9999px;font-size:1.1rem;box-shadow:0 4px 14px rgba(22,163,74,0.4);cursor:pointer;border:none;">
                        🖥️ Masuk Fullscreen & Mulai
                    </button>
                </div>
            </div>
        </div>

        {{-- Overlay peringatan kecurangan --}}
        <div x-show="showWarning" x-cloak>
            <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;">
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.92);"></div>
                <div style="position:relative;text-align:center;padding:2rem;max-width:32rem;margin:0 1rem;">
                    <div style="width:5rem;height:5rem;margin:0 auto 1.5rem;background:rgba(239,68,68,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                        <svg style="width:3rem;height:3rem;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h2 style="font-size:1.5rem;font-weight:700;color:#ef4444;margin-bottom:0.75rem;">⚠️ Kecurangan Terdeteksi!</h2>
                    <p style="color:#fff;font-size:1.1rem;margin-bottom:0.5rem;">Anda terdeteksi meninggalkan halaman ujian.</p>
                    <p style="color:#d1d5db;margin-bottom:1.5rem;">
                        Pelanggaran ke-<span style="color:#f87171;font-weight:700;font-size:1.25rem;" x-text="violations"></span> dari 3.
                        <br><span style="color:#f87171;font-weight:600;" x-show="violations >= 2">Peringatan terakhir!</span>
                    </p>
                    <button @click="dismissWarning()" style="padding:0.75rem 2rem;background:#dc2626;color:#fff;font-weight:700;border-radius:9999px;font-size:1.1rem;cursor:pointer;border:none;">
                        Kembali ke Quiz (Fullscreen)
                    </button>
                </div>
            </div>
        </div>

        {{-- Overlay quiz diakhiri --}}
        <div x-show="quizEnded" x-cloak>
            <div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;display:flex;align-items:center;justify-content:center;">
                <div style="position:absolute;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.95);"></div>
                <div style="position:relative;text-align:center;padding:2rem;max-width:32rem;margin:0 1rem;">
                    <h2 style="font-size:1.5rem;font-weight:700;color:#dc2626;margin-bottom:0.75rem;">Quiz Diakhiri!</h2>
                    <p style="color:#fff;margin-bottom:1.5rem;">Quiz Anda diakhiri otomatis karena <strong style="color:#f87171;">3 pelanggaran</strong>.</p>
                    <a href="{{ route('quiz.results', $userQuizId) }}" style="display:inline-block;padding:0.75rem 2rem;background:#374151;color:#fff;font-weight:700;border-radius:9999px;text-decoration:none;">
                        Lihat Hasil Quiz
                    </a>
                </div>
            </div>
        </div>

        {{-- Indikator pelanggaran --}}
        <div style="position:fixed;top:12px;right:130px;z-index:50;">
            <div style="display:flex;align-items:center;font-size:0.85rem;font-weight:600;" :style="violations > 0 ? 'color:#dc2626' : 'color:#16a34a'">
                <svg style="width:1rem;height:1rem;margin-right:4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span x-text="violations + '/3'"></span>
            </div>
        </div>
    </div>

    {{-- ==================== HEADER / NAVBAR ==================== --}}
    <div style="position:fixed;top:0;left:0;right:0;z-index:40;background:linear-gradient(135deg,#d4a853,#c4944a);border-bottom:1px solid rgba(255,255,255,0.2);backdrop-filter:blur(8px);box-shadow:0 2px 12px rgba(0,0,0,0.1);">
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.5rem 1rem;">
            <button wire:click="cancelQuiz" style="display:inline-flex;align-items:center;padding:0.4rem 1rem;font-size:0.85rem;font-weight:600;color:#92400e;background:rgba(255,255,255,0.85);border-radius:9999px;border:none;cursor:pointer;">
                ✕ Cancel
            </button>
            <div><img src="{{ asset('images/banner_logo.png') }}" alt="Logo" style="height:2.5rem;"></div>
            <div style="display:flex;align-items:center;padding:0.4rem 1rem;background:rgba(0,0,0,0.2);border-radius:9999px;color:#fff;font-weight:700;font-size:0.9rem;"
                 x-data="{ timeLeft: {{ $timeLeft }} }"
                 x-init="let t=setInterval(()=>{if(timeLeft>0){timeLeft--;if(timeLeft<=300)$el.style.background='rgba(220,38,38,0.7)';}else{clearInterval(t);window.location.href='{{ route('quiz.results', $userQuizId) }}';}},1000);"
                 x-text="'⏱ '+Math.floor(timeLeft/60)+':'+(timeLeft%60).toString().padStart(2,'0')">
            </div>
        </div>
    </div>

    {{-- ==================== MAIN CONTENT (SOAL + GRID) ==================== --}}
    <div style="padding-top:4rem;" x-data="{ selectedIdx: @js($selectedAnswerIndex), mobileNavOpen: false }"
         wire:key="quiz-alpine-{{ $questionIndex }}">
        <div style="display:flex;max-width:80rem;margin:0 auto;padding:1rem;gap:1rem;">

            {{-- ===== PANEL SOAL (KIRI) ===== --}}
            <div style="flex:1;min-width:0;">
                <div style="background:rgba(255,255,255,0.85);backdrop-filter:blur(12px);border-radius:1.5rem;box-shadow:0 8px 32px rgba(0,0,0,0.08);border:1px solid rgba(255,255,255,0.5);padding:2rem;margin-bottom:1rem;"
                     wire:key="question-card-{{ $questionIndex }}">

                    {{-- Badge soal --}}
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;">
                        <span style="display:inline-flex;align-items:center;padding:0.35rem 1rem;background:linear-gradient(135deg,#d4a853,#c4944a);color:#fff;font-weight:700;font-size:0.85rem;border-radius:9999px;box-shadow:0 2px 8px rgba(212,168,83,0.3);">
                            Soal {{ $questionIndex + 1 }} / {{ $totalQuestions }}
                        </span>
                        <span style="font-size:0.8rem;color:#9ca3af;">{{ $question->userQuiz->quiz->title }}</span>
                    </div>

                    {{-- Teks soal --}}
                    <div style="font-size:1.1rem;line-height:1.8;color:#1f2937;margin-bottom:1.5rem;" id="question-text">
                        {!! $question->question_text !!}
                    </div>

                    {{-- Gambar soal (jika ada) --}}
                    @if($question->image_path)
                        <div style="margin-bottom:1.5rem;text-align:center;">
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image"
                                 style="max-width:100%;border-radius:1rem;box-shadow:0 4px 16px rgba(0,0,0,0.1);">
                        </div>
                    @endif

                    {{-- Pilihan jawaban --}}
                    <div id="answer-options" style="display:flex;flex-direction:column;gap:0.75rem;">
                        @foreach($cleanAnswers as $ansIdx => $answerOption)
                            <label @click="selectedIdx = {{ $ansIdx }}"
                                   :style="selectedIdx === {{ $ansIdx }}
                                       ? 'background:linear-gradient(135deg,#fef3c7,#fde68a);border:2px solid #d97706;box-shadow:0 4px 12px rgba(217,119,6,0.15);'
                                       : 'background:#f9fafb;border:2px solid #e5e7eb;'"
                                   style="display:flex;align-items:center;padding:1rem 1.25rem;border-radius:1rem;cursor:pointer;transition:all 0.2s ease;">
                                {{-- Radio visual --}}
                                <span :style="selectedIdx === {{ $ansIdx }}
                                          ? 'background:#d97706;border-color:#d97706;box-shadow:inset 0 0 0 3px #fff;'
                                          : 'background:#fff;border-color:#d1d5db;'"
                                      style="flex-shrink:0;width:1.35rem;height:1.35rem;border-radius:50%;border:2px solid;margin-right:0.85rem;transition:all 0.2s;pointer-events:none;">
                                </span>
                                {{-- Teks jawaban (pointer-events:none agar klik tembus ke label meskipun MathJax replace DOM) --}}
                                <span style="color:#374151;font-size:1rem;line-height:1.6;pointer-events:none;" class="math-content">$${{ $answerOption['content'] }}$$</span>
                            </label>
                        @endforeach
                    </div>

                    {{-- Navigasi Prev / Next --}}
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:2rem;padding-top:1.5rem;border-top:1px solid #e5e7eb;">
                        @if($questionIndex > 0)
                            <button @click="@this.previousQuestion(selectedIdx)"
                                    style="display:inline-flex;align-items:center;padding:0.65rem 1.5rem;background:#f3f4f6;color:#374151;font-weight:600;border-radius:9999px;border:1px solid #d1d5db;cursor:pointer;font-size:0.9rem;">
                                ← Sebelumnya
                            </button>
                        @else
                            <div></div>
                        @endif

                        @if($questionIndex + 1 < $totalQuestions)
                            <button @click="@this.nextQuestion(selectedIdx)"
                                    style="display:inline-flex;align-items:center;padding:0.65rem 1.5rem;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-weight:600;border-radius:9999px;border:none;cursor:pointer;font-size:0.9rem;box-shadow:0 4px 12px rgba(37,99,235,0.25);">
                                Selanjutnya →
                            </button>
                        @else
                            <button @click="@this.finishQuiz(selectedIdx)"
                                    style="display:inline-flex;align-items:center;padding:0.65rem 1.5rem;background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;font-weight:700;border-radius:9999px;border:none;cursor:pointer;font-size:0.9rem;box-shadow:0 4px 12px rgba(22,163,74,0.25);">
                                ✓ Selesai Quiz
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ===== GRID NAVIGASI SOAL (KANAN - Desktop) ===== --}}
            <div style="width:220px;flex-shrink:0;" class="quiz-nav-desktop">
                <div style="position:sticky;top:5rem;background:rgba(255,255,255,0.85);backdrop-filter:blur(12px);border-radius:1.5rem;box-shadow:0 8px 32px rgba(0,0,0,0.08);border:1px solid rgba(255,255,255,0.5);padding:1.25rem;">
                    <h3 style="font-size:0.85rem;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.75rem;">Navigasi Soal</h3>
                    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:0.4rem;">
                        @for($i = 0; $i < $totalQuestions; $i++)
                            @php
                                $isActive = $i === $questionIndex;
                                $isAnswered = isset($answeredQuestions[$i]);
                            @endphp
                            <button @click="@this.goToQuestion({{ $i }}, selectedIdx)"
                                    style="width:100%;aspect-ratio:1;border-radius:0.5rem;font-size:0.8rem;font-weight:700;border:2px solid;cursor:pointer;transition:all 0.15s;
                                    {{ $isActive ? 'background:#3b82f6;color:#fff;border-color:#2563eb;box-shadow:0 2px 8px rgba(59,130,246,0.4);' : ($isAnswered ? 'background:#dcfce7;color:#16a34a;border-color:#86efac;' : 'background:#f9fafb;color:#9ca3af;border-color:#e5e7eb;') }}">
                                {{ $i + 1 }}
                            </button>
                        @endfor
                    </div>
                    {{-- Legend --}}
                    <div style="margin-top:1rem;display:flex;flex-direction:column;gap:0.35rem;font-size:0.7rem;color:#6b7280;">
                        <div style="display:flex;align-items:center;gap:0.4rem;">
                            <span style="width:12px;height:12px;border-radius:3px;background:#3b82f6;display:inline-block;"></span> Sedang dikerjakan
                        </div>
                        <div style="display:flex;align-items:center;gap:0.4rem;">
                            <span style="width:12px;height:12px;border-radius:3px;background:#dcfce7;border:1px solid #86efac;display:inline-block;"></span> Sudah dijawab
                        </div>
                        <div style="display:flex;align-items:center;gap:0.4rem;">
                            <span style="width:12px;height:12px;border-radius:3px;background:#f9fafb;border:1px solid #e5e7eb;display:inline-block;"></span> Belum dijawab
                        </div>
                    </div>
                    {{-- Progress --}}
                    <div style="margin-top:1rem;">
                        <div style="display:flex;justify-content:space-between;font-size:0.75rem;color:#6b7280;margin-bottom:0.3rem;">
                            <span>Progress</span>
                            <span>{{ count($answeredQuestions) }}/{{ $totalQuestions }}</span>
                        </div>
                        <div style="height:6px;background:#e5e7eb;border-radius:9999px;overflow:hidden;">
                            <div style="height:100%;background:linear-gradient(90deg,#16a34a,#4ade80);border-radius:9999px;transition:width 0.3s;width:{{ $totalQuestions > 0 ? round(count($answeredQuestions) / $totalQuestions * 100) : 0 }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== GRID NAVIGASI SOAL (MOBILE - Bottom) ===== --}}
        <div class="quiz-nav-mobile" style="display:none;">
            <button @click="mobileNavOpen = !mobileNavOpen"
                    style="position:fixed;bottom:1rem;right:1rem;z-index:45;width:3.5rem;height:3.5rem;border-radius:50%;background:linear-gradient(135deg,#d4a853,#c4944a);color:#fff;font-weight:700;font-size:0.85rem;border:none;cursor:pointer;box-shadow:0 4px 16px rgba(0,0,0,0.2);display:flex;align-items:center;justify-content:center;">
                <span>{{ count($answeredQuestions) }}/{{ $totalQuestions }}</span>
            </button>
            <div x-show="mobileNavOpen" x-cloak @click.self="mobileNavOpen = false"
                 style="position:fixed;bottom:0;left:0;right:0;z-index:44;background:rgba(0,0,0,0.5);top:0;">
                <div style="position:absolute;bottom:0;left:0;right:0;background:#fff;border-radius:1.5rem 1.5rem 0 0;padding:1.5rem;max-height:60vh;overflow-y:auto;">
                    <h3 style="font-size:0.9rem;font-weight:700;color:#374151;margin-bottom:1rem;">Navigasi Soal</h3>
                    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:0.5rem;">
                        @for($i = 0; $i < $totalQuestions; $i++)
                            @php $isActive = $i === $questionIndex; $isAnswered = isset($answeredQuestions[$i]); @endphp
                            <button @click="@this.goToQuestion({{ $i }}, selectedIdx); mobileNavOpen = false;"
                                    style="aspect-ratio:1;border-radius:0.5rem;font-weight:700;border:2px solid;cursor:pointer;font-size:0.85rem;
                                    {{ $isActive ? 'background:#3b82f6;color:#fff;border-color:#2563eb;' : ($isAnswered ? 'background:#dcfce7;color:#16a34a;border-color:#86efac;' : 'background:#f9fafb;color:#9ca3af;border-color:#e5e7eb;') }}">
                                {{ $i + 1 }}
                            </button>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigasi sekarang pakai Alpine @click + $wire, tidak butuh script terpisah --}}

    {{-- MathJax Config --}}
    <script>
        window.MathJax = {
            tex: { inlineMath: [['$$','$$'],['\\(','\\)']], displayMath: [['\\[','\\]']] },
            startup: { typeset: false }
        };
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    {{-- MathJax Render + Proctoring --}}
    <script>
        function typesetMathGlobal() {
            if (window.MathJax && MathJax.typesetPromise) {
                MathJax.startup.document.clear();
                MathJax.startup.document.updateDocument();
                MathJax.typesetPromise().catch(function(err) { console.log('MathJax error:', err); });
            }
        }

        if (window.MathJax && MathJax.startup) {
            MathJax.startup.promise.then(function() { typesetMathGlobal(); });
        }

        document.addEventListener('livewire:init', function() {
            Livewire.on('questionChanged', () => { setTimeout(typesetMathGlobal, 100); });
        });
        document.addEventListener('DOMContentLoaded', function() { setTimeout(typesetMathGlobal, 200); });

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
                isDismissing: false,
                init() {
                    if (isResuming) { this.startListeners(); this.typesetMath(); this.requestFullscreen(); }
                },
                typesetMath() { setTimeout(typesetMathGlobal, 150); },
                enterFullscreenAndStart() {
                    const el = document.documentElement; const self = this;
                    const activate = () => { sessionStorage.setItem(sessionKey,'true'); self.showStart=false; self.proctorActive=true; self.typesetMath(); self.startListeners(); };
                    if (el.requestFullscreen) el.requestFullscreen().then(activate).catch(activate);
                    else if (el.webkitRequestFullscreen) { el.webkitRequestFullscreen(); activate(); }
                    else activate();
                },
                startListeners() {
                    const self = this;
                    document.addEventListener('visibilitychange', function() {
                        if (document.hidden && !self.quizEnded && self.proctorActive) { if (self.showWarning||self.isDismissing) return; self.recordAndWarn('tab_switch'); }
                    });
                    document.addEventListener('fullscreenchange', function() {
                        if (!document.fullscreenElement && !self.quizEnded && self.proctorActive) { if (self.showWarning||self.isDismissing) return; self.recordAndWarn('fullscreen_exit'); }
                    });
                    window.addEventListener('blur', function() {
                        if (!self.quizEnded && self.proctorActive) { if (self.showWarning||self.isDismissing) return; self.recordAndWarn('window_blur'); }
                    });
                },
                recordAndWarn(type) {
                    this.violations++; this.showWarning = true;
                    @this.call('recordViolation', type);
                    if (this.violations >= 3) { this.showWarning=false; this.quizEnded=true; sessionStorage.removeItem(sessionKey);
                        if (document.fullscreenElement) document.exitFullscreen().catch(()=>{}); }
                },
                requestFullscreen() {
                    const el = document.documentElement;
                    if (el.requestFullscreen) el.requestFullscreen().catch(()=>{});
                    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
                },
                dismissWarning() {
                    this.showWarning=false; this.isDismissing=true; this.requestFullscreen(); this.typesetMath();
                    setTimeout(()=>{ this.isDismissing=false; }, 3000);
                }
            };
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        #mobile-menu a[href="/admin"] { display: none !important; }
        /* Desktop: show sidebar nav, hide mobile FAB */
        .quiz-nav-desktop { display: block; }
        .quiz-nav-mobile { display: none !important; }
        @media (max-width: 768px) {
            .quiz-nav-desktop { display: none !important; }
            .quiz-nav-mobile { display: block !important; }
        }
    </style>
</div>
