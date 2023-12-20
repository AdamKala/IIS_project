<?php

namespace App\Http\Livewire\Public;

use App\Models\UserHasRoles;
use App\Traits\AlertHelper;
use Carbon\Carbon;
use Livewire\Component;

class UserProfile extends Component
{
    use AlertHelper;

    protected $listeners = [
        'update-user' => '$refresh',
        'destroyConfirmed' => 'removeUser'
    ];

    public $slug;
    public $editingId;
    public $editingInput;
    public $editingAboutMe;
    public $editingMail;

    public function toggleEdit($id)
    {
        if ($this->editingId == $id) {
            $this->editingId = -1;
            $this->editingInput = '';
            $this->editingAboutMe = '';
            $this->editingMail = '';
        } else {
            $user = \App\Models\User::find($id);
            if ($user) {
                $this->editingId = $id;
                $this->editingInput = $user['name'];
                $this->editingAboutMe = $user['about_me'];
                $this->editingMail = $user['email'];
            }
        }

        $this->emit('update-user');
    }

    public function saveEdit()
    {
        if ($this->editingId != -1) {
            $user = \App\Models\User::find($this->editingId);
            if ($user) {
                if (mb_strlen($this->editingInput) > 0) {
                    $user['name'] = $this->editingInput;
                    $user['about_me'] = $this->editingAboutMe;
                    $user['email'] = $this->editingMail;
                    $user->save();

                    $this->editingId = -1;
                    $this->editingInput = '';
                    $this->editingAboutMe = '';
                    $this->editingMail = '';

                    $this->emit('update-users');
                    $this->showAlert('success', 'Změny provedeny.');
                }
            }
        }
    }

    public function addAdminRole($user_id)
    {
        $user = \App\Models\User::where('id', $user_id)->first();
        if ($user) {
            UserHasRoles::where('model_id', $user_id)
                ->where('role_id', '=', 2)
                ->delete();

            $existingAdmin = UserHasRoles::where('model_id', $user_id)
                ->where('role_id', '=', 1)
                ->exists();

            if (!$existingAdmin) {
                $user->assignRole('Admin');
                $user->created_at = Carbon::now();
                $user->updated_at = Carbon::now();
                $user->save();
            }
        }
    }

    public function toggleEnabled($id){
        $user = \App\Models\User::find($id);
        if($user){
            $user['enabled'] = !$user['enabled'];
            $user->save();
            $this->alert('success', 'Status změněn.');
            $this->emit('update-users');
        }
    }

    public function removeUser($response){
        $id = $response['data']['inputAttributes']['value'];
        $users = \App\Models\GroupUser::where('user_id', $id)->get();
        foreach ($users as $user) {
            $user->delete();
        }

        $usersManageRequest = \App\Models\GroupManageRequest::where('user_id', $id)->get();
        foreach ($usersManageRequest as $uMR) {
            $uMR->delete();
        }

        $usersJoinRequest = \App\Models\GroupJoinRequest::where('user_id', $id)->get();
        foreach ($usersJoinRequest as $uJR) {
            $uJR->delete();
        }

        $votes = \App\Models\Vote::where('user_id', $id)->get();
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

    public function deleteUser($id){
        $user = \App\Models\User::find($id);
        $table = UserHasRoles::query()->where('model_id', $id)->where('role_id', 1)->exists();
        if($table){
            $this->showAlert('error', 'Účet s Admin rolí nelze smazat.');
        }
        if($user && !$table){
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
        return view('livewire.public.user-profile');
    }
}
