<?php

namespace App\Models;

// Classe mère de tous les Models
// On centralise ici toutes les propriétés et méthodes utiles pour TOUS les Models

// CoreModel ne doit pas pouvoir être instanciée directement (on va juste s'en servir pour l'héritage)
// ça tombe bien, si on dit qu'une classe est abstraite avec le mot-clé `abstract`, on ne pourra pas l'instancier !
abstract class CoreModel
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $created_at;
    /**
     * @var string
     */
    protected $updated_at;


    // Méthodes abstraites
    abstract static public function find($id);
    abstract static public function findAll();
    abstract public function insert();
    abstract public function update();
    abstract public function delete();

    /**
     * Sauvegarer le modèle courant (soit en le mettant à jour s'il existe, en créant un enregistrement en base sinon)
     *
     * @return void
     */
    public function save()
    {
        // si le modèle courant a un id supérieur à 0, c'est qu'il a déjà été enregistré en base
        if ($this->getId() > 0) {
            // donc on veut faire un update
            return $this->update();
        } else {
            // sinon, c'est que le modèle n'a jamais été enregistré, donc on veut le créer en base
            return $this->insert();
        }
    }


    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of created_at
     *
     * @return  string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * Get the value of updated_at
     *
     * @return  string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }
}
