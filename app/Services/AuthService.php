<?php

namespace App\Services;

use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Handles the registration of a new user and returns the newly created user object.
     *
     * @param string $name The name of the user.
     * @param string $email The email address of the user.
     * @param string $password The password for the user account.
     * @return User The newly created user instance.
     */
    public function register(string $name, string $email, string $password): User
    {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        return $user;
    }

    /**
     * Authenticates a user based on email and password.
     *
     * @param string $email The email address of the user.
     * @param string $password The password for the user account.
     * @return User The authenticated user instance.
     * @throws InvalidCredentialsException If the credentials are not valid.
     */
    public function login(string $email, string $password): User
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }

    /**
     * Generates and returns an API token for the given user.
     *
     * @param User $user The user instance for whom the token is to be created.
     * @return string The generated plain text API token.
     */
    public function getToken(User $user) : string{
        return $user->createToken('auth_token')->plainTextToken;
    }
}
