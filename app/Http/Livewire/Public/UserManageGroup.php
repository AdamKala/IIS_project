<?php

namespace App\Http\Livewire\Public;

use App\Models\UserHasRoles;
use App\Traits\AlertHelper;
use Livewire\Component;

class UserManageGroup extends Component
{
    use AlertHelper;

    public $slug;
    public $editingId = -1;
    public $editingIdDelete = -1;
    public bool $editing = false;
    public bool $editingDelete = false;
    public $userSelect;

    protected $listeners = [
        'update-user-manage-group' => '$refresh',
        'destroyConfirmed' => 'removeRole'
    ];

    public function removeRole($response){
        $user_id = $response['data']['inputAttributes']['value'];
        $group_id = $response['data']['inputAttributes']['group_id'];
        $users = \App\Models\UserManageGroup::where('user_id', $user_id)->where('group_id', $group_id)->where('role_type', 'moderator')->get();
        foreach ($users as $user) {
            $user->delete();
        }

        $usersManageRequest = \App\Models\GroupManageRequest::where('user_id', $user_id)->where('group_id', $group_id)->get();
        foreach ($usersManageRequest as $uMR) {
            $uMR->delete();
        }

        $models = \App\Models\UserHasRoles::where('model_id', $user_id)
            ->where('group_id', $group_id)
            ->whereIn('role_id', [4, 5])
            ->get();
        foreach ($models as $mod) {
            $mod->delete();
        }

        $userRole = new UserHasRoles;
        $userRole['role_id'] = 3;
        $userRole['model_type'] = 'App\Models\User';
        $userRole['model_id'] = $user_id;
        $userRole['group_id'] = $group_id;
        $userRole->save();



        $this->editingDelete = !$this->editingDelete;
        $this->showAlert('success', 'Role odebrána.');
        $slugSlug = \App\Models\Group::where('id', $group_id)->first();
        $slug = $slugSlug->slug;

        return redirect("/group/{$slug}");
    }

    public function deleteRole($group_id, $user_id){
        $user = \App\Models\User::find($user_id);
        $table = UserHasRoles::query()->where('model_id', $user_id)->where('role_id', 1)->exists();
        if($table){
            $this->showAlert('error', 'Účet s Admin rolí nelze smazat.');
        }
        if($user && !$table){
            $this->confirm('Opravdu chcete odebrat roli?', [
                'inputAttributes' => [
                    'value' => $user_id,
                    'group_id' => $group_id,
                ],
                'onConfirmed' => 'destroyConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        }
    }

    public function addManager($group_id, $user_id)
    {
        $table = UserHasRoles::query()->where('model_id', $user_id)->where('group_id', $group_id)->where('role_id', 3)->first();
        if($table){
            $table->delete();
        }

        $group = new \App\Models\UserManageGroup;
        $group['group_id'] = $group_id;
        $group['user_id'] = $user_id;
        $group['role_type'] = 'moderator';
        $group->save();

        $role = new UserHasRoles;
        $role['role_id'] = 5;
        $role['model_type'] = 'App\Models\User';
        $role['model_id'] = $user_id;
        $role['group_id'] = $group_id;
        $role->save();
        $this->editing = !$this->editing;
        $this->showAlert('success', 'Role úspěšně uložena.');

        $slugSlug = \App\Models\Group::where('id', $group_id)->first();
        $slug = $slugSlug->slug;

        return redirect("/group/{$slug}");
    }

    public function toggleAddUser()
    {
        $this->editing = !$this->editing;
        if (!$this->editing) {
            $this->editingId = -1;
        }
    }

    public function toggleDeleteUser()
    {
        $this->editingDelete = !$this->editingDelete;
        if (!$this->editingDelete) {
            $this->editingIdDelete = -1;
        }
    }

    public function render()
    {
        return view('livewire.public.user-manage-group');
    }
}
