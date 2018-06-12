<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DadosPessoaisRepository")
 * @Vich\Uploadable
 */
class DadosPessoais
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
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
    private $foto;

    /**
     * @Vich\UploadableField(mapping="perfil_imagem", fileNameProperty="foto")
     * @var File
     */
    private $foto_file;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $curriculo;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $cidade;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     * @Assert\Choice(choices={"AC", "AL", "AM", "AP", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RO", "RS", "RR", "SC", "SE", "SP", "TO"})
     * @Assert\Length(
     *     min="2",
     *     minMessage="O Estado deve ter no mínimo {{ limit}} caracteres",
     *     max="2",
     *     maxMessage="O estado deve ter no máximo {{ limit }} caracteres"
     * )
     */
    private $estado;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $data_nascimento;

    /**
     * @ORM\Column(type="string", length=11, nullable=true)
     */
    private $cpf;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $telefone_ddd;

    /**
     * @ORM\Column(type="string", length=9, nullable=true)
     */
    private $telefone_numero;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $logradouro;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $endereco_numero;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $bairro;

    /**
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    private $cod_moip;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $data_cadastro;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="update")
     */
    private $data_alteracao;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moip_access_token;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moip_id_conta;

    public function getId()
    {
        return $this->id;
    }

    public function getFoto()
    {
        return $this->foto;
    }

    public function setFoto(string $foto)
    {
        $this->foto = $foto;

        return $this;
    }

    public function getCurriculo(): ?string
    {
        return $this->curriculo;
    }

    public function setCurriculo(?string $curriculo): self
    {
        $this->curriculo = $curriculo;

        return $this;
    }

    public function getCidade(): ?string
    {
        return $this->cidade;
    }

    public function setCidade(?string $cidade): self
    {
        $this->cidade = $cidade;

        return $this;
    }

    public function getEstado(): ?string
    {
        return $this->estado;
    }

    public function setEstado(?string $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getDataNascimento(): ?\DateTimeInterface
    {
        return $this->data_nascimento;
    }

    public function setDataNascimento(?\DateTimeInterface $data_nascimento): self
    {
        $this->data_nascimento = $data_nascimento;

        return $this;
    }

    public function getCpf(): ?string
    {
        return $this->cpf;
    }

    public function setCpf(?string $cpf): self
    {
        $this->cpf = $cpf;

        return $this;
    }

    public function getTelefoneDdd(): ?string
    {
        return $this->telefone_ddd;
    }

    public function setTelefoneDdd(string $telefone_ddd): self
    {
        $this->telefone_ddd = $telefone_ddd;

        return $this;
    }

    public function getTelefoneNumero(): ?string
    {
        return $this->telefone_numero;
    }

    public function setTelefoneNumero(?string $telefone_numero): self
    {
        $this->telefone_numero = $telefone_numero;

        return $this;
    }

    public function getLogradouro(): ?string
    {
        return $this->logradouro;
    }

    public function setLogradouro(?string $logradouro): self
    {
        $this->logradouro = $logradouro;

        return $this;
    }

    public function getEnderecoNumero(): ?string
    {
        return $this->endereco_numero;
    }

    public function setEnderecoNumero(?string $endereco_numero): self
    {
        $this->endereco_numero = $endereco_numero;

        return $this;
    }

    public function getBairro(): ?string
    {
        return $this->bairro;
    }

    public function setBairro(?string $bairro): self
    {
        $this->bairro = $bairro;

        return $this;
    }

    public function getCodMoip(): ?string
    {
        return $this->cod_moip;
    }

    public function setCodMoip(?string $cod_moip): self
    {
        $this->cod_moip = $cod_moip;

        return $this;
    }

    public function getDataCadastro(): ?\DateTime
    {
        return $this->data_cadastro;
    }

    public function setDataCadastro(?\DateTime $data_cadastro): self
    {
        $this->data_cadastro = $data_cadastro;

        return $this;
    }

    public function getDataAlteracao(): ?\DateTime
    {
        return $this->data_alteracao;
    }

    public function setDataAlteracao(?\DateTime $data_alteracao): self
    {
        $this->data_alteracao = $data_alteracao;

        return $this;
    }

    public function getFotoFile()
    {
        return $this->foto_file;
    }

    public function setFotoFile($foto_file = null)
    {
        $this->foto_file = $foto_file;
        if ($this->foto_file instanceof UploadedFile) {
            $this->data_alteracao = new \DateTime('now');
        }
        return $this;
    }

    public function getMoipAccessToken(): ?string
    {
        return $this->moip_access_token;
    }

    public function setMoipAccessToken(?string $moip_access_token): self
    {
        $this->moip_access_token = $moip_access_token;

        return $this;
    }

    public function getMoipIdConta(): ?string
    {
        return $this->moip_id_conta;
    }

    public function setMoipIdConta(?string $moip_id_conta): self
    {
        $this->moip_id_conta = $moip_id_conta;

        return $this;
    }
}
