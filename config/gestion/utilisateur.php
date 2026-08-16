<?php
class utilisateur
{  
    private PDO $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {   
        $sql = 'SELECT * FROM utilisateurs';
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id):?array
    {
        $sql = 'SELECT * FROM utilisateurs WHERE id=?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    }

    public function create($nom, $prenom, $email, $tel, $mdp):bool
    {
        $sql = 'INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe) VALUES (:nom,:prenom,:email,:telephone,:mot_de_passe)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=> $nom,
            ':prenom'=> $prenom,
            ':email'=> $email,
            ':telephone'=> $tel,
            ':mot_de_passe'=> $mdp,
        ]);
    }

    public function delete($id):bool
    {
        $sql = 'DELETE FROM utilisateurs WHERE id=?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

}
?>