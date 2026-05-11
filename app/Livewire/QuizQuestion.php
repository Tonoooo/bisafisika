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
    public $questionIndex = 0;
    public $totalQuestions;
    public $selectedAnswerIndex = null; // Index jawaban (0,1,2,3) bukan teks
    public $timeLeft;

    // Simpan ID saja, bukan model Eloquent
    public $questionIds = [];

    // Tracking jawaban: array of questionIndex => answerIndex
    public $answeredQuestions = [];

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

        // Muat status jawaban untuk semua soal (untuk grid navigasi)
        $this->loadAllAnsweredStatus();

        // Muat jawaban soal yang sedang aktif
        $this->loadCurrentAnswer();
    }

    /**
     * Muat status jawaban semua soal (sudah dijawab atau belum).
     */
    private function loadAllAnsweredStatus()
    {
        $this->answeredQuestions = [];

        foreach ($this->questionIds as $index => $questionId) {
            $existingAnswer = UserAnswer::where('user_question_id', $questionId)->first();
            if ($existingAnswer && $existingAnswer->answer_content !== null) {
                // Cari index jawaban yang cocok
                $question = UserQuestion::with('question')->find($questionId);
                $answers = json_decode($question->answers, true);
                if (is_array($answers)) {
                    foreach ($answers as $ansIdx => $answer) {
                        if (trim($answer['content'] ?? '') === trim($existingAnswer->answer_content)) {
                            $this->answeredQuestions[$index] = $ansIdx;
                            break;
                        }
                    }
                }
            }
        }
    }

    /**
     * Muat jawaban yang sudah ada untuk soal saat ini.
     */
    private function loadCurrentAnswer()
    {
        if (isset($this->answeredQuestions[$this->questionIndex])) {
            $this->selectedAnswerIndex = $this->answeredQuestions[$this->questionIndex];
        } else {
            $this->selectedAnswerIndex = null;
        }
    }

    /**
     * Dapatkan model UserQuestion saat ini (fresh dari DB).
     */
    private function getCurrentQuestion()
    {
        return UserQuestion::with('question')->find($this->questionIds[$this->questionIndex]);
    }

    /**
     * Ambil daftar jawaban dari soal saat ini (tanpa modifikasi apapun).
     */
    private function getAnswersForQuestion($questionId)
    {
        $question = UserQuestion::find($questionId);
        $answers = json_decode($question->answers, true);
        if (!is_array($answers)) return [];

        // Filter jawaban kosong saja, TANPA modifikasi teks apapun
        return array_values(array_filter($answers, function ($answer) {
            return !empty(trim($answer['content'] ?? ''));
        }));
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

    /**
     * Simpan jawaban berdasarkan index.
     * Index digunakan untuk mengambil teks asli dari data soal.
     */
    public function saveAnswer($answerIndex)
    {
        if ($answerIndex === null || $answerIndex === '') return;

        $answerIndex = (int) $answerIndex;

        try {
            $questionId = $this->questionIds[$this->questionIndex];
            $answers = $this->getAnswersForQuestion($questionId);

            if (!isset($answers[$answerIndex])) return;

            $answerContent = trim($answers[$answerIndex]['content']);
            $isCorrect = (bool) ($answers[$answerIndex]['is_correct'] ?? false);

            UserAnswer::updateOrCreate(
                ['user_question_id' => $questionId],
                [
                    'answer_content' => $answerContent,
                    'is_correct' => $isCorrect
                ]
            );

            // Update tracking
            $this->answeredQuestions[$this->questionIndex] = $answerIndex;
            $this->selectedAnswerIndex = $answerIndex;

        } catch (\Exception $e) {
            Log::error('saveAnswer error: ' . $e->getMessage(), [
                'answerIndex' => $answerIndex,
                'questionIndex' => $this->questionIndex,
            ]);
        }
    }

    /**
     * Navigasi ke soal tertentu berdasarkan index (untuk grid navigasi).
     */
    public function goToQuestion($index, $currentAnswerIndex = null)
    {
        // Simpan jawaban soal saat ini terlebih dahulu
        if ($currentAnswerIndex !== null && $currentAnswerIndex !== '') {
            $this->saveAnswer($currentAnswerIndex);
        }

        $index = (int) $index;
        if ($index >= 0 && $index < $this->totalQuestions) {
            $this->questionIndex = $index;
            $this->loadCurrentAnswer();
            $this->dispatch('questionChanged');
        }
    }

    /**
     * Navigasi ke soal berikutnya.
     */
    public function nextQuestion($currentAnswerIndex = null)
    {
        if ($currentAnswerIndex !== null && $currentAnswerIndex !== '') {
            $this->saveAnswer($currentAnswerIndex);
        }

        if ($this->questionIndex + 1 < $this->totalQuestions) {
            $this->questionIndex++;
            $this->loadCurrentAnswer();
            $this->dispatch('questionChanged');
        }
    }

    /**
     * Navigasi ke soal sebelumnya.
     */
    public function previousQuestion($currentAnswerIndex = null)
    {
        if ($currentAnswerIndex !== null && $currentAnswerIndex !== '') {
            $this->saveAnswer($currentAnswerIndex);
        }

        if ($this->questionIndex > 0) {
            $this->questionIndex--;
            $this->loadCurrentAnswer();
            $this->dispatch('questionChanged');
        }
    }

    /**
     * Submit jawaban terakhir dan selesaikan quiz.
     */
    public function finishQuiz($currentAnswerIndex = null)
    {
        if ($currentAnswerIndex !== null && $currentAnswerIndex !== '') {
            $this->saveAnswer($currentAnswerIndex);
        }

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
        // Load question fresh dari DB setiap render
        $question = $this->getCurrentQuestion();

        // Ambil jawaban TANPA modifikasi apapun
        $questionId = $this->questionIds[$this->questionIndex];
        $cleanAnswers = $this->getAnswersForQuestion($questionId);

        $userQuiz = UserQuiz::findOrFail($this->userQuizId);

        return view('livewire.quiz-question', [
            'question' => $question,
            'cleanAnswers' => $cleanAnswers,
            'questionIndex' => $this->questionIndex,
            'totalQuestions' => $this->totalQuestions,
            'timeLeft' => $this->timeLeft,
            'violationCount' => $userQuiz->total_violations,
            'answeredQuestions' => $this->answeredQuestions,
        ]);
    }
}
