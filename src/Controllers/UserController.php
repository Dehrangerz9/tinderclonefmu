<?php 
namespace Controllers;

class UserController {
    public function login() {
        session_start();
        header('Content-Type: application/json');

        $email = $_POST['email'] ?? '';
        $senha = $_POST['senha'] ?? '';

        if (!$email || !$senha) {
            http_response_code(400);
            echo json_encode(['error' => 'Email e senha são obrigatórios.']);
            return;
        }

        $user = \Models\User::findByEmail($email);

        if (!$user || !password_verify($senha, $user['senha'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciais inválidas.']);
            return;
        }

        $_SESSION['user_id'] = $user['id'];
        echo json_encode(['status' => 'ok', 'message' => 'Login bem-sucedido.']);
    }

    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(['status' => 'ok', 'message' => 'Logout feito com sucesso.']);
    }

    public function register() {
        $data = $_POST;

        $requiredFields = ['nome', 'email', 'senha', 'genero', 'orientacao'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                http_response_code(400);
                echo json_encode(['error' => "Campo '$field' é obrigatório."]);
                return;
            }
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => "Email inválido."]);
            return;
        }

        // Verificar se o email já está cadastrado
        $existingUser = \Models\User::findByEmail($data['email']);
        if ($existingUser) {
            http_response_code(400);
            echo json_encode(['error' => 'Email já cadastrado.']);
            return;
        }

        $userCreated = \Models\User::create($data);
        if ($userCreated) {
            echo json_encode(['success' => 'Usuário registrado com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao registrar usuário.']);
        }
    }

    public function recoverPassword() {
        $email = $_POST['email'] ?? null;

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email inválido.']);
            return;
        }

        $token = \Models\User::generateResetToken($email);
        if (!$token) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado.']);
            return;
        }

        $link = "https://seusite.com/reset-password?token=$token";

        // Enviar email
        mail(
            $email,
            "Recuperação de senha",
            "Clique no link para redefinir sua senha: $link"
        );

        echo json_encode(['success' => 'Link de recuperação enviado para o e-mail.']);
    }
}
