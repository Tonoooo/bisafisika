<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Traits\ProcessesQuestionData;

class QuestionPreviewer extends Component
{
    use ProcessesQuestionData;

    public $questionContent;
    public $randomVariables;
    public $rumus;
    public $answers;
    public $imagePath;

    public $previewQuestionText;
    public $previewAnswers;
    public $previewImagePath;

    public function mount()
    {
        $questionData = Session::get('question_preview_data');
        
        if ($questionData) {
            $this->questionContent = $questionData['content'] ?? '';
            $this->randomVariables = $questionData['random_variables'] ?? [];
            $this->rumus = $questionData['rumus'] ?? [];
            $this->answers = $questionData['answers'] ?? [];
            $this->imagePath = $questionData['image_path'] ?? null;
            
            $this->generatePreview();
        } else {
            return redirect()->route('questions.create');
        }
    }

    public function generatePreview()
    {
        $randomValues = $this->generateRandomValues($this->questionContent, $this->rumus, (object)[
            'random_ranges' => $this->randomVariables
        ]);

        $rumusValues = $this->processRumus($this->rumus, $randomValues, (object)[
            'precision' => 3
        ]);

        $this->previewQuestionText = $this->replacePlaceholders($this->questionContent, array_merge($randomValues, $rumusValues));

        $this->previewAnswers = $this->replacePlaceholdersInAnswers($this->answers, $randomValues, $rumusValues);
        shuffle($this->previewAnswers);

        $this->previewImagePath = $this->imagePath;
    }

    public function backToCreate()
    {
        return redirect()->route('filament.admin.resources.questions.create');
    }

    public function render()
    {
        return view('livewire.question-previewer');
    }
} 