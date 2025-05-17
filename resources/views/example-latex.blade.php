@extends('components.layout')

@section('content')
<div class="max-w-4xl py-12 mx-auto sm:px-6 lg:px-8">
    <div class="p-6 bg-white shadow sm:rounded-lg">
        <h1 class="mb-4 text-2xl font-semibold text-gray-900">Example LaTeX Rendering</h1>
        <p class="mb-6 text-gray-600">Below is an example of a LaTeX math expression rendered using MathJax:</p>

        <div class="mb-6">
            <p class="mb-4 text-lg">
                $$\vec{A} = \left(a\right)\hat{i} + \left(a\right)\hat{j}$$
            </p>
        </div>

        <div class="mb-6">
            <p class="mb-4 text-lg">
                $$\vec{A} = \left(a\right)\hat{j}$$
            </p>
        </div>

        <div class="mb-6">
            <p class="mb-4 text-lg">
                $$\vec{A} = \left(a\right)\hat{i}$$
            </p>
        </div>

        <div class="mb-6">
            <p class="mb-4 text-lg">
                $$\vec{A} = \left(-a\right)\hat{i}$$
            </p>
        </div>
    </div>
</div>
@endsection
