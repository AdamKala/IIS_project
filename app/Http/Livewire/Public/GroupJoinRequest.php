<?php

namespace App\Http\Livewire\Public;

use App\Models\GroupUser;
use App\Models\UserHasRoles;
use App\Traits\AlertHelper;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Collection;
class GroupJoinRequest extends Component
{
    use AlertHelper;

    protected $listeners = [
        'update-group-join-request' => '$refresh',
    ];

    public Collection $joinRequest;

    public $slug;

    public $group;

    public function mount()
    {
        $this->group = \App\Models\Group::query()->where('slug', '=', $this->slug)->first();
        if($this->group){
            $this->joinRequest = \App\Models\GroupJoinRequest::query()->where('group_id', '=', $this->group->id)
                ->where('status', '=', 'ceka')->get();
        }
    }

    public function acceptRequest($id)
    {
        $joinRequest = \App\Models\GroupJoinRequest::query()->where('id', $id)->first();
        if($joinRequest)
        {
            $joinRequest->acceptJoin();
            $this->joinRequest = \App\Models\GroupJoinRequest::query()->where('group_id', '=', $this->group->id)
                ->where('status', '=', 'ceka')->get();
        }
        $accepted = new GroupUser;
        $accepted['user_id'] = $joinRequest->user_id;
        $accepted['group_id'] = $this->group->id;
        $accepted->save();

        $userHasRole = new UserHasRoles;
        $userHasRole['role_id'] = 3;
        $userHasRole['model_type'] = 'App\Models\User';
        $userHasRole['model_id'] = $joinRequest->user_id;
        $userHasRole['group_id'] = $this->group->id;
        $userHasRole->save();

        $this->showAlert('success', 'Žádost přijata!');
        $slugHelp = \App\Models\Group::where('id', $joinRequest->group_id)->first();
        $slug = $slugHelp->slug;
        $this->emit('$refresh');
        return redirect("/group/{$slug}");
    }

    public function rejectRequest($id)
    {
        $joinRequest = \App\Models\GroupJoinRequest::query()->where('id', $id)->first();
        if($joinRequest)
        {
            $this->showAlert('success', 'Žádost zamítnuta!');
            $joinRequest->rejectJoin();
            $this->joinRequest = \App\Models\GroupJoinRequest::query()->where('group_id', '=', $this->group->id)
                ->where('status', '=', 'ceka')->get();
        }
        $slugHelp = \App\Models\Group::where('id', $joinRequest->group_id)->first();
        $slug = $slugHelp->slug;
        $this->emit('$refresh');
        return redirect("/group/{$slug}");
    }

    public function render()
    {
        return view('livewire.public.group-join-request');
    }
}
