<?php 
namespace Controllers;

use Helpers\JwtHelper;
use Models\User;

class UserController {
    private function getBearerToken() {
        if (isset($_SERVER["HTTP_AUTHORIZATION"])) {
            $authorizationHeader = $_SERVER["HTTP_AUTHORIZATION"];
            if (preg_match(('/Bearer\s(\S+)/'), $authorizationHeader, $matches)) {
                $token = $matches[1];
            }
        }
        return null;
    }

   /* private function authenticate(){
        $token = $this->getBearerToken();

        if(!$token){
            http_response_code(401);
            echo json_encode(['error'=> 'Token não enviado']);
            exit;
        }

        $decoded = JwtHelper::validateToken($token);

        if(!$decoded){
            http_response_code(401);
            echo json_encode(['error'=> 'Token inválido']);
            exit;
        }

        return $decoded->user_id;
    }*/

    public function authenticate(){
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        $payload = JwtHelper::validateToken($token);
        return $payload->user_id ?? null;
    }
    http_response_code(401);
    echo json_encode(['error' => 'Token inválido ou ausente.']);
    exit;
}


    public function register(){
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');

        $data = json_decode($json,true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }        
        $required = ['nome', 'email', 'senha', 'genero', 'orientacao','genero_interesse'];
        foreach ($required as $field) {
            if(empty($data[$field])){
                http_response_code(400);
                echo json_encode(['error'=> "Campo '$field' é obrigatório"]);
                return;
            }
        }

        if(!filter_var($data["email"], FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode(["error"=> "Email inválido"]);
            return;
        }

        if (User::findByEmail($data["email"])){
            http_response_code(400);
            echo json_encode(["error"=> "Email já cadastrado."]);
            return;
        }

        if(User::create($data)){
            echo json_encode(["success"=> 'Usuário registrado com sucesso.']);
        } else {
            http_response_code(500);
            echo json_encode(['error'=> 'Erro ao registrar o usuário']);
        }
    }

    public function login(){
        header('Content-Type: application/json');
        $json = file_get_contents('php://input');

        $data = json_decode($json,true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'JSON inválido']);
            return;
        }        
        $email = $data['email'];
        $senha = $data['senha'];

        if(empty($email) || empty($senha)){
            http_response_code(400);
            echo json_encode(['error'=> 'Email e senha obrigátorios.']);
            return;
        }

        $user = User::findByEmail($email);
        if (!$user || !password_verify($senha, $user['senha'])){
            http_response_code(401);
            echo json_encode(['error'=> 'Credenciais inválidas']);
            return;
        }
        $token = JwtHelper::generateToken($user['id']);
        echo json_encode(['success'=> true,'token'=> $token,'logado como: '=> $user]);
    }

    public function logout(){
        echo json_encode(['status' => 'ok', 'message' => 'Logout feito.']);    
    }

    public function recoverPassword(){
        header('Content-Type: application/json');
        $email = $_POST['email'] ?? '';

        if(!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)){
            http_response_code(400);
            echo json_encode(['error'=> 'Email inválido']);
            return;
        }

        $token = User::generateResetToken($email);
        if (!$token){
            http_response_code(404);
            echo json_encode(['error'=> 'Usuário não encontrado']);
            return;
        }

        $link = "https://seusite.com/reset-password?token=$token";
        mail($email, "Recuperação de senha", "Clique aqui para redefinir: $link");

        echo json_encode(['sucess'=>"Email de recuperação enviado"]);
    }

    public function resetPassword(){
        header('Content-Type: application/json');
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['newPassword'] ??'';
        $user = User::findByResetToken($token);
        if(!$user){
            http_response_code(400);
            echo json_encode(['error'=> 'Token inválido ou expirado']);
            return;
        }

        User::updatePassword($user['id'],$newPassword);
        echo json_encode(['sucess'=> 'Senha redefinida com sucess']);
    }

    public function profile(){
        header('Content-Type: application/json');
        $userId = $this->authenticate();
        $user = User::findById($userId);

        unset($user['senha']);
        echo json_encode($user);
    }

    public function updateProfile() {
        header('Content-Type: application/json');
        $userId = $this->authenticate();
        $data = $_POST;

        if (User::updateProfile($userId, $data)) {
            echo json_encode(['success' => 'Perfil atualizado.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar perfil.']);
        }
    }

    public function checkAuth() {
        header('Content-Type: application/json');
        $this->authenticate();
        echo json_encode(['authenticated' => true]);
    }

    public function verifyToken() {
        $this->checkAuth();
    }

    public function changePassword() {
        header('Content-Type: application/json');
        $userId = $this->authenticate();

        $oldPassword = $_POST['oldPassword'] ?? '';
        $newPassword = $_POST['newPassword'] ?? '';

        $user = User::findById($userId);
        if (!password_verify($oldPassword, $user['senha'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Senha antiga incorreta.']);
            return;
        }

        User::updatePassword($userId, $newPassword);
        echo json_encode(['success' => 'Senha alterada com sucesso.']);
    }

    public function sendVerificationEmail() {
        header('Content-Type: application/json');
        $email = $_POST['email'] ?? '';

        $user = User::findByEmail($email);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'Usuário não encontrado.']);
            return;
        }

        $token = User::generateVerificationToken($user['id']);
        $link = "https://seusite.com/verify-email/$token";
        mail($email, "Verifique seu email", "Clique aqui para confirmar: $link");

        echo json_encode(['success' => 'Email de verificação enviado.']);
    }

    public function verifyEmail($token) {
        header('Content-Type: application/json');
        if (User::verifyEmail($token)) {
            echo json_encode(['success' => 'Email verificado.']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Token inválido.']);
        }
    }

    public function resendVerification() {
        header('Content-Type: application/json');
        $userId = $this->authenticate();
        $user = User::findById($userId);

        $token = User::generateVerificationToken($userId);
        $link = "https://seusite.com/verify-email/$token";
        mail($user['email'], "Reenvio de verificação", "Clique aqui para confirmar: $link");

        echo json_encode(['success' => 'Email de verificação reenviado.']);
    }

    public function accountStatus() {
        header('Content-Type: application/json');
        $userId = $this->authenticate();
        $user = User::findById($userId);

        echo json_encode(['status' => $user['status']]);
    }

    //nova função de atualizr a imagem
    
    public function updateProfilePicture()
{
    header('Content-Type: application/json');

    // Pegue os dados enviados via multipart/form-data
    $userId = $_POST['id'] ?? null;

    if (empty($userId)) {
        http_response_code(400);
        echo json_encode(['error' => 'Informe o id do usuário.']);
        return;
    }

    if (!isset($_FILES['profile_picture'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Nenhuma imagem enviada.']);
        return;
    }

    $file = $_FILES['profile_picture'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Tipo de imagem não permitido.']);
        return;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'Imagem muito grande.']);
        return;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('profile_', true) . '.' . $ext;
    $uploadDir = __DIR__ . '/../../api/photos/';
    $uploadPath = $uploadDir . $newFileName;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao salvar a imagem.']);
        return;
    }

    $relativePath = 'photos/' . $newFileName;

    if (User::updateProfilePicture($userId, $relativePath)) {
        echo json_encode(['success' => true, 'newImageUrl' => $relativePath]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar no banco de dados.']);
    }
}


}