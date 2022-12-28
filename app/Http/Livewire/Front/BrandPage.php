<?php

namespace App\Http\Livewire\Front;

use App\Models\Product;
use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class BrandPage extends Component
{
    use WithPagination;

    public int $perPage;

    public array $paginationOptions;

    public $category_id;

    public $subcategory_id;
    public $filterProductCategories;
    public $filterProductSubcategories;

    public function filterProductCategories($category_id)
    {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function filterProductSubcategories($category_id)
    {
        $this->subcategory_id = $subcategory_id;
        $this->resetPage();
    }

    public function mount($brand)
    {
        $this->brand = $brand;
        $this->perPage = 15;
        $this->paginationOptions = [25, 50, 100];
    }

    public function render()
    {
        // where status is true
        $brand_products = Product::where('status', 1)
            ->where('brand_id', $this->brand->id)
            ->when($this->category_id, function ($query) {
                return $query->where('category_id', $this->category_id);
            })
            ->when($this->subcategory_id, function ($query) {
                return $query->where('subcategory_id', $this->subcategory_id);
            })
            ->paginate($this->perPage);

        return view('livewire.front.brand-page', compact('brand_products'));
    }


    public function getCategoriesProperty()
    {
        return Category::where('status', 1)->with('subcategories')->get();
    }
}
