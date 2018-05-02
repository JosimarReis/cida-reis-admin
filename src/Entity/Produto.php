<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProdutoRepository")
 */
class Produto
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $nome;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private $descricao;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     */
    private $preco;

    /**
     * @ORM\Column(type="boolean")
     */
    private $visivel;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categoria", inversedBy="produtos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoria;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Imagem", mappedBy="produto", orphanRemoval=true)
     */
    private $imagens;

    public function __construct()
    {
        $this->imagens = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome(): ?string
    {
        return $this->nome;
    }

    public function setNome(string $nome): self
    {
        $this->nome = $nome;

        return $this;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): self
    {
        $this->descricao = $descricao;

        return $this;
    }

    public function getPreco()
    {
        return $this->preco;
    }

    public function setPreco($preco): self
    {
        $this->preco = $preco;

        return $this;
    }

    public function getVisivel(): ?bool
    {
        return $this->visivel;
    }

    public function setVisivel(bool $visivel): self
    {
        $this->visivel = $visivel;

        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * @return Collection|Imagem[]
     */
    public function getImagens(): Collection
    {
        return $this->imagens;
    }

    public function addImagen(Imagem $imagen): self
    {
        if (!$this->imagens->contains($imagen)) {
            $this->imagens[] = $imagen;
            $imagen->setProduto($this);
        }

        return $this;
    }

    public function removeImagen(Imagem $imagen): self
    {
        if ($this->imagens->contains($imagen)) {
            $this->imagens->removeElement($imagen);
            // set the owning side to null (unless already changed)
            if ($imagen->getProduto() === $this) {
                $imagen->setProduto(null);
            }
        }

        return $this;
    }
}
