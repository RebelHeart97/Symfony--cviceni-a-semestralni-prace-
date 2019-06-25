<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * sklad
 *
 * @ORM\Table(name="sklad")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\skladRepository")
 */
class sklad
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="Nazev", type="string", length=255)
     */
    private $nazev;

    /**
     * @var float
     *
     * @ORM\Column(name="Cena", type="float")
     */
    private $cena;

    /**
     * @var bool
     *
     * @ORM\Column(name="Dostupnost", type="boolean")
     */
    private $dostupnost;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nazev
     *
     * @param string $nazev
     *
     * @return sklad
     */
    public function setNazev($nazev)
    {
        $this->nazev = $nazev;

        return $this;
    }

    /**
     * Get nazev
     *
     * @return string
     */
    public function getNazev()
    {
        return $this->nazev;
    }

    /**
     * Set cena
     *
     * @param float $cena
     *
     * @return sklad
     */
    public function setCena($cena)
    {
        $this->cena = $cena;

        return $this;
    }

    /**
     * Get cena
     *
     * @return float
     */
    public function getCena()
    {
        return $this->cena;
    }

    /**
     * Set dostupnost
     *
     * @param boolean $dostupnost
     *
     * @return sklad
     */
    public function setDostupnost($dostupnost)
    {
        $this->dostupnost = $dostupnost;

        return $this;
    }

    /**
     * Get dostupnost
     *
     * @return bool
     */
    public function getDostupnost()
    {
        return $this->dostupnost;
    }
}

