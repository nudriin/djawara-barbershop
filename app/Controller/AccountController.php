<?php

namespace Nurdin\Djawara\Controller;

use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Model\Account\AccountLoginRequest;
use Nurdin\Djawara\Model\Account\AccountPasswordRequest;
use Nurdin\Djawara\Model\Account\AccountRegisterRequest;
use Nurdin\Djawara\Model\Account\AccountUpdateProfileRequest;
use Nurdin\Djawara\Repository\AccountRepository;
use Nurdin\Djawara\Service\AccountService;
use Nurdin\Djawara\Model\Account\AccountDeleteRequest;
use Nurdin\Djawara\Model\Account\AccountGetRequest;

class AccountController
{
    private AccountService $accountService;

    public function __construct()
    {
        $connection = Database::getConnect();
        $accountRepository = new AccountRepository($connection);
        $this->accountService = new AccountService($accountRepository);
    }

    public function register()
    {
        try {
            /**
             * php://input': Ini adalah stream wrapper khusus dalam PHP yang memungkinkan akses ke 
             * data input permintaan HTTP raw. Dengan menggunakan 'php://input', Anda dapat 
             * membaca data yang dikirimkan dalam body permintaan HTTP, terlepas dari tipe konten
             * (seperti form data, JSON, XML, dll.).
             */
            $json = file_get_contents('php://input'); //! Ambil JSON yang dikirim oleh user
            // Decode json tersebut agar mudah mengambil nilainya
            $request = json_decode($json);

            if (!isset($request->username) || !isset($request->email) || !isset($request->name) || !isset($request->password) || !isset($request->phone)) {
                throw new ValidationException("username, email, name, phone and password is required", 400);
            }

            $registerRequest = new AccountRegisterRequest();
            $registerRequest->username = $request->username;
            $registerRequest->email = $request->email;
            $registerRequest->name = $request->name;
            $registerRequest->phone = $request->phone;
            $registerRequest->password = $request->password;

            if (isset($request->address)) {
                $registerRequest->address = $request->address;
            }

            $account = $this->accountService->register($registerRequest, "USER");

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'username' => $account->account->username,
                    'email' => $account->account->email,
                    'name' => $account->account->name,
                    'phone' => $account->account->phone,
                    'role' => $account->account->role,
                    'address' => $account->account->address,
                    'profile_pic' => $account->account->profile_pic,
                ]
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function adminRegister()
    {
        try {
            /**
             * php://input': Ini adalah stream wrapper khusus dalam PHP yang memungkinkan akses ke 
             * data input permintaan HTTP raw. Dengan menggunakan 'php://input', Anda dapat 
             * membaca data yang dikirimkan dalam body permintaan HTTP, terlepas dari tipe konten
             * (seperti form data, JSON, XML, dll.).
             */
            $json = file_get_contents('php://input'); //! Ambil JSON yang dikirim oleh user
            // Decode json tersebut agar mudah mengambil nilainya
            $request = json_decode($json);

            if (!isset($request->username) || !isset($request->email) || !isset($request->name) || !isset($request->password) || !isset($request->phone)) {
                throw new ValidationException("username, email, name, phone and password is required", 400);
            }

            $registerRequest = new AccountRegisterRequest();
            $registerRequest->username = $request->username;
            $registerRequest->email = $request->email;
            $registerRequest->name = $request->name;
            $registerRequest->phone = $request->phone;
            $registerRequest->password = $request->password;

            if (isset($request->address)) {
                $registerRequest->address = $request->address;
            }

            $account = $this->accountService->register($registerRequest, "ADMIN");

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'username' => $account->account->username,
                    'email' => $account->account->email,
                    'name' => $account->account->name,
                    'phone' => $account->account->phone,
                    'role' => $account->account->role,
                    'address' => $account->account->address,
                    'profile_pic' => $account->account->profile_pic,
                ]
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function login()
    {
        try {
            $json = file_get_contents('php://input');
            $request = json_decode($json);

            if (!isset($request->username) || !isset($request->password)) {
                throw new ValidationException("username and password is required", 400);
            }

            $loginRequest = new AccountLoginRequest();
            $loginRequest->username = $request->username;
            $loginRequest->password = $request->password;

            $token = $this->accountService->login($loginRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'token' => $token->token
                ]
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function current()
    {
        try {
            $headers = apache_response_headers();

            if ($headers['user'] == null || !isset($headers['user'])) {
                throw new ValidationException("Unauthorized", 401);
            }

            $user = json_decode($headers['user']);

            $getRequest = new AccountGetRequest();
            $getRequest->username = $user->username;
            $account = $this->accountService->getUser($getRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $account->account->id,
                    'username' => $account->account->username,
                    'email' => $account->account->email,
                    'name' => $account->account->name,
                    'phone' => $account->account->phone,
                    'role' => $account->account->role,
                    'address' => $account->account->address,
                    'profile_pic' => $account->account->profile_pic,
                ]
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function update()
    {
        try {
            $headers = apache_response_headers();
            if (!isset($headers['user']) || $headers['user'] == null) {
                throw new ValidationException("Unauthorized 2", 401);
            }
            $user = json_decode($headers['user']);

            $json = file_get_contents('php://input');
            $request = json_decode($json);

            $updateRequest = new AccountUpdateProfileRequest();
            $updateRequest->username = $user->username;

            if (isset($request->name) && $request->name != null) {
                $updateRequest->name = $request->name;
            }

            if (isset($request->profile_pic) && $request->profile_pic != null) {
                $updateRequest->profile_pic = $request->profile_pic;
            }

            if (isset($request->address) && $request->address != null) {
                $updateRequest->address = $request->address;
            }
            
            if (isset($request->phone) && $request->phone != null) {
                $updateRequest->phone = $request->phone;
            }

            $account = $this->accountService->updateUser($updateRequest);
            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $account->account->id,
                    'username' => $account->account->username,
                    'email' => $account->account->email,
                    'name' => $account->account->name,
                    'phone' => $account->account->phone,
                    'role' => $account->account->role,
                    'address' => $account->account->address,
                    'profile_pic' => $account->account->profile_pic,
                ]
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function password()
    {
        try {
            $headers = apache_response_headers();
            if (!isset($headers['user']) || $headers['user'] == null) {
                throw new ValidationException("Unauthorized", 401);
            }
            $user = json_decode($headers['user']);

            $json = file_get_contents('php://input');
            $request = json_decode($json);
            if (!isset($request->old_password) || !isset($request->new_password) || $request->old_password == null || $request->new_password == null) {
                throw new ValidationException("old_password and new_password is required", 400);
            }

            $passwordRequest = new AccountPasswordRequest();
            $passwordRequest->username = $user->username;
            $passwordRequest->oldPassword = $request->old_password;
            $passwordRequest->newPassword = $request->new_password;
            $account = $this->accountService->changePassword($passwordRequest);

            http_response_code(200);
            echo json_encode([
                'data' => [
                    'id' => $account->account->id,
                    'username' => $account->account->username,
                    'email' => $account->account->email,
                    'name' => $account->account->name,
                    'phone' => $account->account->phone,
                    'role' => $account->account->role,
                    'address' => $account->account->address,
                    'profile_pic' => $account->account->profile_pic,
                ]
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }

    public function remove()
    {
        try {
            $headers = apache_response_headers();
            if (!isset($headers['user']) || $headers['user'] == null) {
                throw new ValidationException("Unauthorized", 401);
            }
            $user = json_decode($headers['user']);

            $deleteRequest = new AccountDeleteRequest();
            $deleteRequest->username = $user->username;

            $this->accountService->deleteAccount($deleteRequest);

            echo json_encode([
                'data' => 'OK'
            ]);
        } catch (ValidationException $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }
}
