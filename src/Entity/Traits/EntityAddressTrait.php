<?php

namespace App\Entity\Traits;

use App\Attribute\QuillMention;
use Doctrine\ORM\Mapping as ORM;

trait EntityAddressTrait
{
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[QuillMention('adresse')]
    protected ?string $adresse = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[QuillMention('adresseComplementaire')]
    protected ?string $adresseComp =null;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    #[QuillMention('codePostal')]
    protected ?string $codePostal = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    #[QuillMention('ville')]
    protected ?string $ville = null;

    public function getFullAdresse(): ?string
    {
        $fullAdresse = null;
        if ($this->adresse) $fullAdresse .= $this->adresse . ' ';
        if ($this->adresseComp) $fullAdresse .= $this->adresseComp . ' ';
        if ($this->codePostal) $fullAdresse .= $this->codePostal . ' ';
        if ($this->ville) $fullAdresse .= $this->ville . ' ';

        return trim($fullAdresse);
    }

    public function __construct(){}

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getAdresseComp(): ?string
    {
        return $this->adresseComp;
    }

    public function setAdresseComp(?string $adresseComp): void
    {
        $this->adresseComp = $adresseComp;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): void
    {
        $this->ville = $ville;
    }
}
