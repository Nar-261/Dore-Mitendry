<?php
class partition
{  
    private PDO $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {   
        $sql = 'SELECT * FROM partitions';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id):?array
    {
        $sql = 'SELECT * FROM partitions WHERE id=?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    }

    public function create($titre, $fichier, $cours_id):bool
    {
        $sql = 'INSERT INTO partitions (titre, fichier, cours_id) VALUES (:titre, :fichier, :cours_id)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre'=> $titre,
            ':fichier'=> $fichier,
            ':cours_id'=> $cours_id,
        ]);
    }

    public function delete($id):bool
    {
        $sql = 'DELETE FROM partitions WHERE id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function update($id, $titre, $fichier, $cours_id): bool
    {
        $sql = 'UPDATE partitions SET titre = :titre, fichier = :fichier, cours_id = :cours_id WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':titre' => $titre,
            ':fichier' => $fichier,
            ':cours_id' => $cours_id,
        ]);
    }

}
?>
