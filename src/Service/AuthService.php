<?php

namespace App\Service;

use App\Service\FormService;
use App\Model\AuthModel;
use App\Repository\UserRepository;

class AuthService
{
    private array $errors = [];

    public function __construct(
        private readonly ?FormService $formService = null,
        private readonly ?AuthModel $authModel = null,
        private readonly ?UserRepository $userRepository= null
    ){}

    public function register(array $request): array|bool
    {
        $fname = trim($request['fname'] ?? '');
        $lname = trim($request['lname'] ?? '');
        $phone = trim($request['phone'] ?? '');
        $email = trim($request['email'] ?? '');
        $password = $request['password'] ?? '';
        $fields = [
            'First name' => $fname,
            'Last name' =>$lname,
            'Phone Number' => $phone
        ];
        $this->errors = $this->formService->validateRequired($fields);
        $this->errors = array_merge(
            $this->errors,
            $this->formService->validateEmail($email,$this->userRepository)
        );
        $this->errors = array_merge(
            $this->errors,
            $this->formService->validatePassword($password)
        );
        if (!empty($this->errors)){
            $response = [
                'errors' => $this->errors,
                'oldValues' => [
                    'fname' => $fname,
                    'lname' => $lname,
                    'phone' => $phone,
                    'email' => $email,
                ]

            ];
            return $response;
        }
        $hashPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($hashPassword === false) {
            return ['Password hashing failed'];
        }
        $this->authModel->register($fname, $lname, $phone, $email, $hashPassword);
        return true;
    }

    public function login(array $request): array|bool
    {
        $email = trim($request['email'] ?? '');
        $password = $request['password'];
        $this->errors = $this->formService->validateRequired(['Password'=>$password]);
        $this->errors = array_merge(
            $this->errors,
            $this->formService->validateEmail($email)
        );
        if (!empty($this->errors)){
            return [
                'errors' => $this->errors
            ];
        }
        $user = $this->userRepository->findUserByEmail($email);
        if (!$user){
            $this->errors[] = "Invalid credentials";
            return  [
                'errors' =>$this->errors,
            ];
        }
        if (!$user->isActive()){
            $this->errors[] = "Please activate your account";
            return  [
                'errors' =>$this->errors,
                'email' => $email
            ];
        }
        if (!password_verify($password, $user->getPassword())){
            $this->errors[] = "Invalid credentials";
            return  [
                'errors' =>$this->errors,
            ];
        }
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        
        return true;
    }

    public function logout(){
        session_destroy();
        header("Location: /");
        exit();
    }
}