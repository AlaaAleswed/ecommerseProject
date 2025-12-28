<?php
// classes/Address.php
require_once 'Database.php';

class Address
{
    private $db;
    private $table = 'user_addresses';
    
    public $id;
    public $user_id;
    public $address_type;
    public $full_name;
    public $address_line1;
    public $address_line2;
    public $city;
    public $state;
    public $country;
    public $postal_code;
    public $phone;
    public $is_default;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create()
    {
        if (empty($this->user_id) || empty($this->address_line1)) {
            return false;
        }

        // If setting as default, update other addresses
        if ($this->is_default == 1) {
            $this->clearDefaults($this->user_id, $this->address_type);
        }

        $sql = "INSERT INTO {$this->table} 
                (user_id, address_type, full_name, address_line1, address_line2, 
                city, state, country, postal_code, phone, is_default) 
                VALUES (:user_id, :address_type, :full_name, :address_line1, :address_line2, 
                :city, :state, :country, :postal_code, :phone, :is_default)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':address_type', $this->address_type ?? 'home');
        $stmt->bindValue(':full_name', $this->full_name);
        $stmt->bindValue(':address_line1', $this->address_line1);
        $stmt->bindValue(':address_line2', $this->address_line2 ?? '');
        $stmt->bindValue(':city', $this->city);
        $stmt->bindValue(':state', $this->state ?? '');
        $stmt->bindValue(':country', $this->country ?? 'US');
        $stmt->bindValue(':postal_code', $this->postal_code ?? '');
        $stmt->bindValue(':phone', $this->phone ?? '');
        $stmt->bindValue(':is_default', $this->is_default ?? 0, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->id = $this->db->lastInsertId();
            return $this->id;
        }
        return false;
    }

    private function clearDefaults($user_id, $address_type)
    {
        $sql = "UPDATE {$this->table} SET is_default = 0 
                WHERE user_id = :user_id AND address_type = :address_type";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':address_type', $address_type);
        return $stmt->execute();
    }

    public function getUserAddresses($user_id, $type = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
        if ($type) {
            $sql .= " AND address_type = :type";
        }
        $sql .= " ORDER BY is_default DESC, id DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        if ($type) {
            $stmt->bindValue(':type', $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAddress($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDefaultAddress($user_id, $type = 'shipping')
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = :user_id AND address_type = :type AND is_default = 1 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':type', $type);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}