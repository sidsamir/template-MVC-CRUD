<?php

namespace App\Controllers;

use App\Models\AppUser;

class SecurityController extends CoreController
{
    /**
     * Affiche le formulaire de login
     */
    public function showLoginForm()
    {
        $this->show('security/login');
    }

    /**
     * Réceptionne le formulaire de login (et connecte l'user !)
     */
    public function login()
    {
        // première chose à faire dans ce genre de cas, on dump !
        //dd($_POST);

        // on utilise filter_input pour récupérer les données du form (on pourrait aussi le faire à la main avec isset() et $_POST)
        $email = filter_input(INPUT_POST, 'email');
        $password = filter_input(INPUT_POST, 'password');

        // on créé un tableau d'erreurs vide
        $errorList = [];

        // on récupère l'utilisateur correspondant à l'email soumis dans le form
        $user = AppUser::findByEmail($email);

        // findByEmail retourne false si l'utilisateur n'a pas été trouvé
        // on peut donc tester ça avec le if ci-dessous !
        if($user) {
            // l'adresse email est correcte !

            // on compare le mot de passe du form avec celui stocké en BDD !
            //if($password === $user->getPassword()) {

            // maintenant que nos mdp sont hachés en BDD, on doit utiliser password_verify() pour vérifier si le mdp est correct
            if(password_verify($password, $user->getPassword())) {
                // mot de passe correct !
                // die("OK !");

                // on "connecte" l'utilisateur en ajoutant des données en session
                $_SESSION['userId'] = $user->getId();
                $_SESSION['userObject'] = $user;

                // une fois l'utilisateur connecté, on peut le rediriger vers la page d'accueil
                header("Location: /");
                exit();

            } else {
                // mot de passe incorrect !
                //die("Mot de passe incorrect.");
                //! ATTENTION ! Il faut absolument éviter les messages d'erreurs qui donnent trop d'infos ! (des hackers potentiels pourraient s'en servir !)
                //die("Adresse email ou mot de passe incorrect !");

                // on ajoute le message d'erreur au tableau
                $errorList[] = "Adresse email ou mot de passe incorrect !";
            }

        } else {

            // on ajoute le message d'erreur au tableau
            $errorList[] = "Adresse email ou mot de passe incorrect !";
        }

        // si on arrive à ce stade, c'est qu'il y a eu un problème / une erreur lors de la connexion
        // (sinon on aurait été redirigé par la fonction header())

        $this->show('security/login', [
            'errorList' => $errorList
        ]);
    }

    /**
     * Permet de déconnecter l'utilisateur du site
     */
    public function logout()
    {
        // un utilisateur est connecté quand on a les variables de session userId et userObject qui sont définies
        // donc pour déconnecter un utilisateur, on supprime ces variables de session !

        // on pourrait utiliser session_destroy(), mais ça va TOUT supprimer (donc ça peut poser problème si on stocke d'autres trucs en session)
        //session_destroy();

        // on peut plutôt supprimer simplement les variables de session userId et userObject !
        unset($_SESSION["userId"]);
        unset($_SESSION["userObject"]);

        // souvent, après la déconnexion, on redirige l'user vers le form de login
        header("Location: /login");
        exit();
    }
}
