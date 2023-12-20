<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\UserHasRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'about_me' => '',
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ])->validate();


        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'about_me' => '',
            'password' => Hash::make($input['password']),
        ]);

        $roles = new UserHasRoles;
        $roles['role_id'] = 2;
        $roles['model_type'] = 'App\Models\User';
        $roles['model_id'] = $user->id;
        $roles->save();
        return $user;
    }
}
