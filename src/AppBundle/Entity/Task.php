<?php
namespace AppBundle\Entity;

class Task
{
    protected $mesto;
  
    public function getMesto()
    {
        return $this->mesto;
    }

    public function setMesto($mesto)
    {
        $this->mesto = $mesto;
    }
    
    public function __toString() {
      return $this->getMesto();
}

}