<!-- resources/views/admin/categories/create.blade.php -->
@extends('admin.layout')

@section('title', 'Create Category')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">Create Category</h1>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-2xl">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-6">
            <!-- Name Field -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description Field -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" 
                          name="description" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Image Field -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Category Image</label>
                <div class="mt-1 flex items-center space-x-4">
                    <div id="image-preview" class="flex-shrink-0">
                        <span class="inline-block h-16 w-16 rounded-md overflow-hidden bg-gray-100">
                            <svg class="h-full w-full text-gray-300" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 12.004 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                    </div>
                    <div>
                        <label for="image-upload" class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 cursor-pointer">
                            Choose a file
                        </label>
                        <input id="image-upload" name="image" type="file" class="sr-only" accept="image/*">
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        <p id="file-name" class="text-sm text-gray-500 mt-1"></p>
                    </div>
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Active Status -->
            <div>
                <label for="is_active" class="flex items-center">
                    <input type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
                <p class="mt-1 text-sm text-gray-500">Check this box to make the category visible to users.</p>
                @error('is_active')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-3">
            <a href="{{ route('admin.categories.index') }}" 
               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Create Category
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('image-upload').addEventListener('change', function(e) {
    const [file] = e.target.files;
    if (file) {
        // Display file name
        document.getElementById('file-name').textContent = file.name;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = function(event) {
            const previewContainer = document.getElementById('image-preview');
            previewContainer.innerHTML = `
                <img src="${event.target.result}" class="h-16 w-16 rounded-md object-cover" alt="Preview">
            `;
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endsection