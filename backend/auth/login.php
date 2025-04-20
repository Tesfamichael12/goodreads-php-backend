<?php

declare(strict_types=1);

namespace App\Auth;

use PDO;
use PDOException;

class LoginService
{

    private $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }


    public function authenticateUser(string $email, string $password): array
    {
        try {
          
            $email = $this->sanitizeInput($email);
            $password = $this->sanitizeInput($password);

            if (!$this->validateCredentials($email, $password)) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password format'
                ];
            }

            
            $user = $this->getUserByEmail($email);

            if (!$user) {
               
                password_verify('dummy_password', '$2y$10$dummyhashdummyhashdummyhashdumm');
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            if (!password_verify($password, $user['password_hash'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            
            $this->updateLastLogin($user['id']);

            return [
                'success' => true,
                'user' => $this->prepareUserSessionData($user)
            ];

        } catch (PDOException $e) {
            error_log('Authentication error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'System error during authentication'
            ];
        }
    }

   
    private function getUserByEmail(string $email): ?array
    {
        $query = "SELECT u.id, u.name, u.email, u.password_hash, u.profile_pic, 
                         u.bio, r.name AS role, r.id AS role_id
                  FROM users u
                  JOIN roles r ON u.role_id = r.id
                  WHERE u.email = :email
                  LIMIT 1";

        $statement = $this->database->prepare($query);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC) ?: null;
    }


    private function validateCredentials(string $email, string $password): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        if (strlen($password) < 8) {
            return false;
        }

        return true;
    }

  
    private function sanitizeInput(string $input): string
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }

  
   
    private function updateLastLogin(int $userId): void
    {
        $query = "UPDATE users SET last_login = NOW() WHERE id = :id";
        $statement = $this->database->prepare($query);
        $statement->bindParam(':id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    
    private function prepareUserSessionData(array $user): array
    {
        return [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'profilePic' => $user['profile_pic'],
            'bio' => $user['bio'],
            'role' => $user['role'],
            'roleId' => (int)$user['role_id'],
            'lastLogin' => time()
        ];
    }
}