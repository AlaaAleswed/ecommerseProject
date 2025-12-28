<?php

class Review
{
    private $db;
    private $table = "reviews";

    public $id;
    public $product_id;
    public $user_id;
    public $order_id;
    public $order_item_id;
    public $rating;
    public $comment;
    public $approved;
    public $created_at;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create()
    {
        if (
            empty($this->product_id) ||
            empty($this->user_id) ||
            empty($this->rating) ||
            $this->rating < 1 || $this->rating > 5
        ) {
            return false;
        }

        $check = $this->db->prepare(
            "SELECT id FROM {$this->table} WHERE product_id = ? AND user_id = ?"
        );
        $check->execute([$this->product_id, $this->user_id]);
        if ($check->fetch()) {
            return false;
        }

        try {
            $this->db->beginTransaction();

            $sql = "INSERT INTO {$this->table}
                    (product_id, user_id, order_id, order_item_id, rating, comment, approved)
                    VALUES (:product_id, :user_id, :order_id, :order_item_id, :rating, :comment, :approved)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':product_id', (int) $this->product_id, PDO::PARAM_INT);
            $stmt->bindValue(':user_id', (int) $this->user_id, PDO::PARAM_INT);
            $stmt->bindValue(':order_id', $this->order_id ?? null, $this->order_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':order_item_id', $this->order_item_id ?? null, $this->order_item_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':rating', (int) $this->rating, PDO::PARAM_INT);
            $stmt->bindValue(':comment', isset($this->comment) ? htmlspecialchars(strip_tags($this->comment)) : null);
            $stmt->bindValue(':approved', isset($this->approved) ? (int) $this->approved : 1, PDO::PARAM_INT);

            $stmt->execute();

            $this->id = $this->db->lastInsertId();
            $this->db->commit();

            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function readOne($id)
    {
        if (!is_numeric($id))
            return false;

        $sql = "SELECT r.*, u.username, p.name AS product_name
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                JOIN products p ON r.product_id = p.id
                WHERE r.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update()
    {
        if (empty($this->id))
            return false;

        $fields = [];
        $params = [':id' => (int) $this->id];

        if (isset($this->rating)) {
            if ($this->rating < 1 || $this->rating > 5)
                return false;
            $fields[] = "rating = :rating";
            $params[':rating'] = (int) $this->rating;
        }

        if (isset($this->comment)) {
            $fields[] = "comment = :comment";
            $params[':comment'] = htmlspecialchars(strip_tags($this->comment));
        }

        if (isset($this->approved)) {
            $fields[] = "approved = :approved";
            $params[':approved'] = (int) $this->approved;
        }

        if (empty($fields))
            return false;

        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($params);
    }

    public function readOneByProductAndUser($product_id, $user_id, $order_id = null)
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE product_id = :product_id AND user_id = :user_id";
        if ($order_id) {
            $sql .= " AND order_id = :order_id";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        if ($order_id) {
            $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        }
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function delete($id)
    {
        if (!is_numeric($id))
            return false;

        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
        return $stmt->execute([':id' => (int) $id]);
    }
}
