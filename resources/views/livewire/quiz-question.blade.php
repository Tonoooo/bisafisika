<!-- resources/views/livewire/quiz-question.blade.php -->
<div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <div class="p-6 bg-white shadow sm:rounded-lg">
        <h1 class="mb-4 text-2xl font-semibold text-gray-900">{{ $question->userQuiz->quiz->title }}</h1>
        <p class="mb-6 text-gray-600">Question {{ $questionIndex + 1 }} of {{ $totalQuestions }}</p>
        <form wire:submit.prevent="submitAnswer">
            <div class="mb-6">
                <p class="mb-4 text-lg">{!! $question->question_text !!}</p>
                @if($question->image_path)
                    <img src="{{ asset('storage/' . $question->image_path) }}" alt="Question Image" class="mb-4 rounded-lg shadow-md">
                @endif
                @foreach(json_decode($question->answers, true) as $answerOption)
                    <div class="mb-2">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="answer_{{ $question->id }}" 
                                   wire:model="answer" 
                                   value="{{ $answerOption['content'] }}" 
                                   class="text-blue-600 form-radio">
                            <span class="ml-2 text-gray-700">
                                {!! '$$' . $answerOption['content'] . '$$' !!}
                            </span>
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between">
                @if ($questionIndex > 0)
                    <a href="{{ route('quiz.question', ['userQuizId' => $userQuizId, 'questionIndex' => $questionIndex - 1]) }}" class="px-4 py-2 text-gray-800 bg-gray-200 rounded-lg shadow hover:bg-gray-300">
                        Previous
                    </a>
                @endif
                @if ($questionIndex < $totalQuestions - 1)
                    <button type="submit" class="px-4 py-2 text-black bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                        Next
                    </button>
                @else
                    <button type="submit" class="px-4 py-2 text-black bg-blue-600 rounded-lg shadow hover:bg-blue-700">
                        Submit
                    </button>
                @endif
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
