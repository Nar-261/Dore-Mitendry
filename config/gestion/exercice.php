<?php
class exercice
{  
    private PDO $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {   
        $sql = 'SELECT * FROM exercices';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id):?array
    {
        $sql = 'SELECT * FROM exercices WHERE id=?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    }

    public function create($lecon_id, $question, $correction):bool
    {
        $sql = 'INSERT INTO exercices (lecon_id, question, correction) VALUES (:lecon_id, :question, :correction)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':lecon_id'=> $lecon_id,
            ':question'=> $question,
            ':correction'=> $correction,
        ]);
    }

    public function delete($id):bool
    {
        $sql = 'DELETE FROM exercices WHERE id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function update($id, $lecon_id, $question, $correction): bool
    {
        $sql = 'UPDATE exercices SET lecon_id = :lecon_id, question = :question, correction = :correction WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':lecon_id' => $lecon_id,
            ':question' => $question,
            ':correction' => $correction,
        ]);
    }

}
?>
