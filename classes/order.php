<?php
class Order
{
    private $db;
    private $table = "orders";
    public $id;
    public $user_id;
    public $address_id;
    public $order_number;
    public $total_amount;
    public $status;
    public $payment_method;
    public $created_at;
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    public function create()
    {
        if (empty($this->user_id) || empty($this->total_amount)) {
            return false;
        }
        $this->order_number = 'ORD' . date('YmHis') . rand(100, 999);
        $user_id = (int) $this->user_id;
        $address_id = isset($this->address_id) ? (int) $this->address_id : null;
        $total_amount = (float) $this->total_amount;
        $status = isset($this->status) ? htmlspecialchars(strip_tags($this->status)) : 'pending';
        $payment_method = isset($this->payment_method) ? htmlspecialchars(strip_tags($this->payment_method)) : null;
        $sql = 'INSERT INTO ' . $this->table . '
        (user_id, address_id, order_number, total_amount, status, payment_method)
        VALUES (:user_id, :address_id, :order_number, :total_amount, :status, :payment_method)';

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':address_id', $address_id, $address_id !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':order_number', $this->order_number, PDO::PARAM_STR);
        $stmt->bindValue(':total_amount', $total_amount, PDO::PARAM_STR);
        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return $this->id;

        }
        return false;
    }
    public function addItem($order_id, $product_id, $quantity, $price)
    {
        if (!is_numeric($order_id) || !is_numeric($product_id) || !is_numeric($quantity) || (!is_numeric($price)))
            return false;
        $sql = 'INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (:order_id, :product_id, :quantity, :price)';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindValue(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindValue(':price', $price, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function readAll($limit = 10, $offset = 0, $search = '', $status = null)
    {
        $sql = 'SELECT o.*, u.username, u.email
FROM ' . $this->table . ' o
        JOIN users u ON o.user_id = u.id 
        WHERE 1=1';
        $params = [];
        if (!empty($search)) {
            $sql .= " AND (o.order_number LIKE :search OR u.username LIKE :search)";
            $params[":search"] = "%" . htmlspecialchars(strip_tags($search)) . "%";

        }
        if ($status !== null) {
            $sql .= " AND o.status = :status";
            $params[":status"] = $status;

        }
        $sql .= " ORDER BY o.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(":offset", (int) $offset, PDO::PARAM_INT);
        $stmt->bindValue(":limit", (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public function readOne($id)
    {
        if (!is_numeric($id))
            return false;
        $sql = "SELECT o.*, u.username, u.email, u.full_name, u.phone
     From " . $this->table . " o
     JOIN users u ON o.user_id = u.id
     WHERE o.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function update()
    {
        if (empty($this->id) || !is_numeric($this->id))
            return false;
        $status = isset($this->status) ? htmlspecialchars(strip_tags($this->status)) : "pending";
        $payment_method = isset($this->payment_method) ? htmlspecialchars(strip_tags($this->payment_method)) : null;
        $id = (int) $this->id;
        $sql = "UPDATE " . $this->table . " SET
        status = :status,
        payment_method = :payment_method
        WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->bindValue(":status", $status, PDO::PARAM_STR);
        $stmt->bindValue(":payment_method", $payment_method, PDO::PARAM_STR);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
    public function delete($id)
    {

        if (!is_numeric($id))
            return false;
        $deleteItems = $this->db->prepare("DELETE FROM order_items WHERE order_id =
        ?");
        $deleteItems->execute([$id]);
        $sql = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        }
        return false;
    }
    public function getTotalCount($search = '', $status = null)
    {
        $sql = 'SELECT COUNT(*) as total FROM ' . $this->table . ' o WHERE 1=1';
        $params = [];
        if (!empty($search)) {
            $sql .= ' AND (o.order_number LIKE :search)';
            $params[':search'] = "%" . htmlspecialchars(strip_tags($search)) . "%";

        }
        if ($status !== null) {
            $sql .= " AND o.status = :status";
            $params[":status"] = $status;


        }
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, PDO::PARAM_STR);

        }
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) $result['total'];



    }
    public function getByUser($user_id, $limit = 10, $offset = 0)
    {
        if (!is_numeric($user_id))
            return false;
        $sql = 'SELECT * FROM ' . $this->table . '
        WHERE user_id = :user_id
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchALL(PDO::FETCH_ASSOC);

    }
    public function getItems($order_id)
    {
        $sql = 'SELECT oi.*, p.name, p.description
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = :order_id';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function canBeReviewed($order_id, $user_id)
    {
        $sql = "SELECT o.status, 
                COUNT(oi.id) as total_items,
                COUNT(r.id) as reviewed_items
                FROM orders o
                JOIN order_items oi ON o.id = oi.order_id
                LEFT JOIN reviews r ON oi.product_id = r.product_id 
                       AND r.user_id = :user_id 
                       AND r.order_id = :order_id
                WHERE o.id = :order_id AND o.user_id = :user_id2
                GROUP BY o.id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':user_id2', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($result && $result['status'] == 'delivered' &&
            $result['total_items'] > $result['reviewed_items']);
    }
    public function getOrderWithAddress($order_id)
    {
        $sql = "SELECT o.*, u.username, u.email, u.full_name, u.phone,
                       ua.*
                FROM orders o
                JOIN users u ON o.user_id = u.id
                LEFT JOIN user_addresses ua ON o.address_id = ua.id
                WHERE o.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}