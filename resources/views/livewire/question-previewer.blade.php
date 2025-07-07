<div>
    <div class="fixed top-0 left-0 right-0 z-50 border-b border-gray-200" style="background-color: #f2e2b1f0;">
        <div class="flex items-center justify-between px-4 py-2" style="background-color: #f2e2b1f0;">
            <button wire:click="backToCreate" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-lg hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <x-heroicon-o-arrow-left class="w-5 h-5 mr-1.5" />
                Kembali ke Buat Soal
            </button>
            <div class="flex items-center text-gray-600 font-medium">
                Preview Mode
            </div>
        </div>
    </div>

    <div class="pt-4">
        <div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
            <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
            <div class="p-6 shadow sm:rounded-lg" style="background-color: #f2e2b1f0;">
                <h1 class="mb-4 text-2xl font-semibold text-gray-900">Pratinjau Soal</h1>
                <p class="mb-6 text-gray-600">Berikut adalah pratinjau soal Anda:</p>
                <div>
                    <div class="mb-6">
                        <p class="mb-4 text-lg">
                            {!! preg_replace_callback('/\\d+\\.\\d+/', function($m) { return number_format($m[0], 2, '.', ''); }, $previewQuestionText) !!}
                        </p>
                        @if($previewImagePath)
                            <img src="{{ asset('storage/' . $previewImagePath) }}" alt="Question Image" class="mb-4 rounded-lg shadow-md">
                        @endif
                        @foreach($previewAnswers as $answerOption)
                            <div class="mb-2">
                                <label class="flex items-center">
                                    <input type="radio" 
                                           name="answer_preview" 
                                           value="{{ $answerOption['content'] }}" 
                                           class="text-blue-600 form-radio" disabled>
                                    <span class="ml-2 text-gray-700">
                                        {!! preg_replace_callback('/\\d+\\.\\d+/', function($m) { return number_format($m[0], 2, '.', ''); }, $answerOption['content']) !!}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-6">
                    <button wire:click="generatePreview" class="px-4 py-2 text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Refresh Pratinjau
                    </button>
                    <button wire:click="backToCreate" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 ml-2">
                        Kembali ke Buat Soal
                    </button>
                </div>
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
</div> 