<?php

class CommandeModel
/**
 * Classe CommandeModel
 * 
 * Ce modèle s'occupe de récupérer les données sur les commandes.
 * Il interagit avec le base de données pour récupérer les données
 * et retourne les résultats au controller.
 */
{
    private $pdo;

    public function __construct()
    /**
     * Constructeur de la classe CommandeModel.
     * Initialise une connexion à la base de données en utilisant PDO.
     * En cas d'échec, une erreur est affichée et le script s'arrête.
    */
    {
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=eatsmart_bdd_pierre;charset=utf8", "root", "");
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public function getDBAllCommandes()
        /**
    * Récupère toutes les commandes de la base de données.
    */
    {
        $stmt = $this->pdo->query("SELECT * FROM commande");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDBCommandeById($id)
    /*
     * Récupère la commande {id} de la base de données.
     */
    {
        $sql = "SELECT * FROM commande WHERE id_commande = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDBArticlesByCommandes($id)
    /**
     * Récupère les détails d'une commande spécifique de la base de données.
     */
    {
        $sql = "SELECT ac.id_commande, ac.quantite_article, a.*
                FROM assoc_article_commande ac
                JOIN article a ON a.id_article=ac.id_article
                WHERE ac.id_commande = :id"
        ;
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDBCommandes($data) {
        try {
            // Début de la transaction
            $this->pdo->beginTransaction();

            // 1. Insertion de la commande
            $sqlCommande = "INSERT INTO commande (id_commande, date_commande, prix_total, etat)
                            VALUES (:id_commande, :date_commande, :prix_total, :etat)";
            $stmt = $this->pdo->prepare($sqlCommande);
            $stmt->bindParam(':id_commande', $data['id_commande'], PDO::PARAM_INT);
            $stmt->bindParam(':date_commande', $data['date_commande'], PDO::PARAM_STR);
            $stmt->bindParam(':prix_total', $data['prix_total'], PDO::PARAM_STR);
            $stmt->bindParam(':etat', $data['etat'], PDO::PARAM_STR);
            $stmt->execute();

            // 2. Insertion des articles liés
            $sqlAssoc = "INSERT INTO assoc_article_commande (id_article, id_commande, quantite_article)
                        VALUES (:id_article, :id_commande, :quantite_article)";
            $stmtAssoc = $this->pdo->prepare($sqlAssoc);

            foreach ($data['articles'] as $article) {
                $stmtAssoc->bindParam(':id_article', $article['id_article'], PDO::PARAM_INT);
                $stmtAssoc->bindParam(':id_commande', $data['id_commande'], PDO::PARAM_INT);
                $stmtAssoc->bindParam(':quantite_article', $article['quantite'], PDO::PARAM_INT);
                $stmtAssoc->execute();
            }

            // 3. Validation
            $this->pdo->commit();

            // 4. Retourne la commande créée
            return $this->getDBCommandeById($data['id_commande']);

        } catch (Exception $e) {
            // Si ça foire, on annule tout
            $this->pdo->rollBack();
            throw $e;
        }
    }
}

// $commande = new CommandeModel();
// print_r($commande->getDBAllCommandes());
