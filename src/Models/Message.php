<?php
namespace Models;

class Message {
    private $conn;
    private $table = 'messages';

    public $id;
    public $match_id;
    public $sender_id;
    public $receiver_id;
    public $content;
    public $read_status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Criar nova mensagem
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                  SET match_id = :match_id,
                      sender_id = :sender_id,
                      receiver_id = :receiver_id,
                      content = :content,
                      read_status = 0,
                      created_at = NOW()';

        $stmt = $this->conn->prepare($query);

        // Limpar dados
        $this->match_id = htmlspecialchars(strip_tags($this->match_id));
        $this->sender_id = htmlspecialchars(strip_tags($this->sender_id));
        $this->receiver_id = htmlspecialchars(strip_tags($this->receiver_id));
        $this->content = htmlspecialchars(strip_tags($this->content));

        // Vincular parâmetros
        $stmt->bindParam(':match_id', $this->match_id);
        $stmt->bindParam(':sender_id', $this->sender_id);
        $stmt->bindParam(':receiver_id', $this->receiver_id);
        $stmt->bindParam(':content', $this->content);

        if($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Obter mensagens por match
    public function getByMatch($match_id, $user_id) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                  WHERE match_id = :match_id
                  ORDER BY created_at ASC';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':match_id', $match_id);
        $stmt->execute();

        // Marcar mensagens como lidas
        $this->markAsRead($match_id, $user_id);

        return $stmt;
    }

    // Marcar mensagens como lidas
    private function markAsRead($match_id, $user_id) {
        $query = 'UPDATE ' . $this->table . ' 
                  SET read_status = 1
                  WHERE match_id = :match_id 
                  AND receiver_id = :user_id
                  AND read_status = 0';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':match_id', $match_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    }

    // Obter lista de chats
    public function getChatList($user_id) {
        $query = 'SELECT m.match_id, 
                        MAX(m.enviada_em) AS last_message_time,
                        CASE 
                        WHEN mt.usuario1_id = :user_id THEN u2.name
                        ELSE u1.name
                        END AS partner_name,
                        CASE 
                        WHEN mt.usuario1_id = :user_id THEN u2.profile_picture
                        ELSE u1.profile_picture
                        END AS partner_picture
                FROM ' . $this->table . ' m
                JOIN matches mt ON mt.id = m.match_id
                JOIN usuarios u1 ON u1.id = mt.usuario1_id
                JOIN usuarios u2 ON u2.id = mt.usuario2_id
                WHERE mt.usuario1_id = :user_id OR mt.usuario2_id = :user_id
                GROUP BY m.match_id, partner_name, partner_picture
                ORDER BY last_message_time DESC';


        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        return $stmt;
    }
}
?>