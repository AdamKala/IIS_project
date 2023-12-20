<?php

namespace App\Http\Livewire\Public;

use App\Models\Group;
use App\Models\Tags;
use App\Traits\AlertHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateThread extends Component
{
    use AlertHelper, WithFileUploads;

    protected $listeners = [
        'update-create-thread' => '$refresh',
        'destroyConfirmed' => 'removeGroup'
    ];

    public Collection $groups;

    public $slug;

    public $search;

    public $title;
    public $groupSelect;
    public $editingTitle;

    public $editingText;
    public $editingId;
    public $editingSlug;
    public $editingImage;
    public $editingDescription;
    public $editingTag;

    public $userId;
    public $inputField;

    public $image;
    public $fileUploading;

    public function mount()
    {
        $this->inputField = '';
        $this->groups = Group::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->editingId = -1;
        $this->editingTitle = '';
        $this->editingSlug = '';
        $this->editingImage = '';
        $this->search = '';
        $this->image = '';
        $this->groupSelect = 0;
        $this->editingDescription = '';
        $this->editingText = '';
        $this->fileUploading = false;
        $this->emit('update-create-thread');
    }

    public function save()
    {
        $this->validate([
            'image' => 'image'
        ]);
        $this->fileUploading = true;
        $this->emit('$refresh');
    }

    public function search()
    {
        if (empty($this->search)) {
            $this->groups = Group::all();
            return;
        }
        $this->groups = Group::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->emit('update-create-thread');
    }

    public function createThread($groupId)
    {
        if (mb_strlen($this->inputField) > 0) {
            $name = $this->inputField;
            if ($name == null) {
                $this->showAlert('error', 'Vyber skupinu pro vlákno!');
                return;
            }

            $text = $this->editingText;
            $group = Group::query()->where('id', '=', $groupId)->first();
            $groupSlug = $group->slug;

            $slug = Str::slug($name);
            $i = 0;
            while (Group::query()->where('slug', '=', $slug)->first()) {
                $slug = Str::slug($name . ' ' . $i);
                $i++;
            }

            $Thread = new \App\Models\Thread;
            $Thread['name'] = $name;
            $Thread['slug'] = $slug;
            if ($text != null) {
                $Thread['text'] = $text;
            } else {
                $Thread['text'] = '';
            }

            $Thread['group_id'] = $groupId;

            $Thread['created_by'] = Auth::id();
            $Thread->save();


            $tagsArray = explode(' ', $this->editingTag);
            $newThreadId = $Thread->id;

            foreach ($tagsArray as $tagName) {
                $existingTag = Tags::where('name', $tagName)->where('thread_id', $newThreadId)->first();

                if (!$existingTag) {
                    $Tag = new Tags;
                    $Tag['name'] = $tagName;
                    $Tag['thread_id'] = $newThreadId;
                    $Tag->save();
                }
            }

            $this->showAlert('success', 'Vlákno úspěšně vytvořena.');
            $this->inputField = '';
            $this->emit('update-create-thread');
            return redirect("/group/{$groupSlug}");
        }
    }



    public function render()
    {
        return view('livewire.public.create-thread');
    }
}
