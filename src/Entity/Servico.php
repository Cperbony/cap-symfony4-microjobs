<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServicoRepository")
 */
class Servico
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Campo título não pode ser branco")
     * @Assert\Length(
     *     min="40",
     *     minMessage="o Campo título de ter no Mínimo de {{ limit }} caracteres",
     *     max="255",
     *     maxMessage="o Campo título de ter no Máximo de {{ limit }} caracterers"
     * )
     */
    private $titulo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"titulo"}, updatable=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="decimal", precision=2)
     */
    private $valor;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Campo descrição não pode ser branco")
     */
    private $descricao;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $informacoes_adicionais;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Campo Prazo de Entrega não pode ser branco")
     */
    private $prazo_entrega;

    /**
     * @ORM\Column(type="string", length=1,
     *     options={
     *     "comment": "Usar P para publicado, A para Em Análise, I para Inativo, E para Excluído e R para Rejeitado"})
     * @Assert\Choice(choices={"P", "A", "I", "E", "R"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Gedmo\Timestampable(on="create")
     */
    private $data_cadastro;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $data_alteracao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="Selecione uma imagem para o job")
     * @Assert\Image(
     *     mimeTypes={"image/*"},
     *     mimeTypesMessage="Tipo de Arquivo Inválido",
     *     maxHeight="1000",
     *     maxHeightMessage="Máximo de 1000px de altura",
     *     minHeight="400",
     *     minHeightMessage="Mínimo de 400px de altura",
     *     maxWidth="1000",
     *     maxWidthMessage="Máximo de 1000px de largura",
     *     minWidth="400",
     *     minWidthMessage="Mínimo 400px de largura"
     * )
     */
    private $imagem;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Categoria", mappedBy="servicos")
     */
    private $categorias;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Usuario", inversedBy="servicos")
     */
    private $usuario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Contratacoes", mappedBy="servico")
     */
    private $contratacoes;

    public function __construct()
    {
        $this->categorias = new ArrayCollection();
        $this->contratacoes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getValor()
    {
        return $this->valor;
    }

    public function setValor($valor): self
    {
        $this->valor = $valor;

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

    public function getInformacoesAdicionais(): ?string
    {
        return $this->informacoes_adicionais;
    }

    public function setInformacoesAdicionais(?string $informacoes_adicionais): self
    {
        $this->informacoes_adicionais = $informacoes_adicionais;

        return $this;
    }

    public function getPrazoEntrega(): ?int
    {
        return $this->prazo_entrega;
    }

    public function setPrazoEntrega(int $prazo_entrega): self
    {
        $this->prazo_entrega = $prazo_entrega;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus($status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDataCadastro(): ?\DateTimeImmutable
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(\DateTimeImmutable $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;

        return $this;
    }

    public function getDataAlteracao(): ?\DateTimeImmutable
    {
        return $this->data_alteracao;
    }

    public function setDataAlteracao(?\DateTimeImmutable $data_alteracao): self
    {
        $this->data_alteracao = $data_alteracao;

        return $this;
    }

    public function getImagem()
    {
        return $this->imagem;
    }

    public function setImagem($imagem): self
    {
        $this->imagem = $imagem;

        return $this;
    }

    /**
     * @return Collection|Categoria[]
     */
    public function getCategorias(): Collection
    {
        return $this->categorias;
    }

    public function addCategoria(Categoria $categoria): self
    {
        if (!$this->categorias->contains($categoria)) {
            $this->categorias[] = $categoria;
            $categoria->addServico($this);
        }

        return $this;
    }

    public function removeCategoria(Categoria $categoria): self
    {
        if ($this->categorias->contains($categoria)) {
            $this->categorias->removeElement($categoria);
            $categoria->removeServico($this);
        }

        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * @return Collection|Contratacoes[]
     */
    public function getContratacoes(): Collection
    {
        return $this->contratacoes;
    }

    public function addContrataco(Contratacoes $contrataco): self
    {
        if (!$this->contratacoes->contains($contrataco)) {
            $this->contratacoes[] = $contrataco;
            $contrataco->setServico($this);
        }

        return $this;
    }

    public function removeContrataco(Contratacoes $contrataco): self
    {
        if ($this->contratacoes->contains($contrataco)) {
            $this->contratacoes->removeElement($contrataco);
            // set the owning side to null (unless already changed)
            if ($contrataco->getServico() === $this) {
                $contrataco->setServico(null);
            }
        }

        return $this;
    }

}
