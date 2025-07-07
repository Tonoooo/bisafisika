<!-- resources/views/livewire/quiz-question.blade.php -->
<div>
    <div class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200" style="background-color: #f2e2b1f0;">
        <div class="flex items-center justify-between px-4 py-2" style="background-color: #f2e2b1f0;">
            <button wire:click="cancelQuiz" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <x-heroicon-o-x-circle class="w-5 h-5 mr-1.5" />
                Cancel
            </button>
            <div class="flex items-center">
                <img src="{{ asset('images/banner_logo.png') }}" alt="Logo" class="h-12">
                {{-- <h1 class=" font-bold sm:text-xl md:text-2xl">BelajarFisika</h1> --}}
            </div>
            <div class="flex items-center text-red-600 font-medium">
                <x-heroicon-o-clock class="w-5 h-5 mr-1.5" />
                <span x-data="{ timeLeft: {{ $timeLeft }} }" 
                      x-init="
                        let timer = setInterval(() => { 
                            if(timeLeft > 0) {
                                timeLeft--;
                                // Ubah warna menjadi merah jika waktu kurang dari 5 menit
                                if(timeLeft <= 300) {
                                    $el.classList.add('text-red-600', 'font-bold');
                                }
                            } else {
                                clearInterval(timer);
                                window.location.href = '{{ route('quiz.results', $userQuizId) }}';
                            }
                        }, 1000);

                        // Polling untuk mengecek waktu di server setiap 30 detik
                        let serverTimer = setInterval(() => {
                            @this.getTimeLeft().then(time => {
                                if(time <= 0) {
                                    clearInterval(serverTimer);
                                    window.location.href = '{{ route('quiz.results', $userQuizId) }}';
                                }
                            });
                        }, 30000);
                      "
                      x-text="Math.floor(timeLeft / 60) + ':' + (timeLeft % 60).toString().padStart(2, '0')"
                      class="transition-colors duration-300">
                </span>
            </div>
        </div>
    </div>

    <div class="pt-4">
        <div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
            <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
            <div class="p-6 shadow sm:rounded-lg" style="background-color: #f2e2b1f0;">
                <h1 class="mb-4 text-2xl font-semibold text-gray-900">{{ $question->userQuiz->quiz->title }}</h1>
                <p class="mb-6 text-gray-600">Question {{ $questionIndex + 1 }} of {{ $totalQuestions }}</p>
                <form wire:submit.prevent="submitAnswer">
                    <div class="mb-6">
                        <p class="mb-4 text-lg">
                            {!! preg_replace_callback('/\\d+\\.\\d+/', function($m) { return number_format($m[0], 2, '.', ''); }, $question->question_text)  !!}
                        </p>
                        @if($question->image_path)
                            <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image" class="mb-4 rounded-lg shadow-md">
                        @endif
                        @foreach($question->question->shuffled_answers as $answerOption)
                            <div class="mb-2">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="answer_{{ $question->id }}" 
                                           wire:model="answer" 
                                           value="{{ $answerOption['content'] }}" 
                                           class="text-blue-600 form-radio">
                                    <span class="ml-2 text-gray-700">
                                        {!! preg_replace_callback('/\\d+\\.\\d+/', function($m) { return number_format($m[0], 2, '.', ''); }, $answerOption['content']) !!}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-between">
                        @if($questionIndex > 0)
                            <a href="{{ route('quiz.question', ['userQuizId' => $userQuizId, 'questionIndex' => $questionIndex - 1]) }}" 
                               class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                Previous Question
                            </a>
                        @else
                            <div></div>
                        @endif
                        <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ $questionIndex + 1 < $totalQuestions ? 'Next Question' : 'Finish Quiz' }}
                        </button>
                    </div>
                </form>
            </div>
            <script>
                document.addEventListener('livewire:load', function () {
                    window.Livewire.hook('element.updated', (el, component) => {
                        MathJax.typesetPromise();
                    });
                });
            </script>
        </div>
    </div>
    <style>
        #mobile-menu a[href="/admin"] {
            display: none !important;
        }
        
        /*
        .filament-sidebar-item[href="/"] {
            display: none !important;
        }
        .fi-sidebar-item[href="/"] {
            display: none !important;
        }
        */
    </style>
</div>
