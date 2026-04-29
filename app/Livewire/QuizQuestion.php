<?php

namespace App\Livewire;

use App\Models\UserAnswer;
use App\Models\UserQuiz;
use App\Models\UserQuestion;
use App\Models\QuizViolation;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class QuizQuestion extends Component
{
    public $userQuizId;
    public $questionIndex;
    public $totalQuestions;
    public $answer;
    public $timeLeft;

    // Simpan ID saja, bukan model Eloquent
    public $questionIds = [];

    public function mount($userQuizId, $questionIndex = 0)
    {
        $this->userQuizId = $userQuizId;
        $this->questionIndex = $questionIndex;

        $userQuiz = UserQuiz::findOrFail($this->userQuizId);

        if ($userQuiz->is_completed) {
            return redirect()->route('quiz.results', $this->userQuizId);
        }
        if ($userQuiz->user_id !== auth()->id()) {
            abort(403);
        }

        // Simpan semua ID soal (bukan model)
        $this->questionIds = $userQuiz->userQuestions->pluck('id')->toArray();
        $this->totalQuestions = count($this->questionIds);

        // Hitung waktu tersisa
        $startTime = $userQuiz->created_at;
        $timeLimit = $userQuiz->quiz->time_limit * 60;
        $elapsedTime = now()->diffInSeconds($startTime);
        $this->timeLeft = max(0, $timeLimit - $elapsedTime);

        if ($this->timeLeft <= 0) {
            $this->handleTimeUp();
            return;
        }

        // Muat jawaban yang sudah ada untuk soal pertama
        $this->loadCurrentAnswer();
    }

    /**
     * Muat jawaban yang sudah ada untuk soal saat ini.
     */
    private function loadCurrentAnswer()
    {
        $questionId = $this->questionIds[$this->questionIndex];
        $existingAnswer = UserAnswer::where('user_question_id', $questionId)->first();
        $this->answer = $existingAnswer ? $existingAnswer->answer_content : null;
    }

    /**
     * Dapatkan model UserQuestion saat ini (fresh dari DB).
     */
    private function getCurrentQuestion()
    {
        return UserQuestion::with('question')->find($this->questionIds[$this->questionIndex]);
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
        
        foreach ($userQuiz->userQuestions as $userQuestion) {
            if (!$userQuestion->userAnswers()->exists()) {
                UserAnswer::create([
                    'user_question_id' => $userQuestion->id,
                    'answer_content' => null,
                    'is_correct' => false
                ]);
            }
        }

        $userQuiz->update(['is_completed' => true]);
        
        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function saveAnswer($selectedAnswer)
    {
        if (!empty($selectedAnswer)) {
            try {
                $question = $this->getCurrentQuestion();
                $answers = json_decode($question->answers, true);
                $trimmedAnswer = trim($selectedAnswer);
                $isCorrect = false;

                if (is_array($answers)) {
                    foreach ($answers as $answer) {
                        if (trim($answer['content'] ?? '') === $trimmedAnswer) {
                            $isCorrect = $answer['is_correct'] ?? false;
                            break;
                        }
                    }
                }

                UserAnswer::updateOrCreate(
                    ['user_question_id' => $question->id],
                    [
                        'answer_content' => $trimmedAnswer,
                        'is_correct' => $isCorrect
                    ]
                );
            } catch (\Exception $e) {
                Log::error('saveAnswer error: ' . $e->getMessage(), [
                    'answer' => $selectedAnswer,
                    'questionIndex' => $this->questionIndex,
                ]);
            }
        }
    }

    /**
     * Navigasi ke soal berikutnya (tanpa pindah URL).
     */
    public function nextQuestion($selectedAnswer = null)
    {
        $this->saveAnswer($selectedAnswer);

        if ($this->questionIndex + 1 < $this->totalQuestions) {
            $this->questionIndex++;
            $this->loadCurrentAnswer();
            $this->dispatch('questionChanged');
        }
    }

    /**
     * Navigasi ke soal sebelumnya (tanpa pindah URL).
     */
    public function previousQuestion($selectedAnswer = null)
    {
        $this->saveAnswer($selectedAnswer);

        if ($this->questionIndex > 0) {
            $this->questionIndex--;
            $this->loadCurrentAnswer();
            $this->dispatch('questionChanged');
        }
    }

    /**
     * Submit jawaban terakhir dan selesaikan quiz.
     */
    public function finishQuiz($selectedAnswer = null)
    {
        $this->saveAnswer($selectedAnswer);

        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $userQuiz->update(['is_completed' => true]);

        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function cancelQuiz()
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);
        $userQuiz->update(['is_completed' => true]);
        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function recordViolation($type)
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);

        if ($userQuiz->is_completed) {
            return;
        }

        QuizViolation::create([
            'user_quiz_id' => $this->userQuizId,
            'violation_type' => $type,
        ]);

        $totalViolations = $userQuiz->violations()->count();
        $userQuiz->update(['total_violations' => $totalViolations]);

        if ($totalViolations >= 3) {
            $this->forceEndQuiz();
        }
    }

    public function forceEndQuiz()
    {
        $userQuiz = UserQuiz::findOrFail($this->userQuizId);

        foreach ($userQuiz->userQuestions as $userQuestion) {
            if (!$userQuestion->userAnswers()->exists()) {
                UserAnswer::create([
                    'user_question_id' => $userQuestion->id,
                    'answer_content' => null,
                    'is_correct' => false,
                ]);
            }
        }

        $userQuiz->update(['is_completed' => true]);

        return redirect()->route('quiz.results', $this->userQuizId);
    }

    public function render()
    {
        // Load question fresh dari DB setiap render (tidak disimpan sebagai property)
        $question = $this->getCurrentQuestion();

        $answers = json_decode($question->answers, true);
        if (is_array($answers)) {
            $validAnswers = array_filter($answers, function($answer) {
                return !empty($answer['content']) && strpos($answer['content'], '$$') === false;
            });
            $question->question->shuffled_answers = collect($validAnswers);
        }

        $userQuiz = UserQuiz::findOrFail($this->userQuizId);

        return view('livewire.quiz-question', [
            'question' => $question,
            'questionIndex' => $this->questionIndex,
            'totalQuestions' => $this->totalQuestions,
            'timeLeft' => $this->timeLeft,
            'violationCount' => $userQuiz->total_violations,
        ]);
    }
}
