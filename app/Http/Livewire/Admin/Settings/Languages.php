<?php

namespace App\Http\Livewire\Admin\Settings;

use Livewire\Component;
use DateTime, App, File;
use Illuminate\Support\Facades\Storage;
use App\Models\Language;
use App\Models\Langugae;
use Artisan;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Languages extends Component
{
    use LivewireAlert;

    public $languages      = [];

    protected $listeners = ['sendUpdateLanguageStatus' => 'onUpdateLanguageStatus', 'sync'];

    public function mount()
    {
         $this->languages = Language::all()->toArray();
    }

    public function render()
    {
        return view('livewire.translations');
    }

    /**
     * -------------------------------------------------------------------------------
     *  Set Default Language
     * -------------------------------------------------------------------------------
    **/

    public function onSetDefault($id)
    {
        try {

            Language::where('is_default', '=', true)->update( ['is_default' => false] );
            $trans             = Language::findOrFail($id);
            $trans->is_default    = true;
            $trans->updated_at = new DateTime();
            $trans->save();

			$this->alert('success', __('Language updated successfully!') );
            $this->mount();

        } catch (\Exception $e) {
            $this->alert('error', __($e->getMessage()) );

        }
    }

    /**
     * -------------------------------------------------------------------------------
     *  Sync Translations
     * -------------------------------------------------------------------------------
    **/

    public function sync($id){
            
            $languages = Language::findOrFail($id);

            Artisan::call('translatable:export', ['lang' => $languages->code]);

            $this->alert('success', __('Translation updated successfully!') );
        
       
    }

    public function onUpdateLanguageStatus(){
        $this->mount();
    }


    /**
     * -------------------------------------------------------------------------------
     *  Delete Language
     * -------------------------------------------------------------------------------
    **/

    public function delete(Language $lang_id)
    {
        $lang_id->delete();

        $this->alert('warning', __('Language deleted successfully!') );

        $this->reRenderParent();
    }   
}
