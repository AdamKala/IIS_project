<?php

namespace App\Http\Livewire\Public;

use App\Models\Comment;
use App\Models\GroupUser;
use App\Models\User;
use App\Traits\AlertHelper;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Group;

class Groups extends Component
{
    use AlertHelper;

    protected $listeners = [
        'update-groups' => '$refresh',
        'destroyGroupConfirmed' => 'removeGroups'
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
        $this->editingDescription = '';
        $this->fileUploading = false;
        $this->emit('update-groups');
    }

    public function joinGroup($groupid)
    {
        $user = User::find(Auth::id());
        if ($user == null) {
            $this->showAlert('error', 'Nejste zaregistrovaný!');
        } else {
            $request = new \App\Models\GroupJoinRequest;
            $request['group_id'] = $groupid;
            $request['user_id'] = $user->id;
            $request->save();

            $this->showAlert('success', 'Žádost odeslána!');
        }
    }

    public function search()
    {
        if (empty($this->search)) {
            $this->groups = Group::all();
            return;
        }
        $this->groups = Group::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->emit('update-groups');
    }

    public function saveEdit()
    {
        if ($this->editingId != -1) {
            $model = Group::find($this->editingId);
            if ($model) {
                if (mb_strlen($this->editingInput) > 0) {
                    if ($model['slug'] != $this->editingSlug) {
                        $groupModel = Group::query()->where('slug', '=', $this->editingSlug)->first();
                        if ($groupModel) {
                            $this->showAlert('error', 'Slug se již používá!');
                            return;
                        }
                    }
                    $model['name'] = $this->editingInput;
                    $model['slug'] = Str::slug($this->editingSlug);
                    $model['image_path'] = $this->image;
                    $model['description'] = $this->editingDescription;

                    $model->save();
                    $this->editingId = -1;
                    $this->editingInput = '';
                    $this->editingSlug = '';
                    $this->image = '';
                    $this->editingImage = '';
                    $this->editingDescription = '';

                    $this->emit('update-groups');
                    $this->showAlert('success', 'Skupina úspěšně upravena.');
                }
            }
        }
    }

    public function toggleEdit($id)
    {
        if ($this->editingId == $id) {
            $this->editingId = -1;
            $this->editingInput = '';
            $this->editingSlug = '';
            $this->editingTag = '';
            $this->editingDescription = '';
        } else {
            $model = Group::find($id);
            if ($model) {
                $this->editingId = $id;
                $this->editingInput = $model['name'];
                $this->editingSlug = $model['slug'];
                $this->editingDescription = $model['description'];
            }
        }
        $this->emit('update-groups');
    }


    public function removeGroups($response)
    {
        $id = $response['data']['inputAttributes']['value'];
        $threads = \App\Models\Thread::where('group_id', $id)->get();
        $userManageGroup = \App\Models\UserManageGroup::where('group_id', $id)->get();
        $joinRequest = \App\Models\GroupJoinRequest::where('group_id', $id)->get();
        $manageRequest = \App\Models\GroupManageRequest::where('group_id', $id)->get();
        $groupUser = \App\Models\GroupUser::where('group_id', $id)->get();
        if($groupUser != null){
            foreach($groupUser as $gU){
                $gU->delete();
            }
        }
        if($manageRequest != null){
            foreach($manageRequest as $mR){
                $mR->delete();
            }
        }
        if($joinRequest != null){
            foreach($joinRequest as $jR){
                $jR->delete();
            }
        }
        if($threads != null){
            foreach ($threads as $thread) {
                $thread->delete();
                $comm = Comment::where('thread_id', $thread->id)->get();
                foreach ($comm as $cm) {
                    $cm->delete();
                }
            }
        }
        if($userManageGroup != null){
            foreach ($userManageGroup as $umg) {
                $umg->delete();
            }
        }
        Group::destroy($id);
        $this->showAlert('success', 'Skupina úspěšně odstraněna.');
        $this->emit('update-groups');
    }

    public function deleteGroups($id)
    {
        $model = Group::find($id);
        if ($model) {
            $this->confirm('Opravdu chcete odstranit skupinu ' . $model['name'] . '?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'destroyGroupConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        } else {
            $this->showAlert('success', 'Skupina nebyla nalezena.');
        }
    }

    public function toggleEnabled($id)
    {
        $model = Group::find($id);
        if ($model) {
            $model['enabled'] = !$model['enabled'];
            $model->save();
            $this->showAlert('success', 'Status změněn.');
            $this->emit('update-groups');
        }
    }

    public function createGroup()
    {
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
            while (Group::query()->where('slug', '=', $slug)->first()) {
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
            $this->showAlert('success', 'Skupina úspěšně vytvořena.');
            $this->inputField = '';
            $this->groups = Group::all();
            $this->emit('update-create-group');
        }
    }

    public function render()
    {
        return view('livewire.public.groups');
    }
}
