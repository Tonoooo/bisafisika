<div>
        <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="p-6 bg-white shadow sm:rounded-lg">
                <h1 class="mb-6 text-2xl font-semibold text-gray-900">Quiz History</h1>
                <ul class="divide-y divide-gray-200">
                    @foreach ($userQuizzes as $userQuiz)
                        <li class="py-4">
                            <a href="{{ route('quiz.results', $userQuiz->id) }}" class="text-lg font-medium text-blue-600 hover:underline">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $userQuiz->quiz->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $userQuiz->created_at->format('d M Y H:i') }} WIB</div>
                                </td>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
</div>
