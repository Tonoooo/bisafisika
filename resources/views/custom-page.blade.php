@extends('layouts.app')

@section('content')
<div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
    <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200 sm:px-20">
            <div>
                <img src="{{ asset('logo.png') }}" class="block w-auto h-12" alt="Logo" />
            </div>

            <div class="mt-8 text-2xl">
                Welcome to your custom page!
            </div>

            <div class="mt-6 text-gray-500">
                This is a custom page styled with Tailwind CSS. Use this area to add any content you like.
            </div>
        </div>

        <div class="grid grid-cols-1 bg-gray-200 bg-opacity-25 md:grid-cols-2">
            <div class="p-6">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M9 12h6m2 4H7m-2 8h14a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v15a2 2 0 002 2z" />
                    </svg>
                    <div class="ml-4 text-lg font-semibold leading-7 text-gray-600">Feature One</div>
                </div>

                <div class="ml-12">
                    <div class="mt-2 text-sm text-gray-500">
                        Description for feature one.
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M7 16V4m0 0l8 8m-8-8h6a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                    </svg>
                    <div class="ml-4 text-lg font-semibold leading-7 text-gray-600">Feature Two</div>
                </div>

                <div class="ml-12">
                    <div class="mt-2 text-sm text-gray-500">
                        Description for feature two.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
