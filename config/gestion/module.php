<?php

class module
{  
    private PDO $db;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * recupere le nombre de module
     */
    public function count()
    {
        $sql = 'SELECT COUNT(*) FROM modules';
        return (int)$this->db->query($sql)->fetchColumn();
    }

    /**
     * Récupère tous les modules avec le titre du cours correspondant.
     */
    public function getAll(): array
    {   
        $sql = 'SELECT m.*, c.titre AS cours_titre 
                FROM modules m 
                LEFT JOIN cours c ON c.id = m.cours_id 
                ORDER BY m.cours_id ASC, m.ordre ASC, m.id DESC';
                
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un module simple par son ID.
     */
    public function getById(int $id): ?array
    {
        $sql = 'SELECT m.*, c.titre AS cours_titre 
                FROM modules m 
                LEFT JOIN cours c ON c.id = m.cours_id 
                WHERE m.id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Récupère le module complet avec toutes ses sections et ses images (pour l'affichage public).
     */
    public function getFullModule(int $id): ?array
    {
        $module = $this->getById($id);
        if (!$module) {
            return null;
        }

        // Récupération des sections
        $sqlSections = 'SELECT * FROM module_sections WHERE module_id = ? ORDER BY ordre ASC';
        $stmtSections = $this->db->prepare($sqlSections);
        $stmtSections->execute([$id]);
        $sections = $stmtSections->fetchAll(PDO::FETCH_ASSOC);

        // Récupération des images pour chaque section
        foreach ($sections as &$section) {
            $sqlImages = 'SELECT * FROM section_images WHERE section_id = ? ORDER BY ordre ASC';
            $stmtImages = $this->db->prepare($sqlImages);
            $stmtImages->execute([$section['id']]);
            $section['images'] = $stmtImages->fetchAll(PDO::FETCH_ASSOC);
        }

        $module['sections'] = $sections;
        return $module;
    }

    /**
     * Crée un nouveau module.
     */
    public function create(
        int $cours_id, 
        string $titre, 
        string $description = '', 
        int $ordre = 1, 
        ?string $image_hero = null
    ): bool {
        $sql = 'INSERT INTO modules (cours_id, titre, description, ordre, image_hero) 
                VALUES (:cours_id, :titre, :description, :ordre, :image_hero)';
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':cours_id'   => $cours_id,
            ':titre'      => $titre,
            ':description'=> $description,
            ':ordre'      => $ordre,
            ':image_hero' => $image_hero,
        ]);
    }

    /**
     * Met à jour un module existant.
     */
    public function update(
        int $id, 
        int $cours_id, 
        string $titre, 
        string $description = '', 
        int $ordre = 1, 
        ?string $image_hero = null
    ): bool {
        $sql = 'UPDATE modules 
                SET cours_id = :cours_id, 
                    titre = :titre, 
                    description = :description, 
                    ordre = :ordre, 
                    image_hero = :image_hero 
                WHERE id = :id';
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'         => $id,
            ':cours_id'   => $cours_id,
            ':titre'      => $titre,
            ':description'=> $description,
            ':ordre'      => $ordre,
            ':image_hero' => $image_hero,
        ]);
    }

    /**
     * Supprime un module par son ID.
     */
    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM modules WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}

/**
 * CRUD des section
 */
class section
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Récupérer toutes les sections d'un module avec leurs images
    public function getByModuleId(int $moduleId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM module_sections WHERE module_id = ? ORDER BY ordre ASC');
        $stmt->execute([$moduleId]);
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($sections as &$sec) {
            $stmtImg = $this->db->prepare('SELECT * FROM section_images WHERE section_id = ? ORDER BY ordre ASC');
            $stmtImg->execute([$sec['id']]);
            $sec['images'] = $stmtImg->fetchAll(PDO::FETCH_ASSOC);
        }

        return $sections;
    }

    // Ajouter une section
    public function create(int $moduleId, string $titre, string $contenu, int $ordre = 1): int
    {
        $sql = 'INSERT INTO module_sections (module_id, titre, contenu, ordre) VALUES (:module_id, :titre, :contenu, :ordre)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':module_id' => $moduleId,
            ':titre'     => $titre,
            ':contenu'   => $contenu,
            ':ordre'     => $ordre
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Supprimer une section (les images associées sont supprimées automatiquement grâce à ON DELETE CASCADE)
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM module_sections WHERE id = ?');
        return $stmt->execute([$id]);
    }

    // Ajouter une image à une section
    public function addImage(int $sectionId, string $urlImage, string $alt = '', string $css = 'shadow', int $ordre = 1): bool
    {
        $sql = 'INSERT INTO section_images (section_id, url_image, texte_alternatif, classe_css, ordre) 
                VALUES (:section_id, :url_image, :alt, :css, :ordre)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':section_id' => $sectionId,
            ':url_image'  => $urlImage,
            ':alt'        => $alt,
            ':css'        => $css,
            ':ordre'      => $ordre
        ]);
    }

    // Supprimer une image
    public function deleteImage(int $imageId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM section_images WHERE id = ?');
        return $stmt->execute([$imageId]);
    }
}