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

    public function create($nom, $prenom, $email, $tel, $mdp, $photo = null, $role = 'apprenant'):bool
    {
        $sql = 'INSERT INTO utilisateurs (nom, prenom, email, telephone, photo, mot_de_passe, role) VALUES (:nom,:prenom,:email,:telephone,:photo,:mot_de_passe,:role)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'=> $nom,
            ':prenom'=> $prenom,
            ':email'=> $email,
            ':telephone'=> $tel,
            ':photo'=> $photo,
            ':mot_de_passe'=> $mdp,
            ':role'=> $role,
        ]);
    }

    public function update($id, $nom, $prenom, $email, $tel, $photo = null, $role = 'apprenant'):bool
    {
        $sql = 'UPDATE utilisateurs SET nom=:nom, prenom=:prenom, email=:email, telephone=:telephone, photo=:photo, role=:role WHERE id=:id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':id'=> $id,
            ':nom'=> $nom,
            ':prenom'=> $prenom,
            ':email'=> $email,
            ':telephone'=> $tel,
            ':photo'=> $photo,
            ':role'=> $role,
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