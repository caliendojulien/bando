<?php

namespace App\Services;

class ListeSortiesService
{
    private array $liste;

    public function __construct()
    {
        $this->liste = [];
    }

    public  function getListe()
    {
        return $this->liste;
    }

    /**
     * @param mixed $liste
     */
    public  function setListe(array $liste): void
    {
        $this->liste=$liste;
    }


}