<?php

namespace App\Livewire;

// app/Http/Livewire/QuizQuestion.php

use App\Models\UserAnswer;
use App\Models\UserQuiz;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class QuizQuestion extends Component
{
    public $userQuizId;

    public $questionIndex;

    public $question;

    public $totalQuestions;

    public $answer;

    public $timeLeft;

    public function mount($userQuizId, $questionIndex)
    {
        $this->userQuizId = $userQuizId;
        $this->questionIndex = $questionIndex;
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $this->question = $userQuiz->userQuestions[$this->questionIndex];
        $this->totalQuestions = $userQuiz->userQuestions->count();
        
        // Calculate remaining time
        $startTime = $userQuiz->created_at;
        $timeLimit = $userQuiz->quiz->time_limit * 60; // Convert minutes to seconds
        $elapsedTime = now()->diffInSeconds($startTime);
        $this->timeLeft = max(0, $timeLimit - $elapsedTime);

        // Jika waktu habis, selesaikan kuis
        if ($this->timeLeft <= 0) {
            $this->handleTimeUp();
            return;
        }

        // Shuffle the answers only if they haven't been shuffled and stored before
        if (!isset($this->question->question->shuffled_answers)) {
            $answers = json_decode($this->question->answers, true);
            shuffle($answers);
            $this->question->question->shuffled_answers = collect($answers);
        }
        
        // Load existing answer if available
        $existingAnswer = UserAnswer::where('user_question_id', $this->question->id)->first();
        if ($existingAnswer) {
            $this->answer = $existingAnswer->answer_content;
        }

        if ($userQuiz->is_completed) {
            return redirect()->route('quiz.results', $this->userQuizId);
        }
        if ($userQuiz->user_id !== auth()->id()) {
            abort(403);
        }
    }

    public function getTimeLeft()
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $startTime = $userQuiz->created_at;
        $timeLimit = $userQuiz->quiz->time_limit * 60;
        $elapsedTime = now()->diffInSeconds($startTime);
        $this->timeLeft = max(0, $timeLimit - $elapsedTime);

        if ($this->timeLeft <= 0) {
            $this->handleTimeUp();
        }

        return $this->timeLeft;
    }

    public function handleTimeUp()
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        
        // Simpan jawaban kosong untuk soal yang belum dijawab
        foreach ($userQuiz->userQuestions as $userQuestion) {
            if (!$userQuestion->userAnswers()->exists()) {
                UserAnswer::create([
                    'user_question_id' => $userQuestion->id,
                    'answer_content' => null,
                    'is_correct' => false
                ]);
            }
        }

        // Tandai kuis sebagai selesai
        $userQuiz->update(['is_completed' => true]);
        
        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function saveCurrentAnswer()
    {
        if (!empty($this->answer)) {
            $answers = json_decode($this->question->answers, true);
            $selectedAnswer = trim($this->answer);
            $isValidAnswer = false;
            $isCorrect = false;

            foreach ($answers as $answer) {
                if (trim($answer['content']) === $selectedAnswer) {
                    $isValidAnswer = true;
                    $isCorrect = $answer['is_correct'];
                    break;
                }
            }

            UserAnswer::updateOrCreate(
                ['user_question_id' => $this->question->id],
                [
                    'answer_content' => $isValidAnswer ? $selectedAnswer : null,
                    'is_correct' => $isCorrect
                ]
            );
        }
    }

    public function submitAnswer()
    {
        // Simpan jawaban saat ini
        $this->saveCurrentAnswer();

        // Lanjut ke soal berikutnya atau selesai
        if ($this->questionIndex + 1 < $this->totalQuestions) {
            return redirect()->route('quiz.question', [
                'userQuizId' => $this->userQuizId, 
                'questionIndex' => $this->questionIndex + 1
            ]);
        } else {
            $this->question->userQuiz->update(['is_completed' => true]);
            return redirect()->route('quiz.results', $this->userQuizId);
        }
    }

    public function cancelQuiz()
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $userQuiz->update(['is_completed' => true]);
        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function render()
    {
        // Pastikan jawaban yang ditampilkan valid
        $answers = json_decode($this->question->answers, true);
        if (is_array($answers)) {
            $validAnswers = array_filter($answers, function($answer) {
                return !empty($answer['content']) && strpos($answer['content'], '$$') === false;
            });
            $this->question->question->shuffled_answers = collect($validAnswers);
        }

        return view('livewire.quiz-question', [
            'question' => $this->question,
            'questionIndex' => $this->questionIndex,
            'totalQuestions' => $this->totalQuestions,
            'timeLeft' => $this->timeLeft
        ]);
    }
}
