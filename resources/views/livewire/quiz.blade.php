<!-- resources/views/livewire/quiz.blade.php -->
<div>
    @if ($questions->isNotEmpty())
        <div>
            <p>{!! $questions[$currentQuestionIndex]->question_text !!}</p>
            @if ($questions[$currentQuestionIndex]->question->image_path)
                <img src="{{ asset('storage/' . $questions[$currentQuestionIndex]->question->image_path) }}" alt="Question Image">
            @endif
            @foreach ($questions[$currentQuestionIndex]->question->answers as $answer)
                <label>
                    <input type="radio" wire:model="answers.{{ $questions[$currentQuestionIndex]->id }}" value="{{ $answer['content'] }}">
                    {{ $answer['content'] }}
                </label>
            @endforeach
        </div>
        <div>
            @if ($currentQuestionIndex > 0)
                <button wire:click="previousQuestion">Previous</button>
            @endif
            @if ($currentQuestionIndex < count($questions) - 1)
                <button wire:click="nextQuestion">Next</button>
            @else
                <button wire:click="submitQuiz">Submit Quiz</button>
            @endif
        </div>
    @else
        <p>No questions available for this quiz.</p>
    @endif
</div>
