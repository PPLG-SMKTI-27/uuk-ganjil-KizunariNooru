<?php
require_once __DIR__ . '/../Config/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail($email) {
        $e = $this->db->real_escape_string($email);
        $stmt = $this->db->prepare("SELECT * FROM user WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $e);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function verifyLogin($email, $password) {
        $u = $this->findByEmail($email);
        if (!$u) return null;
        return password_verify($password, $u['password']) ? $u : null;
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM user");
    }

    public function create($email, $password, $role) {
        $e = $this->db->real_escape_string($email);
        $pw = password_hash($password, PASSWORD_DEFAULT);
        $r = $this->db->prepare("INSERT INTO user (email, password, role) VALUES (?, ?, ?)");
        $r->bind_param("sss", $e, $pw, $role);
        return $r->execute() ? $this->db->insert_id : false;
    }

    public function updatePassword($id, $password) {
        $id = (int)$id;
        $pw = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        $stmt->bind_param("si", $pw, $id);
        return $stmt->execute();
    }

    public function delete($id) {
        $id = (int)$id;
        $stmt = $this->db->prepare("DELETE FROM user WHERE id_user = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function find($id) {
        $id = (int)$id;
        $stmt = $this->db->prepare("SELECT * FROM user WHERE id_user = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updateUser($id, $email, $role, $password = null) {
        $email = $this->db->real_escape_string($email);
        $role = $this->db->real_escape_string($role);

        $sql = "UPDATE user SET email = ?, role = ?";

        if ($password) {
            $pw = password_hash($password, PASSWORD_DEFAULT);
            $sql .= ", password = ?";
        }

        $sql .= " WHERE id_user = ?";

        $stmt = $this->db->prepare($sql);
        if ($password) {
            $stmt->bind_param("sssi", $email, $role, $pw, $id);
        } else {
            $stmt->bind_param("ssi", $email, $role, $id);
        }

        return $stmt->execute();
    }

    public function deleteUser(){
        $id = (int)($_GET['id'] ?? 0);
    
        // Hapus data siswa yang terkait
        $s = new Siswa();
        $s->deleteByUserId($id);
    
        // Hapus user setelah siswa
        $u = new User();
        $u->delete($id);
    
        $_SESSION['flash'] = ['type'=>'success','msg'=>'User dan data terkait dihapus'];
        header('Location: ../Public/index.php?c=admin&m=index');
    }


}
?>
