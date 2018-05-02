<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImagemRepository")
 */
class Imagem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caminho;

    /**
     * @ORM\Column(type="boolean")
     */
    private $principal;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produto", inversedBy="imagens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $produto;

    public function getId()
    {
        return $this->id;
    }

    public function getCaminho(): ?string
    {
        return $this->caminho;
    }

    public function setCaminho(string $caminho): self
    {
        $this->caminho = $caminho;

        return $this;
    }

    public function getPrincipal(): ?bool
    {
        return $this->principal;
    }

    public function setPrincipal(bool $principal): self
    {
        $this->principal = $principal;

        return $this;
    }

    public function getProduto(): ?Produto
    {
        return $this->produto;
    }

    public function setProduto(?Produto $produto): self
    {
        $this->produto = $produto;

        return $this;
    }
}
