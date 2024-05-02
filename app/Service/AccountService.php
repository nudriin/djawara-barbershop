<?php

namespace Nurdin\Djawara\Service;

use Dotenv\Dotenv;
use Firebase\JWT\JWT;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Domain\Account;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Account\AccountDeleteRequest;
use Nurdin\Djawara\Model\Account\AccountDisplayResponse;
use Nurdin\Djawara\Model\Account\AccountGetAllRequest;
use Nurdin\Djawara\Model\Account\AccountGetAllResponse;
use Nurdin\Djawara\Model\Account\AccountGetRequest;
use Nurdin\Djawara\Model\Account\AccountGetResponse;
use Nurdin\Djawara\Model\Account\AccountLoginRequest;
use Nurdin\Djawara\Model\Account\AccountLoginResponse;
use Nurdin\Djawara\Model\Account\AccountPasswordRequest;
use Nurdin\Djawara\Model\Account\AccountPasswordResponse;
use Nurdin\Djawara\Model\Account\AccountRegisterRequest;
use Nurdin\Djawara\Model\Account\AccountRegisterResponse;
use Nurdin\Djawara\Model\Account\AccountUpdateProfileRequest;
use Nurdin\Djawara\Model\Account\AccountUpdateProfileResponse;
use Nurdin\Djawara\Repository\AccountRepository;

class AccountService
{
    private AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../"); // ! BISA BEGINI
        $dotenv->load();
    }

    public function register(AccountRegisterRequest $request, $role): AccountRegisterResponse
    {
        $this->validateRegister($request);
        try {
            Database::beginTransaction();
            $account = $this->accountRepository->findAccount($request->username, 'username');
            if ($account != null) {
                throw new ValidationException("Username is already exist", 400);
            }

            $account = $this->accountRepository->findAccount($request->email, 'email');
            if ($account != null) {
                throw new ValidationException("Email is already exist", 400);
            }

            $account = new Account();
            $account->username = $request->username;
            $account->password = password_hash($request->password, PASSWORD_BCRYPT);
            $account->email = $request->email;
            $account->role = $role;
            $account->name = $request->name;
            $account->phone = $request->phone;
            $account->address = $request->address;
            $account->profile_pic = "https://firebasestorage.googleapis.com/v0/b/mern-auth-5a53c.appspot.com/o/profile.svg?alt=media&token=37afdff7-242d-4f97-9062-677c7cdd898d";

            $this->accountRepository->save($account);
            Database::commitTransaction();

            $response = new AccountRegisterResponse();
            $response->account = $account;

            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateRegister(AccountRegisterRequest $request)
    {
        if (
            $request->username == null || $request->email == null || $request->name == null || $request->password == null || $request->phone == null ||
            trim($request->username) == "" || trim($request->email) == "" || trim($request->name) == "" || trim($request->password) == "" || trim($request->phone == "")
        ) {
            throw new ValidationException("Username, email, name, phone and password is required", 400);
        }
        
        if(!filter_var($request->email, FILTER_VALIDATE_EMAIL)){
            throw new ValidationException("Email must be valid email", 400);
        }
    }

    public function login(AccountLoginRequest $request): AccountLoginResponse
    {
        $this->validateLogin($request);
        try {
            $account = $this->accountRepository->findAccount($request->username, 'username');
            if ($account == null) {
                throw new ValidationException("Username or password is wrong", 400);
            }
            if (password_verify($request->password, $account->password)) {
                $response = new AccountLoginResponse();
                $expired_time = time() + (60 * 60);

                $payload = [
                    'id' => $account->id,
                    'username' => $account->username,
                    'email' => $account->email,
                    'name' => $account->name,
                    'phone' => $account->phone,
                    'role' => $account->role,
                    'address' => $account->address,
                    'profile_pic' => $account->profile_pic,
                    'exp' => $expired_time // token 1 jam
                ];

                $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
                $response->token = $token;

                return $response;
            } else {
                throw new ValidationException("Username or password is wrong", 400);
            }
        } catch (ValidationException $e) {
            throw $e;
        }
    }


    public function validateLogin(AccountLoginRequest $request)
    {
        if (
            $request->username == null || $request->password == null ||
            trim($request->username) == "" || trim($request->password) == ""
        ) {
            throw new ValidationException("Username and password is required", 400);
        }
    }


    public function getUser(AccountGetRequest $request): AccountGetResponse
    {
        $this->ValidateGetUser($request);
        try {
            $account = $this->accountRepository->findAccount($request->username, 'username');
            if ($account == null) {
                throw new ValidationException("User not found", 404);
            }

            $response = new AccountGetResponse();
            $response->account = $account;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }

    public function ValidateGetUser(AccountGetRequest $request)
    {
        if ($request->username == null || trim($request->username) == "") {
            throw new ValidationException("User not found", 404);
        }
    }

    public function updateUser(AccountUpdateProfileRequest $request): AccountUpdateProfileResponse
    {
        $this->validateAccountUpdateProfile($request);
        try {
            Database::beginTransaction();
            $account = $this->accountRepository->findAccount($request->username, 'username');
            if ($account == null) {
                throw new ValidationException("User not found", 404);
            }

            // cek jika user da ngirim request nama
            if ($request->name !== null && trim($request->name) != "") $account->name = $request->name;
            if ($request->profile_pic !== null && trim($request->profile_pic) != "") $account->profile_pic = $request->profile_pic;
            if ($request->phone !== null && trim($request->phone) != "") $account->phone = $request->phone;
            if ($request->address !== null && trim($request->address) != "") $account->address = $request->address;

            $this->accountRepository->update($account);
            Database::commitTransaction();

            $response = new AccountUpdateProfileResponse();
            $response->account = $account;
            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateAccountUpdateProfile(AccountUpdateProfileRequest $request)
    {
        if ($request->username == null || trim($request->username) == "") {
            throw new ValidationException("User not found", 404);
        }
    }

    public function changePassword(AccountPasswordRequest $request): AccountPasswordResponse
    {
        $this->validateChangePassword($request);
        try {
            Database::beginTransaction();
            $account = $this->accountRepository->findAccount($request->username, 'username');
            if ($account == null) {
                throw new ValidationException("User not found", 404);
            }

            if (!password_verify($request->oldPassword, $account->password)) {
                throw new ValidationException("Old password is wrong", 400);
            }

            $account->password = password_hash($request->newPassword, PASSWORD_BCRYPT);
            $this->accountRepository->update($account);
            Database::commitTransaction();

            $response = new AccountPasswordResponse();
            $response->account = $account;
            return $response;
        } catch (ValidationException $e) {
            Database::rollbackTransaction();
            throw $e;
        }
    }

    public function validateChangePassword(AccountPasswordRequest $request)
    {
        if ($request->oldPassword == null || $request->newPassword == null || trim($request->oldPassword) == "" || trim($request->newPassword) == "") {
            throw new ValidationException("Password is required", 400);
        }
    }
    
    public function deleteAccount(AccountDeleteRequest $request) 
    {
        $this->validateDeleteAccount($request);
        try{
            Database::beginTransaction();
            $account = $this->accountRepository->findAccount($request->username, 'username');
            if($account == null){
                throw new ValidationException("User not found", 404);
            }
            $this->accountRepository->deleteAccount($account->username, 'username');
            Database::commitTransaction();
        } catch(ValidationException $e){
            Database::rollbackTransaction();
            throw $e;
        }
    }
    
    
    public function validateDeleteAccount(AccountDeleteRequest $request)
    {
        if ($request->username == null || trim($request->username) == "") {
            throw new ValidationException("Password is required", 400);
        }
    }
    
    public function getAllAccount() : AccountGetAllResponse
    {
        try {
            $account = $this->accountRepository->findAll();
            if($account == null) throw new ValidationException("User not found", 404);
            
            $response = new AccountGetAllResponse();
            $response->account = $account;

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }
}
