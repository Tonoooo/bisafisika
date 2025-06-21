<!-- resources/views/livewire/quiz-result.blade.php -->

<div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <div class="p-6 bg-white rounded-lg shadow-lg">
        <div class="flex items-center justify-center mb-6">
            <img src="{{ asset('images/einstein.png') }}" alt="Logo" class="w-12 h-12">
            <h1 class="text-2xl font-bold text-gray-900">Quiz Result</h1>
        </div>
        
        <p class="mb-2 text-lg text-gray-700"><strong>Quiz:</strong> {{ $userQuiz->quiz->title }}</p>
        <p class="mb-2 text-lg text-gray-700"><strong>Tanggal:</strong> {{ $userQuiz->created_at->format('d M Y H:i') }} WIB</p>
        <p class="mb-4 text-lg text-gray-700"><strong>Score:</strong> {{ $score }}</p>

        <h2 class="mb-6 text-xl font-bold text-gray-900">Questions and Answers</h2>
        @foreach ($userQuiz->userQuestions as $userQuestion)
            <div class="p-4 mb-6 bg-gray-100 rounded-lg">
                <p class="mb-2 text-gray-800"><strong>Question:</strong> {!! $userQuestion->question_text !!}</p>
                @if ($userQuestion->question->image_path)
                    <img src="{{ asset('storage/' . $userQuestion->question->image_path) }}" alt="Question Image" class="mb-4 rounded-lg shadow-md">
                @endif
                {{-- <p class="mb-2 text-gray-700"><strong>Answer:</strong> {{  '$$'. $userQuestion->userAnswers->first()->answer_content .'$$' ?? 'No Answer' }}</p> --}}
                @php
                $firstUserAnswer = $userQuestion->userAnswers->first();
                $answerContent = $firstUserAnswer ? $firstUserAnswer->answer_content : 'No Answer';
            @endphp
            <p class="mb-2 text-gray-700"><strong>Answer:</strong> {!! '$$' . $answerContent . '$$' !!}</p>


            </div>
        @endforeach
    </div>
    <script>
        document.addEventListener('livewire:load', function () {
            window.Livewire.hook('element.updated', (el, component) => {
                MathJax.typesetPromise();
            });
        });
    </script>
</div>
