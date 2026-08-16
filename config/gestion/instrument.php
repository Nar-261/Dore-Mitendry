<?php
class instrument
{  
    private PDO $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {   
        $sql = 'SELECT * FROM instruments';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id):?array
    {
        $sql = 'SELECT * FROM instruments WHERE id=?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    }

    public function create($nom, $image, $description):bool
    {
        $sql = 'INSERT INTO instruments (nom, image, description) VALUES (:nom, :image, :description)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=> $nom,
            ':image'=> $image,
            ':description'=> $description,
        ]);
    }

    public function delete($id):bool
    {
        $sql = 'DELETE FROM instruments WHERE id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function update($id, $nom, $image, $description): bool
    {
        $sql = 'UPDATE instruments SET nom = :nom, image = :image, description = :description WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':nom' => $nom,
            ':image' => $image,
            ':description' => $description,
        ]);
    }

}
?>
