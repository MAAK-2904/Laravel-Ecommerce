<?php

namespace App\Http\Livewire\Admin\Slider;

use App\Http\Livewire\Trix;
use App\Http\Livewire\WithSorting;
use App\Models\Language;
use App\Models\Slider;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Str;

class Index extends Component
{
    use WithPagination, WithSorting,
        LivewireAlert, WithFileUploads;

    public $slider;

    public $photo;

    public $listeners = [
        Trix::EVENT_VALUE_UPDATED,
        'refreshIndex' => '$refresh',
        'showModal', 'editModal', 'delete'
    ];

    public $showModal = false;

    public $refreshIndex;

    public $editModal = false;

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

    protected $rules = [
        'slider.title' => ['required', 'string', 'max:255'],
        'slider.subtitle' => ['nullable', 'string'],
        'slider.details' => ['nullable'],
        'slider.link' => ['nullable', 'string'],
        'slider.language_id' => ['nullable', 'integer'],
        'slider.bg_color' => ['nullable', 'string'],
        'slider.embeded_video' => ['nullable'],
    ];

    public function mount()
    {
        $this->sortBy = 'id';
        $this->sortDirection = 'desc';
        $this->perPage = 25;
        $this->paginationOptions = [25, 50, 100];
        $this->orderable = (new Slider())->orderable;
        $this->initListsForFields();
    }

    public function render()
    {
        $query = Slider::advancedFilter([
            's' => $this->search ?: null,
            'order_column' => $this->sortBy,
            'order_direction' => $this->sortDirection,
        ]);

        $sliders = $query->paginate($this->perPage);

        return view('livewire.admin.slider.index', compact('sliders'));
    }

    // public function getPhotoPreviewProperty()
    // {
    //     return $this->slider->photo;
    // }

    public function setFeatured($id)
    {
        Slider::where('featured', '=', true)->update(['featured' => false]);
        $slider = Slider::findOrFail($id);
        $slider->featured = true;
        $slider->save();

        $this->alert('success', __('Slider featured successfully!'));
    }

    public function editModal(Slider $slider)
    {
        $this->resetErrorBag();

        $this->resetValidation();

        $this->slider = $slider;

        $this->editModal = true;
    }

    public function update()
    {
        $this->validate();

        if ($this->photo) {
            $imageName = Str::slug($this->slider->title).'.'.$this->photo->extension();
            $this->photo->storeAs('sliders', $imageName);
            $this->slider->photo = $imageName;
        }

        $this->slider->save();

        $this->alert('success', __('Slider updated successfully.'));

        $this->editModal = false;
    }

    public function showModal(Slider $slider)
    {
        $this->resetErrorBag();

        $this->resetValidation();

        $this->slider = $slider;

        $this->showModal = true;
    }

    public function delete(Slider $slider)
    {
        $slider->delete();

        $this->alert('success', __('Slider deleted successfully.'));
    }

    protected function initListsForFields(): void
    {
        $this->listsForFields['languages'] = Language::pluck('name', 'id')->toArray();
    }
}
