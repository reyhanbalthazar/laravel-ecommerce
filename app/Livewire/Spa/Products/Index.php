<?php

namespace App\Livewire\Spa\Products;

use App\Models\Category;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $minPrice = '';
    public $maxPrice = '';
    public $sort = 'newest';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
        'sort' => ['except' => 'newest']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function addToCart($productId)
    {
        $this->dispatch('addToCart', productId: $productId, quantity: 1);
    }

    public function render()
    {
        $query = Product::query();

        // Search filter
        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if ($this->category) {
            $query->whereHas('category', function($q) {
                $q->where('slug', $this->category);
            });
        }

        // Price range filter
        if ($this->minPrice !== '') {
            $query->where('price', '>=', $this->minPrice);
        }
        if ($this->maxPrice !== '') {
            $query->where('price', '<=', $this->maxPrice);
        }

        // Sorting
        switch ($this->sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->with(['images', 'category'])->paginate(12);
        $categories = Category::withCount('products')->get();

        return view('livewire.spa.products.index', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category', 'minPrice', 'maxPrice', 'sort']);
    }
}
