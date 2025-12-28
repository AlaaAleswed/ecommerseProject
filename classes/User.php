<?php
class User
{
    private $db;
    private $table = "users";
    public $id;
    public $username;
    public $password;
    public $email;
    public $full_name;
    public $phone;
    public $role;
    public $created_at;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    public function register()
    {
        if (empty($this->username) || empty($this->email) || empty($this->password)) {
            return false;
        }
        $check = $this->db->prepare("SELECT id FROM " . $this->table . " WHERE username = ? OR email = ?");
        $check->execute([$this->username, $this->email]);
        if ($check->fetch()) {
            return false;


            # code...
        }
        $username = htmlspecialchars(strip_tags($this->username));
        $email = htmlspecialchars(strip_tags($this->email));
        $full_name = isset($this->full_name) ? htmlspecialchars(strip_tags(strip_tags($this->full_name))) : "";
        $phone = isset($this->phone) ? htmlspecialchars(strip_tags(strip_tags($this->phone))) : "";
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO " . $this->table . "
        (username, email, password, full_name, phone, role)
        VALUES (:username, :email, :password, :full_name, :phone, 'user')
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":password", $hashed_password, PDO::PARAM_STR);
        $stmt->bindValue(":full_name", $full_name, PDO::PARAM_STR);
        $stmt->bindValue(":phone", $phone, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }
    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            return false;
        }
        $sql = "SELECT * FROM " . $this->table . " WHERE username = :username OR email = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":username", $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user["password"])) {
            session_start();
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['logged_in'] = true;
            return true;
        }
        return false;

    }
    public function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        return true;
    }
    public function create()
    {
        return $this->register();
    }
    public function readAll($limit = 10, $offset = 0, $search = '', $role = null)
    {
        $sql = 'SELECT id, username, email, full_name, phone, role, created_at
    FROM ' . $this->table . ' WHERE 1=1';
        $params = [];
        if (!empty($search)) {
            $sql .= ' AND (username LIKE :search OR email LIKE :search OR full_name LIKE :search)';
            $params[':search'] = '%' . htmlspecialchars(strip_tags($search)) . '%';
        }
        if ($role !== null) {
            $sql .= ' AND role = :role';
            $params[':role'] = $role;
        }
        $sql .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);

        }
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function getTotalCount($search = '', $role = null)
    {
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->table . ' WHERE 1=1';
        $params = [];
        if (!empty($search)) {
            $sql .= ' AND (username LIKE :search OR email LIKE :search OR full_name LIKE :search)';
            $params[':search'] = '%' . htmlspecialchars(strip_tags($search)) . '%';
        }
        if ($role !== null) {
            $sql .= ' AND role = :role';
            $params[':role'] = $role;
        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];

    }
    public function readOne($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = 'SELECT id, username, email, full_name, phone, role, created_at
    FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        if (empty($this->id) || !is_numeric($this->id)) {
            return false;
        }
        $username = htmlspecialchars(strip_tags($this->username));
        $email = htmlspecialchars(strip_tags($this->email));
        $full_name = isset($this->full_name) ? htmlspecialchars(strip_tags($this->full_name)) : '';
        $phone = isset($this->phone) ? htmlspecialchars(strip_tags($this->phone)) : '';
        $role = isset($this->role) ? $this->role : 'user';
        $id = (int) $this->id;
        $sql = 'UPDATE ' . $this->table . ' SET
        username = :username,
        email = :email,
        full_name = :full_name,
        phone = :phone,
        role = :role
        WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':full_name', $full_name, PDO::PARAM_STR);
        $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;

    }
    public function delete($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
    public static function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;

    }
    public static function isAdmin()
    {
        if (session_status() === PHP_SESSION_NONE)
            session_start();
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

    }
    public static function getCurrentUser()
    {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'email' => $_SESSION['email'] ?? null,
                'role' => $_SESSION['role'] ?? null,
                'full_name' => $_SESSION['full_name'] ?? null,
            ];
        }
        return null;
    }
    public function updateProfile($user_id)
{
    $sql = "UPDATE {$this->table} SET full_name = :full_name, phone = :phone WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':full_name', $this->full_name ?? '');
    $stmt->bindValue(':phone', $this->phone ?? '');
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}

public function changePassword($user_id, $current_password, $new_password)
{
    // First verify current password
    $sql = "SELECT password FROM {$this->table} WHERE id = :id";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($current_password, $user['password'])) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE {$this->table} SET password = :password WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':password', $hashed_password);
        $stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    return false;
}
}


