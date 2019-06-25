<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * majetek
 *
 * @ORM\Table(name="majetek")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\majetekRepository")
 */
class majetek
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
     * @ORM\Column(name="predmet", type="string", length=255)
     */
    private $predmet;

    /**
     * @var int
     *
     * @ORM\Column(name="vyrobeno", type="integer")
     */
    private $vyrobeno;

    /**
     *     
     * @ORM\ManyToOne(targetEntity="majitel", inversedBy="login")
     */
    private $vlastnik;


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
     * Set predmet.
     *
     * @param string $predmet
     *
     * @return majetek
     */
    public function setPredmet($predmet)
    {
        $this->predmet = $predmet;

        return $this;
    }

    /**
     * Get predmet.
     *
     * @return string
     */
    public function getPredmet()
    {
        return $this->predmet;
    }

    /**
     * Set vyrobeno.
     *
     * @param int $vyrobeno
     *
     * @return majetek
     */
    public function setVyrobeno($vyrobeno)
    {
        $this->vyrobeno = $vyrobeno;

        return $this;
    }

    /**
     * Get vyrobeno.
     *
     * @return int
     */
    public function getVyrobeno()
    {
        return $this->vyrobeno;
    }

    /**
     * Set vlastnik.
     *
     * @param string $vlastnik
     *
     * @return majetek
     */
    public function setVlastnik($vlastnik)
    {
        $this->vlastnik = $vlastnik;

        return $this;
    }

    /**
     * Get vlastnik.
     *
     * @return string
     */
    public function getVlastnik()
    {
        return $this->vlastnik;
    }

    public function _toString(){
     return $this->vlastnik;
    }
}
