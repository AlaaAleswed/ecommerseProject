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
        if (
            empty($this->name) || empty($this->price) || empty($this->category_id)
            || empty($this->stock)
        ) {
            return false;
        }
        $categoryCheck = $this->db->prepare("SELECT id FROM categories WHERE id = ?");
        $categoryCheck->execute([$this->category_id]);
        if(!$categoryCheck->fetch()) {
            return false;}

        $name = htmlspecialchars(strip_tags($this->name));
        $description = htmlspecialchars(strip_tags($this->description ?? ''));
        $price = (float) $this->price;
        $old_price = isset($this->old_price) ? (float) $this->old_price : null;
        $category_id = (int) $this->category_id;
        $primary_image_id = isset($this->primary_image_id) ? (int) $this->primary_image_id : null;
        $stock = (int) $this->stock;
        $featured = isset($this->featured) ? (int) $this->featured : 0;
        $discount_percent = isset($this->discount_percent) ? (int) $this->discount_percent : 0;
        $sql = "INSERT INTO " . $this->table . " 
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
        $stmt->bindValue(':discount_percent', $discount_percent, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return true;
        }
        return false;
    }

    // Read all products with optional filtering and pagination
    public function readAll($limit = 10, $offset = 0, $search = '', $category_id = null, $featured = null)
    {
        $sql = "SELECT * FROM " . $this->table . " WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR description LIKE :search)";
            $params[":search"] = "%" . htmlspecialchars(strip_tags($search)) . "%";

        }
        if ($category_id !== null && is_numeric($category_id)) {
            $sql .= " AND category_id = :category_id";
            $params[":category_id"] = $category_id;
        }
        if ($featured !== null) {
            $sql .= " AND featured = :featured";
            $params[":featured"] = (int) $featured;
        }
        
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = strpos($key, "search") !== false ? PDO::PARAM_STR : PDO::PARAM_INT;
            $stmt->bindValue($key, $value, $paramType);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);



    }
    public function getTotalCount($search = '', $category_id = null, $featured = null)
    {
        $sql = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE 1=1";
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (name LIKE :search OR description LIKE :search)";
        }
        if ($category_id !== null && is_numeric($category_id)) {

            $sql .= " AND category_id = :category_id";
            $params[":category_id"] = $category_id;
        }
        if ($featured !== null) {
            $sql .= " AND featured = :featured";
            $params[":featured"] = (int) $featured;
        }

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = strpos($key, "search") !== false ? PDO::PARAM_STR : PDO::PARAM_INT;
            $stmt->bindValue($key, $value, $paramType);
        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result["total"];
    }

    // Read a single product by ID
    public function readOne($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = "SELECT * FROM products WHERE id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        // Implement with prepared statements
        // Return product object or false
    }

    // Update a product
    public function update()
    {
        if (empty($this->id) || !is_numeric($this->id) || empty($this->price) || empty($this->category_id) || empty($this->stock)) {
            return false;
        }
        $name = htmlspecialchars(strip_tags($this->name));
        $description = htmlspecialchars(strip_tags($this->description ?? ""));
        $price = (float) $this->price;
        $old_price = isset($this->old_price) ? (float) $this->old_price : null;
        $category_id = (int) $this->category_id;
        $primary_image_id = isset($this->primary_image_id) ? (int) $this->primary_image_id : null;
        $stock = (int) $this->stock;
        $featured = isset($this->featured) ? $this->featured : 0;
        $discount_percent = isset($this->discount_percent) ? (int) $this->discount_percent : 0;
        $id = (int) $this->id;
        $sql = "UPDATE " . $this->table . " SET 
        name = :name, 
        description = :description,
        price = :price, 
        old_price = :old_price, 
        category_id = :category_id, 
        primary_image_id = :primary_image_id, 
        stock = :stock, 
        featured = :featured,
        discount_percent = :discount_percent 
        WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        $stmt->bindValue(":description", $description, PDO::PARAM_STR);
        $stmt->bindValue(":price", $price, PDO::PARAM_STR);
        $stmt->bindValue(":old_price", $old_price, $old_price !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(":category_id", $category_id, PDO::PARAM_INT);
        $stmt->bindValue(":primary_image_id", $primary_image_id, $primary_image_id !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(":stock", $stock, PDO::PARAM_INT);
        $stmt->bindValue(":discount_percent", $discount_percent, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }




    // Implement with prepared statements

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
        if (!getimagesize($file['tmp_name'])) {
            return false;
        }

        if (!isset($file["error"]) || !is_array($file)) {
            return false;
        }
        switch ($file["error"]) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                return false;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return false;
            default:
                return false;
        }
        // Implement secure file upload
        if ($file['size'] > 5000000) {
            return false;
        }
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        $allowedMimes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        if (!in_array($mime, $allowedMimes)) {
            return false;
        }
        $extention = array_search($mime, $allowedMimes);
        $filename = uniqid('product_', true) . '.' . $extention;
        $uploadDir = 'assets/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $destination = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return false;
        }
        try {

            $sql = "INSERT INTO product_images (filename, original_name, mime_type, file_size, product_id, uploaded_at) 
        VALUES (:filename, :original_name, :mime_type, :file_size, :product_id, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(":filename", $filename, PDO::PARAM_STR);
            $stmt->bindValue(":original_name", $file["name"], PDO::PARAM_STR);
            $stmt->bindValue(":mime_type", $mime, PDO::PARAM_STR);
            $stmt->bindValue(":file_size", $file['size'], PDO::PARAM_INT);
            if ($stmt->execute()) {
                $imageId = $this->db->lastInsertId();
                $this->primary_image_id = $imageId;
                return $imageId;
            }
        } catch (PDOException $e) {
            return false;
        }
        return false;

        // Return filename or false
    }
    private function setPrimaryImage($imageId)
    {
        $sql = "UPDATE products SET primary_image_id = :image_id WHERE id = :product_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":image_id", $imageId, PDO::PARAM_INT);
        $stmt->bindValue(":product_id", $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function getFeaturedProducts($limit = 5)
    {
        $sql = "SELECT * FROM " . $this->table . " 
        WHERE featured = 1 
        ORDER BY created_at DESC
        LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDiscountedProducts($limit = 5)
    {
        $sql = "SELECT * FROM " . $this->table . "
        WHERE discount_percent > 0
        ORDER BY discount_percent DESC, created_at DESC
        LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateStock($id, $quantity)
    {
        if (!is_numeric($id) || !is_numeric($quantity)) {
            return false;
        }
        $sql = "UPDATE " . $this->table . "
            SET stock = stock + :quantity
            WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":quantity", (int) $quantity, PDO::PARAM_INT);
        $stmt->bindValue(":id", (int) $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
    public function search($keyword, $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM " . $this->table . "
            WHERE name LIKE :keyword OR description LIKE :keyword
            ORDER BY name
            LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":keyword", "%$keyword%", PDO::PARAM_STR);
        $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getByCategory($category_id, $limit = 10, $offset = 0)
    {

        if (!is_numeric($category_id)) {
            return false;
            # code...
        }

        $sql = "SELECT * FROM " . $this->table . "
            WHERE category_id = :category_id
            ORDER BY created_at DESC
            LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":category_id", (int) $category_id, PDO::PARAM_INT);
        $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}