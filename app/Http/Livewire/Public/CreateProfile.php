<?php

namespace App\Http\Livewire\Public;

use App\Traits\AlertHelper;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class CreateProfile extends Component
{
    use AlertHelper;

    protected $listeners = [
        'create-profile' => '$refresh'
    ];

    public Collection $groups;

    public $search;

    public $title;

    public $editingId;
    public $editingMail;
    public $editingPassword;
    public $confirmPassword;

    public $inputField;

    public $image;
    public $fileUploading;

    public function createGroup() {
        if (mb_strlen($this->inputField) > 0) {
            $name = $this->inputField;
            if($this->editingMail == null){
                $this->showAlert('error', 'Chybí E-Mail.');
                return;
            }
            if(!filter_var($this->editingMail, FILTER_VALIDATE_EMAIL)){
                $this->showAlert('error', 'Chybný E-Mail.');
                return;
            }
            if($this->editingPassword == null){
                $this->showAlert('error', 'Je potřeba heslo!');
                return;
            }
            $email = $this->editingMail;
            $password = $this->editingPassword;
            $confirmedPassword = $this->confirmPassword;

            if ($password !== $confirmedPassword) {
                $this->showAlert('error', 'Hesla se neshodují.');
                return;
            }

            $hashedPassword = Hash::make($password);

            $user = \App\Models\User::where('name', $name)->first();
            if ($user) {
                $this->showAlert('error', 'Uživatel se jménem již existuje!');
                return;
            }

            $user = new \App\Models\User;
            $user['name'] = $name;
            $user['email'] = $email;
            $user['password'] = $hashedPassword;
            $user->save();

            $this->inputField = '';
            $this->editingMail = '';
            $this->editingPassword = '';
            $this->confirmPassword = '';

            $this->emit('create-profile');
            $this->showAlert('success', 'Účet úspěšně vytvořen.');
            return redirect("./");
        }
        $this->showAlert('error', 'Vyplňte jméno!');
        return;
    }


    public function render()
    {
        return view('livewire.public.create-profile');
    }
}
