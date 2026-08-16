<?php
class lecon
{  
    private PDO $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {   
        $sql = 'SELECT * FROM lecons';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id):?array
    {
        $sql = 'SELECT * FROM lecons WHERE id=?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    }

    public function create($module_id, $titre, $contenu):bool
    {
        $sql = 'INSERT INTO lecons (module_id, titre, contenu) VALUES (:module_id, :titre, :contenu)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':module_id'=> $module_id,
            ':titre'=> $titre,
            ':contenu'=> $contenu,
        ]);
    }

    public function update($id, $module_id, $titre, $contenu):bool
    {
        $sql = 'UPDATE lecons SET module_id=:module_id, titre=:titre, contenu=:contenu WHERE id=:id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'=> $id,
            ':module_id'=> $module_id,
            ':titre'=> $titre,
            ':contenu'=> $contenu,
        ]);
    }

    public function delete($id):bool
    {
        $sql = 'DELETE FROM lecons WHERE id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

}
?>
