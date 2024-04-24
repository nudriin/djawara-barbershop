<?php

namespace Nurdin\Djawara\Middleware;

use Dotenv\Dotenv;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Nurdin\Djawara\Config\Database;
use Nurdin\Djawara\Exception\ValidationException;
use Nurdin\Djawara\Middleware\Middleware;
use Nurdin\Djawara\Model\Account\AccountGetRequest;
use Nurdin\Djawara\Repository\AccountRepository;
use Nurdin\Djawara\Service\AccountService;

class AuthAdminMiddleware implements Middleware
{
    private AccountService $accountService;
    public function __construct()
    {
        $connection = Database::getConnect();
        $accountRepository = new AccountRepository($connection);
        $this->accountService = new AccountService($accountRepository);
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../"); // ! BISA BEGINI
        $dotenv->load();
    }

    public function auth(): void
    {
        try {
            if(!isset($_SERVER['HTTP_AUTHORIZATION'])) throw new ValidationException("Unauthorized", 401);

            $authorization = $_SERVER['HTTP_AUTHORIZATION'];
            if (!isset($authorization)) throw new ValidationException("Unauthorized", 401);

            list(, $token) = explode(' ', $authorization);
            $payload = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

            $request = new AccountGetRequest();
            $request->username = $payload->username;

            $account = $this->accountService->getUser($request);

            if ($account == null) throw new ValidationException("User not found", 404);
            if ($account->account->role == "USER") throw new ValidationException("Unauthorized", 401);

            $user = [
                'id' => $account->account->id,
                'username' => $account->account->username,
                'email' => $account->account->email,
                'name' => $account->account->name,
                'phone' => $account->account->phone,
                'role' => $account->account->role,
                'address' => $account->account->address,
                'profile_pic' => $account->account->profile_pic,
            ];

            http_response_code(200);
            $json = json_encode($user);
            header("user: $json");
        } catch (Exception $e) {
            // SignatureInvalidException // ! error dari jwtnya
            http_response_code($e->getCode());
            echo json_encode([
                'errors' => $e->getMessage()
            ]);
            exit();
        }
    }
}
