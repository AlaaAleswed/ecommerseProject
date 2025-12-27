<?php
class Category{
private $db;
private $table= "categories";
public $id;
public $name;
public $description;
public $created_at;
 public function __construct(){
    $this->db =Database::getInstance()->getConnection();
 }
 public function create (){

    if(empty($this->name)){
        return false;

 }  

 $name = htmlspecialchars(strip_tags($this->name));
 $description = isset($this->description)?htmlspecialchars(strip_tags($this->description)):'';
 $sql = 'INSERT INTO ' . $this->table . ' (name, description) VALUES (:name, :description)';
 $stmt = $this->db->prepare($sql);
 $stmt->bindValue(':name', $name, PDO::PARAM_STR);
 $stmt->bindValue(':description', $description, PDO::PARAM_STR);
 if($stmt->execute()){ 
    $this->id = $this->db->lastInsertId();
    return true;
 }
 return false;

}
public function readAll($limit = 10, $offset = 0, $search=''){
    $sql = 'SELECT * FROM '. $this->table . ' WHERE 1=1';
    $params = [];
    if(!empty($search)){
        $sql .= ' AND (name LIKE :search OR description LIKE :search)';
        $params[':search'] = '%'. htmlspecialchars(strip_tags($search)) .'%';
    }
    $sql .=' ORDER BY name LIMIT :limit OFFSET :offset';
    $stmt = $this->db->prepare($sql);
    foreach($params as $key => $value){
        $stmt->bindValue($key, $value, PDO::PARAM_STR);}
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
public function readOne($id){
    if(!is_numeric($id)) return false;
    $sql = 'SELECT * FROM ' . $this->table . ' WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function update(){
    if (empty($this->id) || ! is_numeric($this->id)) return false;
    $name = htmlspecialchars(strip_tags($this->name));
    $description = isset($this->description) ? htmlspecialchars(strip_tags($this->description)) :'';   
    $id =(int) $this->id;
    $sql = 'UPDATE '. $this->table . ' SET
    name = :name,
    description = :description
    WHERE id = :id
    ';
$stmt = $this->db->prepare($sql);
$stmt->bindValue(':name', $name, PDO::PARAM_STR);
$stmt->bindValue(':description', $description, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);
if($stmt->execute()){  
    return $stmt->rowCount()>0;
}
return false;
}
public function delete($id){
    if(!is_numeric($id)) return false;
    $sql = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    if( $stmt->execute()){
        return $stmt->rowCount()> 0;}
        return false;
    }
    public function getTotalCount($search = ''){
        $sql = 'SELECT COUNT(*) as total FROM '. $this->table . ' WHERE 1=1';
        $params = [];
        if(!empty($search)){
            $sql .= ' AND (name LIKE :search OR description LIKE :search)';
            $params[':search'] = '%' . htmlspecialchars(strip_tags($search)) . '%';}
            $stmt = $this->db->prepare($sql);
            foreach($params as $key => $value){
                $stmt->bindValue($key, $value, PDO::PARAM_STR);}
                $stmt->execute();
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return (int)$result['total'];

        }
        public function getAll() {
            $sql = 'SELECT id, name FROM ' . $this->table . ' ORDER BY name';
            $stmt = $this->db->prepare($sql);
             $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);}
}