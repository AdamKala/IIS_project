<?php

namespace App\Http\Livewire\Public;

use App\Models\Group;
use App\Models\GroupUser;
use App\Models\UserHasRoles;
use App\Traits\AlertHelper;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateGroup extends Component
{
    use AlertHelper, WithFileUploads;

    protected $listeners = [
        'update-create-group' => '$refresh',
        'destroyConfirmed' => 'removeGroup'
    ];

    public Collection $groups;

    public $search;

    public $title;

    public $editingTitle;
    public $editingId;
    public $editingSlug;
    public $editingImage;
    public $editingDescription;

    public $userId;
    public $inputField;

    public $image;
    public $fileUploading;

    public function mount() {
        $this->inputField = '';
        $this->groups = Group::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->editingId = -1;
        $this->editingTitle = '';
        $this->editingSlug = '';
        $this->editingImage = '';
        $this->search = '';
        $this->image = '';
        $this->editingDescription = '';
        $this->fileUploading = false;
        $this->emit('update-create-group');
    }

    public function save()
    {
        $this->validate([
            'image' => 'image'
        ]);
        $this->fileUploading = true;
        $this->emit('$refresh');
    }

    public function search(){
        if(empty($this->search)) {
            $this->groups = Group::all();
            return;
        }
        $this->groups = Group::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->emit('update-create-group');
    }

    public function createGroup() {
        if (mb_strlen($this->inputField) > 0) {
            $name = $this->inputField;
            $slug = $this->editingSlug;
            $description = $this->editingDescription;
            $group = Group::where('name', '=', $name)->first();
            if ($group) {
                $this->showAlert('error', 'Skupina již existuje!');
                return;
            }
            $slug = Str::slug($name);
            $i = 0;
            while(Group::query()->where('slug', '=', $slug)->first()){
                $slug = Str::slug($name . ' ' . $i);
                $i++;
            }

            $group = new Group;
            $group['name'] = $name;
            $group['slug'] = $slug;
            $group['privacy'] = true;
            $group['description'] = $description;
            $group['image_path'] = '';
            $group['created_by'] = Auth::id();
            $group->save();

            $group_manage = new \App\Models\UserManageGroup;
            $group_manage['group_id'] = $group->id;
            $group_manage['user_id'] = Auth::id();
            $group_manage['role_type'] = 'spravce';
            $group_manage->save();

            $group_user = new GroupUser;
            $group_user['user_id'] = Auth::id();
            $group_user['group_id'] = $group->id;
            $group_user->save();



            $userHasRole = new UserHasRoles;
            $userHasRole['role_id'] = 4;
            $userHasRole['model_id'] = Auth::id();
            $userHasRole['model_type'] = 'App\Models\User';
            $userHasRole['group_id'] = $group->id;
            $userHasRole->save();

            $this->inputField = '';
            $this->groups = Group::all();
            $this->emit('update-create-group');
            $this->showAlert('success', 'Skupina úspěšně vytvořena.');
            return redirect("./");
        }
    }

    public function render()
    {
        return view('livewire.public.create-group');
    }
}
