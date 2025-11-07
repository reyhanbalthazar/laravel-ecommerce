{{-- resources/views/admin/products/create.blade.php --}}
@extends('admin.layout')

@section('title', 'Create Product')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Product</h1>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Basic Product Information --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select id="category_id" name="category_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="number" step="0.01" id="price" name="price" value="{{ old('price') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                        required>
                    @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-1">Sale Price</label>
                    <input type="number" step="0.01" id="sale_price" name="sale_price" value="{{ old('sale_price') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('sale_price') border-red-500 @enderror"
                        placeholder="Leave blank if no sale price">
                    @error('sale_price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock *</label>
                    <input type="number" id="stock" name="stock" value="{{ old('stock') ?? 0 }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('stock') border-red-500 @enderror"
                        required>
                    @error('stock')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-end space-x-6">
                    <div class="flex items-center">
                        <input type="hidden" name="is_featured" value="0">
                        <input type="checkbox" id="is_featured" name="is_featured" value="1"
                            {{ old('is_featured') ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_featured" class="ml-2 block text-sm text-gray-700 font-medium">Featured Product</label>
                    </div>

                    <div class="flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                            {{ old('is_active', 1) ? 'checked' : '' }}
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-700 font-medium">Active</label>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                <textarea id="description" name="description" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                    required>{{ old('description') }}</textarea>
                @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Image Upload Section --}}
            <div class="mb-6">
                <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Product Images</label>

                {{-- File Input --}}
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors duration-200">
                    <input type="file" name="images[]" id="images" multiple
                        accept="image/*"
                        class="hidden"
                        onchange="previewImages(this.files)">
                    <label for="images" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="mt-2">
                            <span class="text-blue-600 font-medium">Click to upload</span>
                            <span class="text-gray-500"> or drag and drop</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF, WEBP up to 2MB each</p>
                        <p class="text-xs text-gray-500">First image will be set as primary</p>
                    </label>
                </div>

                {{-- Error Messages --}}
                @error('images.*')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Image Preview Area --}}
                <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 hidden">
                    <!-- Preview images will appear here -->
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function previewImages(files) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        preview.classList.remove('hidden');

        if (files.length === 0) {
            preview.classList.add('hidden');
            return;
        }

        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();

            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative group bg-white rounded-lg border border-gray-200 p-2 shadow-sm';
                div.innerHTML = `
                <div class="aspect-w-1 aspect-h-1 bg-gray-100 rounded-md overflow-hidden">
                    <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded">
                </div>
                <div class="absolute top-1 right-1">
                    <span class="bg-blue-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                        ${index + 1}
                    </span>
                </div>
                <div class="mt-2 text-xs text-gray-600 truncate">${file.name}</div>
                <div class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</div>
            `;
                preview.appendChild(div);
            }

            reader.readAsDataURL(file);
        });
    }

    // Drag and drop functionality
    const dropArea = document.querySelector('label[for="images"]').parentElement;
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        dropArea.classList.add('border-blue-400', 'bg-blue-50');
    }

    function unhighlight() {
        dropArea.classList.remove('border-blue-400', 'bg-blue-50');
    }

    dropArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        document.getElementById('images').files = files;
        previewImages(files);
    }
</script>
@endpush
@endsection