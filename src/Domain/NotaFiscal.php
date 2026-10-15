<?php

namespace Proceda;

use Proceda\NotaFiscal\Carga;
use Proceda\NotaFiscal\Item;
use Proceda\NotaFiscal\Volume;

readonly class NotaFiscal
{
    public function __construct(
        protected ?string       $numero = null,
        protected ?string       $serie = null,
        protected ?float        $valor = null,
        protected ?Empresa      $emissor = null,
        protected ?string       $dataEmissao = null,
        protected ?string       $romaneio = null,
        protected ?string       $codigoRota = null,
        protected ?bool         $seguroEfetuado = null,
        protected ?bool         $indicacaoBonificacao = null,
        protected ?float        $quantidadeVolumes = null,
        protected ?float        $pesoDensidadeCubagem = null,
        protected ?float        $valorSeguro = null,
        protected ?float        $valorCobradoCliente = null,
        protected ?float        $valorTaxas = null,
        protected ?string       $idPedidoCliente = null,
        protected ?string       $desdobro = null,
        protected ?string       $cfop = null,
        protected ?float        $pesoBruto = null,
        protected ?float        $pesoLiquido = null,
        protected ?float        $pesoCubado = null,
        protected ?float        $valorOutrasDespesasAcessorias = null,
        protected ?string       $ufFatoGerador = null,
        protected ?bool         $devolucao = null,
        protected ?Type         $acaoDocumento = null,
        protected ?Destinatario $destinatario = null,
        protected ?Carga        $carga = null,
        protected ?Nfe          $nfe = null,
        protected ?Conhecimento $redespacho = null,
        protected ?Type         $documentoFiscal = null,
        protected ?Imposto      $imposto = null,
        protected ?Frete        $frete = null,
        protected ?Veiculo      $veiculo = null,
        protected ?Type         $meioTransporte = null,
        protected ?Empresa      $transportador = null,
        protected ?Empresa      $consignatario = null,
        protected ?Empresa      $localColeta = null,
        /** @var Item[]  */
        protected array         $items = [],
        /** @var NotaFiscal[] */
        protected array         $entregasCasadas = [],
        /** @var Volume[] */
        protected array         $volumes = [],
        /** @var Embarque[] */
        protected array         $embarques = [],
    ) {}

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function getSerie(): ?string
    {
        return $this->serie;
    }

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function getEmissor(): ?Empresa
    {
        return $this->emissor;
    }

    public function getDataEmissao(): ?string
    {
        return $this->dataEmissao;
    }

    public function getRomaneio(): ?string
    {
        return $this->romaneio;
    }

    public function getCodigoRota(): ?string
    {
        return $this->codigoRota;
    }

    public function getSeguroEfetuado(): ?bool
    {
        return $this->seguroEfetuado;
    }

    public function getIndicacaoBonificacao(): ?bool
    {
        return $this->indicacaoBonificacao;
    }

    public function getQuantidadeVolumes(): ?float
    {
        return $this->quantidadeVolumes;
    }

    public function getPesoDensidadeCubagem(): ?float
    {
        return $this->pesoDensidadeCubagem;
    }

    public function getValorSeguro(): ?float
    {
        return $this->valorSeguro;
    }

    public function getValorCobradoCliente(): ?float
    {
        return $this->valorCobradoCliente;
    }

    public function getValorTaxas(): ?float
    {
        return $this->valorTaxas;
    }

    public function getIdPedidoCliente(): ?string
    {
        return $this->idPedidoCliente;
    }

    public function getDesdobro(): ?string
    {
        return $this->desdobro;
    }

    public function getCfop(): ?string
    {
        return $this->cfop;
    }

    public function getPesoBruto(): ?float
    {
        return $this->pesoBruto;
    }

    public function getPesoLiquido(): ?float
    {
        return $this->pesoLiquido;
    }

    public function getPesoCubado(): ?float
    {
        return $this->pesoCubado;
    }

    public function getValorOutrasDespesasAcessorias(): ?float
    {
        return $this->valorOutrasDespesasAcessorias;
    }

    public function getUfFatoGerador(): ?string
    {
        return $this->ufFatoGerador;
    }

    public function getDevolucao(): ?bool
    {
        return $this->devolucao;
    }

    public function getAcaoDocumento(): ?Type
    {
        return $this->acaoDocumento;
    }

    public function getDestinatario(): ?Destinatario
    {
        return $this->destinatario;
    }

    public function getCarga(): ?Carga
    {
        return $this->carga;
    }

    public function getNfe(): ?Nfe
    {
        return $this->nfe;
    }

    public function getRedespacho(): ?Conhecimento
    {
        return $this->redespacho;
    }

    public function getDocumentoFiscal(): ?Type
    {
        return $this->documentoFiscal;
    }

    public function getImposto(): ?Imposto
    {
        return $this->imposto;
    }

    public function getFrete(): ?Frete
    {
        return $this->frete;
    }

    public function getVeiculo(): ?Veiculo
    {
        return $this->veiculo;
    }

    public function getMeioTransporte(): ?Type
    {
        return $this->meioTransporte;
    }

    public function getTransportador(): ?Empresa
    {
        return $this->transportador;
    }

    public function getConsignatario(): ?Empresa
    {
        return $this->consignatario;
    }

    public function getLocalColeta(): ?Empresa
    {
        return $this->localColeta;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getEntregasCasadas(): array
    {
        return $this->entregasCasadas;
    }

    public function getVolumes(): array
    {
        return $this->volumes;
    }

    public function getEmbarques(): array
    {
        return $this->embarques;
    }
}
