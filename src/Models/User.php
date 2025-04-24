<?php

namespace Models;

use Core\Database;
use PDO;

class User {
    public static function findByEmail($email): ?array {
        $db = Database::connect();
        $stmt = $db->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public static function create($data) {
        $db = Database::connect();
    
        $stmt = $db->prepare("INSERT INTO usuarios (nome, email, senha, genero, orientacao,nascimento) VALUES (?, ?, ?, ?, ?,?)");
        return $stmt->execute([
            $data['nome'],
            $data['email'],
            password_hash($data['senha'], PASSWORD_DEFAULT),
            $data['genero'],
            $data['orientacao'],
            $data['nascimento']
        ]);
    }
    
    public static function generateResetToken($email) {
        $db = Database::connect();
        
        $stmt = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
    
        if ($stmt->rowCount() === 0) return false;
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = bin2hex(random_bytes(16));
    
        $stmt = $db->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, NOW() + INTERVAL '1 hour')");
        $stmt->execute([$user['id'], $token]);
    
        return $token;
    }
    
}
