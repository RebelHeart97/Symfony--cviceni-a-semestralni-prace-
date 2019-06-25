<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * majitel
 *
 * @ORM\Table(name="majitel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\majitelRepository")
 */
class majitel
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
     * 
     *
     * @ORM\Column(name="login", type="string", length=255)

     */
    private $login;


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
     * @return majitel
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
     * @return majitel
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
     * Set login.
     *
     * @param string $login
     *
     * @return majitel
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login.
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }
 
     public function __toString()
    {
        return $this->login;
    }
}
