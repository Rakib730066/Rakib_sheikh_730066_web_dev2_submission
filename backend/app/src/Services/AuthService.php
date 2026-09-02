<?php

namespace App\Services;

use App\Helpers\Validator;
use App\Repositories\UserRepository;
use App\Repositories\IUserRepository;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;
    private JwtService $jwtService;

    public function __construct(?IUserRepository $userRepository = null, ?JwtService $jwtService = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->jwtService = $jwtService ?? new JwtService();
    }

    public function register(array $data): array
    {
        $validator = new Validator();
        $name = trim($data['name'] ?? '');
        $validator->validateRequired($name, 'name');
        $validator->validateLength($name, 2, 255, 'name');

        $email = trim($data['email'] ?? '');
        $validator->validateEmail($email);

        $password = $data['password'] ?? '';
        $validator->validatePassword($password);

        if ($validator->hasErrors()) {
            throw new \InvalidArgumentException(implode(', ', $validator->getErrors()));
        }

        try {
            if ($this->userRepository->findByEmail($email)) {
                throw new \RuntimeException('Email already exists');
            }

            $user = $this->userRepository->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'user'
            ]);

            $token = $this->jwtService->createToken([
                'id' => $user->id,
                'role' => $user->role
            ]);

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token
            ];
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('Registration failed');
        }
    }

    public function login(array $data): array
    {
        $validator = new Validator();
        $email = trim($data['email'] ?? '');
        $validator->validateEmail($email);

        $password = $data['password'] ?? '';
        $validator->validateRequired($password, 'password');

        if ($validator->hasErrors()) {
            throw new \InvalidArgumentException(implode(', ', $validator->getErrors()));
        }

        try {
            $user = $this->userRepository->findByEmail($email);
            if (!$user || !password_verify($password, $user->password)) {
                throw new \RuntimeException('Invalid email or password');
            }

            $token = $this->jwtService->createToken([
                'id' => $user->id,
                'role' => $user->role
            ]);

            return [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ],
                'token' => $token
            ];
        } catch (\RuntimeException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \RuntimeException('Login failed');
        }
    }

    public function verifyCredentials(string $email, string $password): object
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || !password_verify($password, $user->password)) {
            throw new \RuntimeException('Invalid credentials');
        }

        return $user;
    }
}
