<!-- resources/views/livewire/quiz-list.blade.php -->
<div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="p-6 bg-white shadow sm:rounded-lg">
        <h1 class="mb-6 text-2xl font-semibold text-gray-900">Available Quizzes</h1>

        @if (session('error'))
            <div class="relative px-4 py-3 mb-4 text-red-700 bg-red-100 border border-red-400 rounded" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <ul class="divide-y divide-gray-200">
            @foreach ($quizzes as $quiz)
                <li class="py-4">
                    <a href="{{ route('quiz.start', $quiz->id) }}" class="text-lg font-medium text-blue-600 hover:underline">
                        {{ $quiz->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
