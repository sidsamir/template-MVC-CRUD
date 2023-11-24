<?php

namespace App\Controllers;

use App\Controllers\CoreController;

class ErrorController extends CoreController
{
    /**
     * Méthode gérant l'affichage de la page 404
     *
     * @return void
     */
    public function err404()
    {
        // On envoie le header 404
        header('HTTP/1.0 404 Not Found');

        // Puis on gère l'affichage
        $this->show('error/err404');
    }

    /**
     * Méthode gérant l'affichage de la page 403
     *
     * @return void
     */
    public function err403()
    {
        // on peut aussi utiliser http_response_code()
        http_response_code(403);

        // puis on affiche la vue
        $this->show('error/err403');
    }
}