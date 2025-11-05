<!-- resources/views/categories/index.blade.php -->
@extends('layouts.app')

@section('title', 'Categories - LaravelStore')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Product Categories</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('categories.show', $category) }}"
            class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition duration-300 border border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-folder text-blue-600 text-xl"></i>
                </div>
                <span class="bg-blue-500 text-white px-2 py-1 rounded-full text-sm">
                    {{ $category->products_count }} products
                </span>
            </div>
            <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $category->name }}</h3>
            <p class="text-gray-600 text-sm">{{ $category->description }}</p>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <span class="text-blue-600 text-sm font-semibold hover:text-blue-800">
                    Browse Category <i class="fas fa-arrow-right ml-1"></i>
                </span>
            </div>
        </a>
        @endforeach
    </div>

    @if($categories->isEmpty())
    <div class="text-center py-12">
        <i class="fas fa-folder-open text-gray-400 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Categories Found</h3>
        <p class="text-gray-500">There are no product categories available at the moment.</p>
    </div>
    @endif
</div>
@endsection