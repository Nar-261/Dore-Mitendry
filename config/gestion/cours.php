<?php

class Cours
{  
    private PDO $db;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {   
        $sql = 'SELECT c.id, c.titre, c.description, c.niveau, c.instrument_id, i.nom AS instrument 
                FROM cours c 
                LEFT JOIN instruments i ON i.id = c.instrument_id 
                ORDER BY c.id DESC';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $sql = 'SELECT * FROM cours WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function create(string $titre, string $description, ?int $instrument_id, string $niveau): bool
    {
        $sql = 'INSERT INTO cours (titre, description, instrument_id, niveau) 
                VALUES (:titre, :description, :instrument_id, :niveau)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titre'         => $titre,
            ':description'   => $description,
            ':instrument_id' => $instrument_id,
            ':niveau'        => $niveau,
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM cours WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function update(int $id, string $titre, string $description, ?int $instrument_id, string $niveau): bool
    {
        $sql = 'UPDATE cours 
                SET titre = :titre, 
                    description = :description, 
                    instrument_id = :instrument_id, 
                    niveau = :niveau 
                WHERE id = :id';
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'            => $id,
            ':titre'         => $titre,
            ':description'   => $description,
            ':instrument_id' => $instrument_id,
            ':niveau'        => $niveau,
        ]);
    }
}
?>