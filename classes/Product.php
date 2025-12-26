<?php

class Product
{
    private $db;
    private $table = "products";


    public $id;
    public $name;
    public $description;
    public $price;
    public $old_price;
    public $category_id;
    public $primary_image_id;
    public $stock;
    public $featured;
    public $discount_percent;
    public $created_at;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Create a new product
    public function create()
    {
        // Implement with prepared statements
        if (empty($this->name) || empty($this->description) || empty($this->price) || empty($this->old_price) || empty($this->category_id) || empty($this->primary_image_id) || empty($this->stock)) {
            $name = htmlspecialchars(strip_tags($this->name));
            $description = htmlspecialchars(strip_tags($this->description ?? ''));
            $price = (float) $this->price;
            $old_price = isset($this->old_price) ? (float) $this->old_price : null;
            $category_id = (int) $this->category_id;
            $primary_image_id = isset($this->primary_image_id) ? (int) $this->primary_image_id : null;
            $stock = (int) $this->stock;
            $featured = isset($this->featured) ? (int) $this->featured : 0;
            $discount_precent = isset($this->discount_precent) ? (float) $this->discount_precent : 0;
            $sql = "INSERT INTO" . $this->table . "
            (name, description, price, old_price, category_id, primary_image_id, stock, featured, discount_percent) 
                VALUES (:name, :description, :price, :old_price, :category_id, :primary_image_id, :stock, :featured, :discount_percent)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":name", $name, PDO::PARAM_STR);
            $stmt->bindValue(":description", $description, PDO::PARAM_STR);
            $stmt->bindValue(":price", $price, PDO::PARAM_STR);
            $stmt->bindValue(':old_price', $old_price, $old_price !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->bindValue(':primary_image_id', $primary_image_id, $primary_image_id !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
            $stmt->bindValue(':stock', $stock, PDO::PARAM_INT);
            $stmt->bindValue(':featured', $featured, PDO::PARAM_INT);
            $stmt->bindValue(':discount_precent', $discount_precent, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $this->id = $this->db->lastInsertId();
                return true;
            }
            return false;
        }
    }

    // Read all products with optional filtering and pagination
    public function readAll($limit = 10, $offset = 0, $search = '')
    {
        // Implement with prepared statements
        // Return array of products
    }

    // Read a single product by ID
    public function readOne($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = "SELECT * FROM products WHERE id=:id";
        // Implement with prepared statements
        // Return product object or false
    }

    // Update a product
    public function update()
    {
        // Implement with prepared statements
    }

    // Delete a product
    public function delete($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;

        }
        return false;
    }



    // Handle file uploads for product images
    public function uploadImage($file)
    {
        // Implement secure file upload
        // Return filename or false
    }
}
