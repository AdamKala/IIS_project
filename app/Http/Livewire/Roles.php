<?php

namespace App\Http\Livewire;

use App\Traits\AlertHelper;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Roles extends Component
{
    use AlertHelper;

    protected $listeners = [
        'destroyConfirmed' => 'removeGroup',
    ];

    public bool $addingRole = false;
    public string $addingRoleName = '';

    public $roles = [];

    public $editingId = -1;

    public function mount(): void
    {
        $this->roles = Role::all();
    }

    public function toggleAdd(): void
    {
        $this->addingRole = !$this->addingRole;
        $this->addingRoleName = '';
    }

    public function createGroup(): void
    {
        if($this->addingRole && mb_strlen($this->addingRoleName) > 0)
        {
            $role = Role::query()->where('guard_name', '=', 'web')
                ->where('name', '=', $this->addingRoleName)->first();
            if($role)
            {
                $this->showAlert('error', 'Role již existuje!');
                return;
            }
            $role = Role::create(['guard_name' => 'web', 'name' => $this->addingRoleName]);
            $this->showAlert('success', 'Role úspěšně vytvořena!');
            $this->toggleAdd();
            $this->roles = Role::all();
        }
    }

    public function toggleEdit($id):void
    {
        if($this->editingId == $id)
        {
            $this->editingId = -1;
        } else {
            $this->editingId = $id;
        }
    }

    public function revokePermission($roleId, $permissionName): void
    {
        $role = Role::query()->where('guard_name', '=', 'web')->where('id', '=', $roleId)->first();
        if($role)
        {
            $role->revokePermissionTo($permissionName);
        }
    }

    public function removeGroup($response): void
    {
        $id = $response['data']['inputAttributes']['value'];
        Role::destroy($id);
        $this->showAlert('success', 'Role úspěšně odstraněna.');
        $this->roles = Role::all();
    }

    public function deleteGroup($roleId): void
    {
        $role = Role::query()->where('guard_name', '=', 'web')->where('id', '=', $roleId)->first();
        if($role)
        {
            if($role->name === 'Admin'){
                $this->showAlert('error', 'Role admin nemůže být smazána!');
                return;
            }
            $this->confirm('Opravdu chcete odstranit roli "' . $role['name'] . '" ?', [
                'inputAttributes' => [
                    'value' => $roleId,
                ],
                'onConfirmed' => 'destroyConfirmed',
                'position' => 'top',
                'cancelButtonText' => 'Zrušit',
                'confirmButtonText' => 'Smazat',
            ]);
        }

    }

    public function grantPermission($roleId, $permissionName): void
    {
        $role = Role::query()->where('guard_name', '=', 'web')->where('id', '=', $roleId)->first();
        if($role)
        {
            $role->givePermissionTo($permissionName);
        }
    }

    public function render()
    {
        return view('livewire.roles');
    }
}
