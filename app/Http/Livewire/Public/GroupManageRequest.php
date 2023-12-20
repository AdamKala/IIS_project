<?php

namespace App\Http\Livewire\Public;

use App\Models\GroupUser;
use App\Models\UserHasRoles;
use App\Traits\AlertHelper;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class GroupManageRequest extends Component
{
    use AlertHelper;

    protected $listeners = [
        'update-group-manage-request' => '$refresh',
    ];

    public Collection $manageRequest;

    public $slug;

    public $group;

    public function mount()
    {
        $this->group = \App\Models\Group::query()->where('slug', $this->slug)->first();
        if($this->group){
            $this->manageRequest = \App\Models\GroupManageRequest::query()->where('group_id', '=', $this->group->id)
                ->where('status', '=', 'ceka')->get();
        }
    }

    public function acceptRequest($id)
    {
        $manageRequest = \App\Models\GroupManageRequest::query()->where('id', $id)->first();
        if($manageRequest)
        {
            $manageRequest->accept();
            $this->manageRequest = \App\Models\GroupManageRequest::query()->where('group_id', '=', $this->group->id)
                ->where('status', '=', 'ceka')->get();
        }

        $userHasOtherRole = UserHasRoles::where('model_id', $manageRequest->user_id)->where('group_id', $this->group->id)->first();
        if($userHasOtherRole != null){
            $userHasOtherRole->delete();
        }
        $userHasRole = new UserHasRoles;
        $userHasRole['role_id'] = 5;
        $userHasRole['model_type'] = 'App\Models\User';
        $userHasRole['model_id'] = $manageRequest->user_id;
        $userHasRole['group_id'] = $this->group->id;
        $userHasRole->save();

        $accepted = new \App\Models\UserManageGroup;
        $accepted['user_id'] = $manageRequest->user_id;
        $accepted['group_id'] = $this->group->id;
        $accepted->save();
        $this->showAlert('success', 'Žádost přijata!');
        $slugHelp = \App\Models\Group::where('id', $manageRequest->group_id)->first();
        $slug = $slugHelp->slug;
        $this->emit('$refresh');
        return redirect("/group/{$slug}");
    }

    public function rejectRequest($id)
    {
        $manageRequest = \App\Models\GroupManageRequest::query()->where('id', $id)->first();
        if($manageRequest)
        {
            $this->showAlert('success', 'Žádost odmítnuta!');
            $manageRequest->reject();
            $this->manageRequest = \App\Models\GroupManageRequest::query()->where('group_id', '=', $this->group->id)
                ->where('status', '=', 'ceka')->get();
        }
        $slugHelp = \App\Models\Group::where('id', $manageRequest->group_id)->first();
        $slug = $slugHelp->slug;
        $this->emit('$refresh');
        return redirect("/group/{$slug}");
    }

    public function render()
    {
        return view('livewire.public.group-manage-request');
    }
}
