<?php

namespace App\Controllers;

use App\Models\AppUser;

class UserController extends CoreController
{
    // R (Read) du CRUD pour les utilisateurs
    public function list()
    {

        // on récupère un tableau d'objets AppUser (notre liste des utilisateurs) grâce à la méthode findAll() du modèle AppUser
        $users = AppUser::findAll();

        $this->show('user/list', [
            'users' => $users
        ]);
    }

    // C (Create) du CRUD pour les utilisateurs (affichage du formulaire uniquement)
    public function add()
    {
        // vu que dans certains cas on renvoie le formulaire pré-rempli avec un objet $user,
        // pour éviter une erreur, on va envoyer un objet $user vide !
        $user = new AppUser();

        $this->show('user/add', [
            'user' => $user
        ]);
    }

    // C (Create) du CRUD pour les utilisateurs (réception du formulaire)
    public function create()
    {

        // Vérification que le formulaire a été soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération des données du formulaire
            $email = filter_input(INPUT_POST, 'email');
            $password = filter_input(INPUT_POST, 'password');
            $firstname = filter_input(INPUT_POST, 'firstname');
            $lastname = filter_input(INPUT_POST, 'lastname');
            $role = filter_input(INPUT_POST, 'role');
            $status = filter_input(INPUT_POST, 'status');

            // Vérification que toutes les données nécessaires sont présentes
            if (!$email || !$password || !$firstname || !$lastname || !$role || !$status) {
                die("Erreur: Tous les champs doivent être remplis.");
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "Adresse e-mail non valide";

            }elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[_\-|%&*=@$]).{8,}$/', $password))
                //    Cette expression régulière vérifie les critères suivants :
                //    Au moins une lettre en minuscule ((?=.*[a-z]))
                //    Au moins une lettre en majuscule ((?=.*[A-Z]))
                //    Au moins un chiffre ((?=.*\d))
                //    Au moins un caractère spécial parmi ['_', '-', '|', '%', '&', '*', '=', '@', '$'] ((?=.*[_\-|%&*=@$]))
                //    Au moins 8 caractères au total (.{8,})
            {
                echo "Mot de passe non valide";
            }


            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


            $user = new AppUser();


            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setFirstName($firstname);
            $user->setLastName($lastname);
            $user->setRole($role);
            $user->setStatus($status);

            if ($user->save()) {

                header('Location: /user/list');
                exit;
            } else {
                // ! utiliser le tableau d'erreur
                die("Erreur rencontrée lors de l'ajout.");
            }
        }


    }
}
