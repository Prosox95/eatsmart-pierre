<?php
header("Access-Control-Allow-Origin: *");
require_once "controllers/ArticleController.php";
require_once "controllers/CommandeController.php";
require_once "controllers/CategorieController.php";

$articleController = new ArticleController();
$commandeController = new CommandeController();
$categorieController = new CategorieController();

// Vérifie si le paramètre "page" est vide ou non présent dans l'URL
if (empty($_GET["page"])) {
    // Si le paramètre est vide, on affiche un message d'erreur
    echo "La page n'existe pas";
} else {
    // Sinon, on récupère la valeur du paramètre "page"
    
    // On découpe cette chaîne en segments, en séparant sur le caractère "/"
    // Cela donne un tableau, ex : ["articles", "3"]
    $url = explode("/", $_GET['page']);
    $method = $_SERVER['REQUEST_METHOD'];
    
    // On teste le premier segment pour déterminer la ressource demandée
    switch($url[0]) {
        case "articles" :
            switch($method) {
                case "GET":
                    // Si un second segment est présent (ex: un ID), on l’utilise
                    if (isset($url[1])) {
                        if (isset($url[2]) && $url[2] === "commandes" && isset($url[1])) {
                            // Exemple : /articles/3/commandes → affiche tous les commandes de l'article 3
                            $articleController->getAllCommandesByArticle($url[1]);
                        }
                        else {
                            // Exemple : /articles/3 → affiche les infos du article 3
                            $articleController->getArticleById($url[1]);
                        }
                    } 
                    else {
                        // Sinon, on affiche tous les articles
                        $articleController->getAllArticles();
                    }
                    break;
                case "POST":
                    $data = json_decode(file_get_contents('php://input'), true);
                    $articleController->createArticle($data);
                    break;
                case "PUT":
                    if (isset($url[1])) {
                        $data = json_decode(file_get_contents('php://input'), true);
                        $articleController->updateArticle($url[1], $data);
                        echo json_encode($data);
                    } else {
                        echo "L'ID de l'article est requis pour la mise à jour.";
                    }
                    break;
                case "DELETE":
                    if (isset($url[1])) {
                        $articleController->deleteArticle($url[1]);
                    } else {
                        echo "L'ID de l'article est requis pour la suppression.";
                    }
                    break;
            }
            break;
            
        case "categories" : 
            switch ($method) {

                // Gérer les requêtes GET pour les categories
                case "GET":
                    // Si un second segment est présent (ex: un ID), on l’utilise
                    if (isset($url[1])) {
                        if (isset($url[2]) && $url[2] === "articles" && isset($url[1])) {
                            // Exemple : /categories/3/articles → affiche tous les articles de la categorie 3
                            $categorieController->getAllArticlesByCategorie($url[1]);
                        }
                        else {
                            // Exemple : /categories/3 → affiche les infos du categorie 3
                            $categorieController->getCategorieById($url[1]);
                        }
                    } 
                    else {
                        // Sinon, on affiche tous les categories
                        $categorieController->getAllCategories();
                    }
                    break;

                // Gérer les requêtes POST pour les categories
                case "POST":
                    $data = json_decode(file_get_contents('php://input'), true);
                    $categorieController->createCategorie($data);
                    break;

                // Gérer les requêtes PUT pour les categories
                case "PUT":
                    if (isset($url[1])) {
                        $data = json_decode(file_get_contents('php://input'), true);
                        $categorieController->updateCategorie($url[1], $data);
                        echo json_encode($data);
                    } else {
                        echo "L'ID de la categorie est requis pour la mise à jour.";
                    }
                    break;

                // Gérer les requêtes DELETE pour les categories
                case "DELETE":
                    if (isset($url[1])) {
                        $categorieController->deleteCategorie($url[1]);
                    } else {
                        echo "L'ID de la categorie est requis pour la suppression.";
                    }
                    break;
            }
            break;
        case "commandes" :
            switch ($method) {
                // Gérer les requêtes GET pour les commandes
                case "GET":
                    // Si un second segment est présent (ex: un ID), on l’utilise
                    if (isset($url[1])) {
                        if (isset($url[2]) && $url[2] === "articles" && isset($url[1])) {
                            // Exemple : /commandes/3/articles → affiche les details de la commandes 3
                            $commandeController->getArticlesByCommandes($url[1]);
                        }
                        else {
                            // Exemple : /commandes/3 → affiche les infos du commandes 3
                            $commandeController->getCommandeById($url[1]);
                        }
                    } 
                    else {
                        // Sinon, on affiche tous les commandess
                        $commandeController->getAllCommandes();
                    }
                    break;
                case "POST":
                    // Gérer les requêtes POST pour les commandes
                    $data = json_decode(file_get_contents('php://input'), true);
                    $commandeController->createCommandes($data);
                    break;
                case "PUT":
                    // Gérer les requêtes PUT pour les commandes
                    break;
                case "DELETE":
                    // Gérer les requêtes DELETE pour les commandes
                    break;
            }
            break;


        // Si la ressource n'existe pas, on renvoie un message d’erreur
        default :
            echo "La page n'existe pas";
    }
}