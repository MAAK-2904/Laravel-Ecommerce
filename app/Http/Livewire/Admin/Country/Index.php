<?php

namespace App\Http\Livewire\Admin\Country;

use Livewire\Component;
use App\{
    Models\Country,
    Models\User
};
use Illuminate\Http\Response;
use Livewire\WithPagination;
use App\Http\Livewire\WithSorting;
use Str;

class Index extends Component
{
    use WithPagination;
    use WithSorting;

    public int $perPage;

    public array $orderable;

    public string $search = '';

    public array $selected = [];

    public array $paginationOptions;

    public array $listsForFields = [];

    protected $queryString = [
        'search' => [
            'except' => '',
        ],
        'sortBy' => [
            'except' => 'id',
        ],
        'sortDirection' => [
            'except' => 'desc',
        ],
    ];

    public function getSelectedCountProperty()
    {
        return count($this->selected);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetSelected()
    {
        $this->selected = [];
    }

    public function mount()
    {
        $this->sortBy            = 'id';
        $this->sortDirection     = 'desc';
        $this->perPage           = 25;
        $this->paginationOptions = config('settings.pagination.options');
        $this->orderable         = (new Country())->orderable;
    }

    public function render()
    {
        $query = Country::advancedFilter([
            's'               => $this->search ?: null,
            'order_column'    => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $countries = $query->paginate($this->perPage);

        return view('livewire.admin.country.index', compact('countries'));
    }

}