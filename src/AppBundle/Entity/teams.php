<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * teams
 *
 * @ORM\Table(name="teams")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\teamsRepository")
 */
class teams
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
     * @ORM\Column(name="nazev", type="string", length=255)
     */
    private $nazev;

    /**
     * @var string
     *
     * @ORM\Column(name="liga", type="string", length=255)
     */
    private $liga;

    /**
     * @var string
     *
     * @ORM\Column(name="mesto", type="string", length=255)
     */
    private $mesto;

    /**
     * @var string
     *
     * @ORM\Column(name="stat", type="string", length=255)
     */
    private $stat;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nazev.
     *
     * @param string $nazev
     *
     * @return teams
     */
    public function setNazev($nazev)
    {
        $this->nazev = $nazev;

        return $this;
    }

    /**
     * Get nazev.
     *
     * @return string
     */
    public function getNazev()
    {
        return $this->nazev;
    }

    /**
     * Set liga.
     *
     * @param string $liga
     *
     * @return teams
     */
    public function setLiga($liga)
    {
        $this->liga = $liga;

        return $this;
    }

    /**
     * Get liga.
     *
     * @return string
     */
    public function getLiga()
    {
        return $this->liga;
    }

    /**
     * Set mesto.
     *
     * @param string $mesto
     *
     * @return teams
     */
    public function setMesto($mesto)
    {
        $this->mesto = $mesto;

        return $this;
    }

    /**
     * Get mesto.
     *
     * @return string
     */
    public function getMesto()
    {
        return $this->mesto;
    }

    /**
     * Set stat.
     *
     * @param string $stat
     *
     * @return teams
     */
    public function setStat($stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat.
     *
     * @return string
     */
    public function getStat()
    {
        return $this->stat;
    }  
    
    public function __toString(){
        return $this->nazev;
    }
}
