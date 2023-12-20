<?php

namespace App\Http\Livewire\Public;

use App\Models\UserHasRoles;
use App\Traits\AlertHelper;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Users extends Component
{
    use AlertHelper;

    protected $listeners = [
        'update-users' => '$refresh',
        'destroyConfirmed' => 'removeUser',
    ];

    public Collection $users;
    public $roleSelect;
    public $editingId;
    public $search;

    public $sortByName;
    public $sortByEmail;

    public function mount()
    {
        $this->users = \App\Models\User::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->editingId = -1;
        $this->search = '';
        $this->emit('update-users');
    }

    public function search()
    {
        if (empty($this->search)) {
            $this->users = \App\Models\User::all();
            return;
        }
        $this->users = \App\Models\User::query()->where('name', 'LIKE', '%' . $this->search . '%')->get();
        $this->emit('update-users');
    }

    public function sortBy($number)
    {
        if ($number == 1) {
            if ($this->sortByName == 1) {
                $this->sortByName = 0;
            } else {
                $this->sortByName = 1;
                $this->sortByEmail = 0;
            }
        } else if ($number == 2) {
            if ($this->sortByEmail == 1) {
                $this->sortByEmail = 0;
            } else {
                $this->sortByName = 0;
                $this->sortByEmail = 1;
            }
        }
    }

    public function toggleEdit($id): void
    {
        if ($this->editingId == $id) {
            $this->editingId = -1;
        } else {
            $this->editingId = $id;
        }
    }

    public function save($model_id)
    {
        $role = $this->roleSelect;
        $UserHasRoles = \App\Models\UserHasRoles::query()
            ->where('model_id', $model_id)
            ->first();
        if ($role == null) {
            $this->roleSelect = $UserHasRoles['role_id'];
        }
        if ($role == 6) {
            $this->deleteUser($model_id);
            return;
        }
        if ($UserHasRoles) {
            $UserHasRoles['role_id'] = $this->roleSelect;;
            $UserHasRoles->save();
            $this->emit('update-users');
            $this->showAlert('success', 'Role změněna!');
        } else {
            $UserHasRoles = new UserHasRoles;
            $UserHasRoles['role_id'] = $role;
            $UserHasRoles['model_type'] = 'App\Models\User';
            $UserHasRoles['model_id'] = $model_id;
            $UserHasRoles->save();
            $this->showAlert('success', 'Role úspěšně uložena.');
        }
        $this->editingId = -1;
        $this->emit('update-users');
    }

    public function removeUser($response)
    {
        $id = $response['data']['inputAttributes']['value'];
        $users = \App\Models\GroupUser::where('user_id', $id)->get();
        foreach ($users as $user) {
            $user->delete();
        }
        $usersManage = \App\Models\UserManageGroup::where('user_id', $id)->get();
        foreach ($usersManage as $uM) {
            $uM->delete();
        }

        $usersManageRequest = \App\Models\GroupManageRequest::where('user_id', $id)->get();
        foreach ($usersManageRequest as $uMR) {
            $uMR->delete();
        }

        $usersJoinRequest = \App\Models\GroupJoinRequest::where('user_id', $id)->get();
        foreach ($usersJoinRequest as $uJR) {
            $uJR->delete();
        }

        $votes = \App\Models\Vote::where('created_by', $id)->get();
        foreach ($votes as $vote) {
            $vote->delete();
        }

        $threads = \App\Models\Thread::where('created_by', $id)->get();
        foreach ($threads as $thread) {
            $thread->delete();
        }

        $comments = \App\Models\Comment::where('created_by', $id)->get();
        foreach ($comments as $comm) {
            $comm->delete();
        }

        $subcomments = \App\Models\SubComment::where('created_by', $id)->get();
        foreach ($subcomments as $subcomm) {
            $subcomm->delete();
        }

        $models = \App\Models\UserHasRoles::where('model_id', $id)->get();
        foreach ($models as $mod) {
            $mod->delete();
        }

        $groups = \App\Models\Group::where('created_by', $id)->get();
        foreach ($groups as $group) {
            $group->delete();
        }

        \App\Models\User::destroy($id);
        $this->showAlert('success', 'Účet smazán.');
        $this->emit('update-users');
        return redirect('/');
    }

    public function deleteUser($id)
    {
        $user = \App\Models\User::find($id);
        $table = UserHasRoles::query()->where('model_id', $id)->where('role_id', 1)->exists();
        if ($table) {
            $this->showAlert('error', 'Účet s Admin rolí nelze smazat.');
        }
        if ($user && !$table) {
            $this->confirm('Opravdu chcete smazat účet ' . $user->name . '?', [
                'inputAttributes' => [
                    'value' => $id,
                ],
                'onConfirmed' => 'destroyConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.public.users');
    }
}
