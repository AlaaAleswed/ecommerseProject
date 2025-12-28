<?php
require_once __DIR__ . '/Database.php';
class Cart
{
    private $db;
    private $table = 'cart';
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create()
    {
        if (!is_numeric($this->user_id) || !is_numeric($this->product_id) || !is_numeric($this->quantity)) {
            return false;
        }

        ;
        $check = $this->db->prepare('SELECT id, quantity FROM ' . $this->table . '
        WHERE user_id = ? AND product_id = ?');
        $check->execute([$this->user_id, $this->product_id]);
        $existing = $check->fetch(PDO::FETCH_ASSOC);
        if ($existing) {
            $new_quantity = $existing['quantity'] + ($this->quantity ?? 1);
            return $this->updateCartItem($existing['id'], $new_quantity);
        }
        $user_id = (int) $this->user_id;
        $product_id = (int) $this->product_id;
        $quantity = isset($this->quantity) ? (int) $this->quantity : 1;
        $sql = 'INSERT INTO ' . $this->table . ' (user_id, product_id, quantity)
        VALUES (:user_id, :product_id, :quantity)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }
    private function updateCartItem($cart_id, $quantity)
    {
        if ($quantity <= 0) {
            return $this->delete($cart_id);
        }
        $sql = 'UPDATE ' . $this->table . ' SET quantity = :quantity WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $cart_id, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', (int) $quantity, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function readAll($user_id, $limit = 20, $offset = 0)
    {
        if (!is_numeric($user_id) || !is_numeric($limit) || !is_numeric($offset)) {
            return false;
        }
        $sql = 'SELECT c.*, p.name, p.price, p.discount_percent,
    (SELECT filename FROM product_images WHERE product_id = p.id LIMIT 1) AS image
    FROM ' . $this->table . ' c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = :user_id
    ORDER BY c.id DESC
    LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function readOne($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        if (empty($this->id) || !is_numeric($this->id)) {
            return false;
        }
        $quantity = isset($this->quantity) ? $this->quantity : 1;
        $id = (int) $this->id;
        if ($quantity <= 0) {
            return $this->delete($id);
        }
        $sql = 'UPDATE ' . $this->table . ' SET quantity = :quantity WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
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
    public function clear($user_id)
    {
        if (!is_numeric($user_id))
            return false;
        $sql = 'DELETE FROM ' . $this->table . ' WHERE user_id = :user_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    public function isInCart($user_id, $product_id)
    {
        if (!is_numeric($user_id) || !is_numeric($product_id))
            return false;
        $sql = 'SELECT id FROM ' . $this->table . '
    WHERE user_id = :user_id AND product_id = :product_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    public function updateQuantity($cart_id, $quantity)
    {
        if (!is_numeric($cart_id) || !is_numeric($quantity)) {
            return false;
        }
        if ($quantity <= 0) {
            return $this->delete($cart_id);
        }
        $sql = 'UPDATE ' . $this->table . ' SET quantity = :quantity WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $cart_id, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', (int) $quantity, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }

}