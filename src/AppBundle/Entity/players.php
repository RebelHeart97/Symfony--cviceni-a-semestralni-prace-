<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * players
 *
 * @ORM\Table(name="players")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\playersRepository")
 */
class players
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
     * @ORM\Column(name="jmeno", type="string", length=255)
     */
    private $jmeno;

    /**
     * @var string
     *
     * @ORM\Column(name="prijmeni", type="string", length=255)
     */
    private $prijmeni;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dat_nar", type="date")
     */
    private $datNar;

    /**
     * @var string
     *
     * @ORM\Column(name="zeme_puvodu", type="string", length=255)
     */
    private $zemePuvodu;

    /**
     * @var int
     *
     * @ORM\Column(name="vyska", type="integer")
     */
    private $vyska;

    /**
     * @var int
     *
     * @ORM\Column(name="vaha", type="integer")
     */
    private $vaha;

    /**
     * @var string
     *
     * @ORM\Column(name="drzeni_hole", type="string", length=50)
     */
    private $drzeniHole;
    
     /**
     * @var string
     *
     * @ORM\Column(name="pozice", type="string", length=150)
     */
    private $pozice;

    /**
     * 
     * @ORM\ManyToOne(targetEntity="teams", inversedBy="nazev")
     */
    private $tym;

   
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
     * Set jmeno.
     *
     * @param string $jmeno
     *
     * @return players
     */
    public function setJmeno($jmeno)
    {
        $this->jmeno = $jmeno;

        return $this;
    }

    /**
     * Get jmeno.
     *
     * @return string
     */
    public function getJmeno()
    {
        return $this->jmeno;
    }

    /**
     * Set prijmeni.
     *
     * @param string $prijmeni
     *
     * @return players
     */
    public function setPrijmeni($prijmeni)
    {
        $this->prijmeni = $prijmeni;

        return $this;
    }

    /**
     * Get prijmeni.
     *
     * @return string
     */
    public function getPrijmeni()
    {
        return $this->prijmeni;
    }

    /**
     * Set datNar.
     *
     * @param \DateTime $datNar
     *
     * @return players
     */
    public function setDatNar($datNar)
    {
        $this->datNar = $datNar;

        return $this;
    }

    /**
     * Get datNar.
     *
     * @return \DateTime
     */
    public function getDatNar()
    {
        return $this->datNar;
    }

    /**
     * Set zemePuvodu.
     *
     * @param string $zemePuvodu
     *
     * @return players
     */
    public function setZemePuvodu($zemePuvodu)
    {
        $this->zemePuvodu = $zemePuvodu;

        return $this;
    }

    /**
     * Get zemePuvodu.
     *
     * @return string
     */
    public function getZemePuvodu()
    {
        return $this->zemePuvodu;
    }

    /**
     * Set vyska.
     *
     * @param int $vyska
     *
     * @return players
     */
    public function setVyska($vyska)
    {
        $this->vyska = $vyska;

        return $this;
    }

    /**
     * Get vyska.
     *
     * @return int
     */
    public function getVyska()
    {
        return $this->vyska;
    }

    /**
     * Set vaha.
     *
     * @param int $vaha
     *
     * @return players
     */
    public function setVaha($vaha)
    {
        $this->vaha = $vaha;

        return $this;
    }

    /**
     * Get vaha.
     *
     * @return int
     */
    public function getVaha()
    {
        return $this->vaha;
    }

    /**
     * Set drzeniHole.
     *
     * @param string $drzeniHole
     *
     * @return players
     */
    public function setDrzeniHole($drzeniHole)
    {
        $this->drzeniHole = $drzeniHole;

        return $this;
    }

    /**
     * Get drzeniHole.
     *
     * @return string
     */
    public function getDrzeniHole()
    {
        return $this->drzeniHole;
    }
    
    
     /**
     * Set pozice.
     *
     * @param string $pozice
     *
     * @return players
     */
    public function setPozice($pozice)
    {
        $this->pozice = $pozice;

        return $this;
    }

    /**
     * Get pozice.
     *
     * @return string
     */
    public function getPozice()
    {
        return $this->pozice;
    }

    /**
     * Set tym.
     *
     * @param int $tym
     *
     * @return players
     */
    public function setTym($tym)
    {
        $this->tym = $tym;

        return $this;
    }

    /**
     * Get tym.
     *
     * @return int
     */
    public function getTym()
    {
        return $this->tym;
    }
    
    public function _toString(){
      return $this->tym;
    }
}
