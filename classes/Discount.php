<?php

class Discount {
    private $db;
    private $table = "discounts";

    public $category_id;
    public $discount_value;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create() {

        if (!is_numeric($this->category_id) || empty($this->discount_value)) {
            return false;
        }

        $sql = "UPDATE {$this->table}
                SET is_active = 0
                WHERE category_id = :category_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "INSERT INTO {$this->table}
                (category_id, discount_type, discount_value, is_active)
                VALUES (:category_id, 'percentage', :value, 1)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $this->category_id, PDO::PARAM_INT);
        $stmt->bindValue(':value', $this->discount_value);

        return $stmt->execute();
    }

    public function removeByCategory($categoryId) {
        $sql = "UPDATE {$this->table}
                SET is_active = 0
                WHERE category_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $categoryId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
