<?php

namespace App\Controllers;

// CoreController ne doit pas pouvoir être instanciée directement (on va juste s'en servir pour l'héritage)
// ça tombe bien, si on dit qu'une classe est abstraite avec le mot-clé `abstract`, on ne pourra pas l'instancier !
abstract class CoreController
{
    // une propriété $router pour stocker le routeur et pouvoir faire des $router->generate dans tous nos contrôleurs !
    protected $router;

    public function __construct()
    {
        // le constructeur du CoreController sera instancié automatiquement dès qu'on va instancier l'un de nos contrôleurs.
        global $router;
        $this->router = $router;

        // on "globalise" $match, qui contient les infos de la route (notamment son nom !)
        global $match;
        // on récupère le nom de la route demandée par le visiteur
        $routeName = $match["name"];
        // dump($routeName);

        //* Quelle que soit la page/route demandée par le visiteur, il faudra OBLIGATOIREMENT passer par le constructeur du CoreController

        // Tableau d'ACL (Access Control List)
        // il contient la liste des routes dont l'accès est restreint
        // et pour chacune de ces routes, on stocke qui a le droit d'y accéder !
        //* si on veut qu'une route ne soit pas protégée / soit accessible par tout le monde, on ne la met pas dans le tableau d'ACL !
        $acl = [
            // ici, on ajoute toutes nos routes avec les permissions :

            //'main-home' => ['admin', 'catalog-manager'],
            //'user-list' => ['admin'],


        ];

        // le code du vigile à l'entrée qui check les ACL pour voir si on a le droit ou pas de rentrer :
        // 1. le vigile vérifie si la route demandée est dans le tableau $acl
        if(array_key_exists($routeName, $acl)) {
            // si oui, 2. le vigile regarde les rôles qui sont autorisés pour cette route
            $authorizedRoles = $acl[$routeName];
            // 3. le vigile vérifie si l'user a bien le bon rôle !
            $this->checkAuthorization($authorizedRoles);
        } else {
            // si la route demandée n'est pas dans le tableau, c'est qu'elle n'est pas protégée, qu'elle est publique
            // donc RAS ! (le vigile laisse passer)
        }



        // Token CSRF

        // on créé un tableau pour stocker TOUTES les routes en POST (formulaires) qui nécessitent la vérification d'un token CSRF
        $csrfProtectedRoutesPost = [
            'user-create'
        ];

        // un deuxième tableau pour stocker TOUTES les routes en GET qui nécessitent la vérification d'un token CSRF
        $csrfProtectedRoutesGet = [
            'category-delete'
        ];

        // on regarde dans le tableau $csrfProtectedRoutesPost pour voir si la route actuelle nécessite la vérif d'un token
        if(in_array($routeName, $csrfProtectedRoutesPost)) {
            // la route actuelle est dans le tableau, ça veut dire qu'on doit vérifier la présence du token !
            if(isset($_POST['token'])) {
                // le token est bien présent, on le compare avec celui stocké en session !
                if($_POST['token'] !== $_SESSION['token']) {
                    // le token est différent de celui stocké en session, tentative d'exploitation de faille CSRF détecté !
                    // on affiche une erreur 403
                    http_response_code(403);
                    $this->show('error/err403');

                    // juste pour être bien sûr qu'on aille plus loin !
                    exit();
                } else {
                    // le token est correct, donc RAS, on fait rien de spécial !
                }
            } else {
                // le token est manquant
                // on affiche une erreur 403
                http_response_code(403);
                $this->show('error/err403');

                // juste pour être bien sûr qu'on aille plus loin !
                exit();
            }
        }

        // on regarde dans le tableau $csrfProtectedRoutesGet pour voir si la route actuelle nécessite la vérif d'un token
        if(in_array($routeName, $csrfProtectedRoutesGet)) {
            // la route actuelle est dans le tableau, ça veut dire qu'on doit vérifier la présence du token !
            if(isset($_GET['token'])) {
                // le token est bien présent, on le compare avec celui stocké en session !
                if($_GET['token'] !== $_SESSION['token']) {
                    // le token est différent de celui stocké en session, tentative d'exploitation de faille CSRF détecté !
                    // on affiche une erreur 403
                    http_response_code(403);
                    $this->show('error/err403');

                    // juste pour être bien sûr qu'on aille plus loin !
                    exit();
                } else {
                    // le token est correct, donc RAS, on fait rien de spécial !
                }
            } else {
                // le token est manquant
                // on affiche une erreur 403
                http_response_code(403);
                $this->show('error/err403');

                // juste pour être bien sûr qu'on aille plus loin !
                exit();
            }
        }

    }

    /**
     * Méthode permettant d'afficher du code HTML en se basant sur les views
     *
     * @param string $viewName Nom du fichier de vue
     * @param array $viewData Tableau des données à transmettre aux vues
     * @return void
     */
    protected function show(string $viewName, $viewData = [])
    {
        // On globalise $router car on ne sait pas faire mieux pour l'instant
        global $router;

        // Comme $viewData est déclarée comme paramètre de la méthode show()
        // les vues y ont accès
        // ici une valeur dont on a besoin sur TOUTES les vues
        // donc on la définit dans show()
        $viewData['currentPage'] = $viewName;

        // définir l'url absolue pour nos assets
        $viewData['assetsBaseUri'] = $_SERVER['BASE_URI'] . 'assets/';
        // définir l'url absolue pour la racine du site
        // /!\ != racine projet, ici on parle du répertoire public/
        $viewData['baseUri'] = $_SERVER['BASE_URI'];

        // On veut désormais accéder aux données de $viewData, mais sans accéder au tableau
        // La fonction extract permet de créer une variable pour chaque élément du tableau passé en argument
        extract($viewData);
        // => la variable $currentPage existe désormais, et sa valeur est $viewName
        // => la variable $assetsBaseUri existe désormais, et sa valeur est $_SERVER['BASE_URI'] . '/assets/'
        // => la variable $baseUri existe désormais, et sa valeur est $_SERVER['BASE_URI']
        // => il en va de même pour chaque élément du tableau

        // $viewData est disponible dans chaque fichier de vue
        require_once __DIR__ . '/../Views/layout/header.tpl.php';
        require_once __DIR__ . '/../Views/' . $viewName . '.tpl.php';
        require_once __DIR__ . '/../Views/layout/footer.tpl.php';
    }

    /**
     * Cette fonction détermine si l'user a le droit d'accéder à une page ou pas
     *
     * @param string[] $authorizedRoles un tableau contenant tous les rôles autorisés à accéder à une page spécifique (par défaut, tableau vide)
     */
    protected function checkAuthorization($authorizedRoles = [])
    {
        // est-ce que l'utilisateur est connecté ?
        if(isset($_SESSION['userObject'])) {
            // (si $_SESSION['userObject'] est défini, ça veut dire que l'user est connecté)

            // si oui, on récupère l'objet AppUser (depuis la session)
            $user = $_SESSION['userObject'];

            // on récupère dans cet objet le rôle de l'utilisateur
            $role = $user->getRole();

            // on vérifie si le rôle de l'utilisateur est autorisé à accéder à la page en question
            // pour savoir quels rôles sont autorisés sur chaque page, on envoie les rôles autorisés via le paramètre $authorizedRoles
            // est-ce que le rôle de l'user est dans le tableau $authorizedRoles ?
            if(in_array($role, $authorizedRoles)) {
                // si oui, on retourne true
                return true;
            } else {
                // si non, l'utilisateur n'a pas le bon rôle
                // donc on affiche une erreur 403 (avec le HTTP 403)
                // et on arrête le script PHP afin que la page ne s'affiche pas.
                //$controller = new ErrorController();
                //$controller->err403();
                //! avec nos ACL et notre checkAuthorization() dans le __construct du CoreController,
                //! on ne peut plus instancier ErrorController() ici (sinon, on tourne en boucle !)
                // donc on affiche directement l'erreur !
                http_response_code(403);
                $this->show('error/err403');

                // juste pour être bien sûr qu'on aille plus loin !
                exit();
            }
        } else {
            // si l'utilisateur n'est pas connecté
            // on le redirige vers la page de connexion
            header("Location: {$this->router->generate('security-login')}");
            exit();
        }

    }
}

