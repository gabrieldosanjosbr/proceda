<?php

namespace Proceda\Builder;

use Proceda\Conhecimento;
use Proceda\Destinatario;
use Proceda\Embarque;
use Proceda\Empresa;
use Proceda\Frete;
use Proceda\Imposto;
use Proceda\Nfe;
use Proceda\NotaFiscal;
use Proceda\NotaFiscal\Carga;
use Proceda\NotaFiscal\Item;
use Proceda\NotaFiscal\Volume;
use Proceda\Type;
use Proceda\Veiculo;

class NotaFiscalBuilder extends Builder
{
    protected ?string $numero = null;
    protected ?string $serie = null;
    protected ?float $valor = null;
    protected ?Empresa $emissor = null;
    protected ?string $dataEmissao = null;
    protected ?string $romaneio = null;
    protected ?string $codigoRota = null;
    protected ?bool $seguroEfetuado = null;
    protected ?bool $indicacaoBonificacao = null;
    protected ?float $quantidadeVolumes = null;
    protected ?float $pesoDensidadeCubagem = null;
    protected ?float $valorSeguro = null;
    protected ?float $valorCobradoCliente = null;
    protected ?float $valorTaxas = null;
    protected ?string $idPedidoCliente = null;
    protected ?string $desdobro = null;
    protected ?string $cfop = null;
    protected ?float $pesoBruto = null;
    protected ?float $pesoLiquido = null;
    protected ?float $pesoCubado = null;
    protected ?float $valorOutrasDespesasAcessorias = null;
    protected ?string $ufFatoGerador = null;
    protected ?bool $devolucao = null;
    protected ?Type $acaoDocumento = null;
    protected ?Destinatario $destinatario = null;
    protected ?Nfe $nfe = null;
    protected ?Conhecimento $redespacho = null;
    protected ?Carga $carga = null;
    protected ?Type $documentoFiscal = null;
    protected ?Imposto $imposto = null;
    protected ?Frete $frete = null;
    protected ?Type $meioTransporte = null;
    protected ?Veiculo $veiculo = null;
    protected ?Empresa $transportador = null;
    protected ?Empresa $consignatario = null;
    protected ?Empresa $localColeta = null;

    /** @var Item[] */
    protected array $items = [];

    /** @var NotaFiscal[] */
    protected array $entregasCasadas = [];

    /** @var Volume[] */
    protected array $volumes = [];

    /** @var Embarque[] */
    protected array $embarques = [];

    public function numero(?string $numero): static
    {
        $this->numero = $numero;
        return $this;
    }
    public function serie(?string $serie): static
    {
        $this->serie = $serie;
        return $this;
    }
    public function valor(?float $valor): static
    {
        $this->valor = $valor;
        return $this;
    }
    public function emissor(?Empresa $emissor): static
    {
        $this->emissor = $emissor;
        return $this;
    }
    public function dataEmissao(?string $dataEmissao): static
    {
        $this->dataEmissao = $dataEmissao;
        return $this;
    }
    public function romaneio(?string $romaneio): static
    {
        $this->romaneio = $romaneio;
        return $this;
    }
    public function codigoRota(?string $codigoRota): static
    {
        $this->codigoRota = $codigoRota;
        return $this;
    }
    public function seguroEfetuado(?bool $seguroEfetuado): static
    {
        $this->seguroEfetuado = $seguroEfetuado;
        return $this;
    }
    public function indicacaoBonificacao(?bool $indicacaoBonificacao): static
    {
        $this->indicacaoBonificacao = $indicacaoBonificacao;
        return $this;
    }
    public function quantidadeVolumes(?float $quantidadeVolumes): static
    {
        $this->quantidadeVolumes = $quantidadeVolumes;
        return $this;
    }
    public function pesoDensidadeCubagem(?float $pesoDensidadeCubagem): static
    {
        $this->pesoDensidadeCubagem = $pesoDensidadeCubagem;
        return $this;
    }
    public function valorSeguro(?float $valorSeguro): static
    {
        $this->valorSeguro = $valorSeguro;
        return $this;
    }
    public function valorCobradoCliente(?float $valorCobradoCliente): static
    {
        $this->valorCobradoCliente = $valorCobradoCliente;
        return $this;
    }
    public function valorTaxas(?float $valorTaxas): static
    {
        $this->valorTaxas = $valorTaxas;
        return $this;
    }
    public function idPedidoCliente(?string $idPedidoCliente): static
    {
        $this->idPedidoCliente = $idPedidoCliente;
        return $this;
    }
    public function desdobro(?string $desdobro): static
    {
        $this->desdobro = $desdobro;
        return $this;
    }
    public function cfop(?string $codigo): static
    {
        $this->cfop = $codigo;
        return $this;
    }
    public function pesoBruto(?float $pesoBruto): static
    {
        $this->pesoBruto = $pesoBruto;
        return $this;
    }
    public function pesoLiquido(?float $pesoLiquido): static
    {
        $this->pesoLiquido = $pesoLiquido;
        return $this;
    }
    public function pesoCubado(?float $pesoCubado): static
    {
        $this->pesoCubado = $pesoCubado;
        return $this;
    }
    public function valorOutrasDespesasAcessorias(?float $valorOutrasDespesasAcessorias): static
    {
        $this->valorOutrasDespesasAcessorias = $valorOutrasDespesasAcessorias;
        return $this;
    }
    public function ufFatoGerador(?string $uf): static
    {
        $this->ufFatoGerador = $uf;
        return $this;
    }
    public function devolucao(?bool $devolucao): static
    {
        $this->devolucao = $devolucao;
        return $this;
    }
    public function acaoDocumento(?Type $acaoDocumento): static
    {
        $this->acaoDocumento = $acaoDocumento;
        return $this;
    }
    public function destinatario(?Destinatario $destinatario): static
    {
        $this->destinatario = $destinatario;
        return $this;
    }
    public function carga(?Carga $carga): static
    {
        $this->carga = $carga;
        return $this;
    }
    public function nfe(?Nfe $nfe): static
    {
        $this->nfe = $nfe;
        return $this;
    }
    public function redespacho(?Conhecimento $redespacho): static
    {
        $this->redespacho = $redespacho;
        return $this;
    }
    public function documentoFiscal(?Type $documentoFiscal): static
    {
        $this->documentoFiscal = $documentoFiscal;
        return $this;
    }
    public function imposto(?Imposto $imposto): static
    {
        $this->imposto = $imposto;
        return $this;
    }
    public function frete(?Frete $frete): static
    {
        $this->frete = $frete;
        return $this;
    }
    public function meioTransporte(Type $value): static
    {
        $this->meioTransporte = $value;
        return $this;
    }
    public function veiculo(Veiculo $value): static
    {
        $this->veiculo = $value;
        return $this;
    }
    public function transportador(Empresa $value): static
    {
        $this->transportador = $value;
        return $this;
    }
    public function consignatario(?Empresa $consignatario): static
    {
        $this->consignatario = $consignatario;
        return $this;
    }
    public function localColeta(?Empresa $localColeta): static
    {
        $this->localColeta = $localColeta;
        return $this;
    }

    public function addItem(Item $notaFiscalItem): static
    {
        $this->items[] = $notaFiscalItem;

        return $this;
    }

    public function addEntregaCasada(NotaFiscal $entregaCasada): static
    {
        $this->entregasCasadas[] = $entregaCasada;

        return $this;
    }

    public function addVolume(Volume $volume): static
    {
        $this->volumes[] = $volume;

        return $this;
    }

    public function addEmbarque(Embarque $embarque): static
    {
        $this->embarques[] = $embarque;

        return $this;
    }

    public function build(): NotaFiscal
    {
        return new NotaFiscal(
            numero: $this->numero,
            serie: $this->serie,
            valor: $this->valor,
            emissor: $this->emissor,
            dataEmissao: $this->dataEmissao,
            romaneio: $this->romaneio,
            codigoRota: $this->codigoRota,
            seguroEfetuado: $this->seguroEfetuado,
            indicacaoBonificacao: $this->indicacaoBonificacao,
            quantidadeVolumes: $this->quantidadeVolumes,
            pesoDensidadeCubagem: $this->pesoDensidadeCubagem,
            valorSeguro: $this->valorSeguro,
            valorCobradoCliente: $this->valorCobradoCliente,
            valorTaxas: $this->valorTaxas,
            idPedidoCliente: $this->idPedidoCliente,
            desdobro: $this->desdobro,
            cfop: $this->cfop,
            pesoBruto: $this->pesoBruto,
            pesoLiquido: $this->pesoLiquido,
            pesoCubado: $this->pesoCubado,
            valorOutrasDespesasAcessorias: $this->valorOutrasDespesasAcessorias,
            ufFatoGerador: $this->ufFatoGerador,
            devolucao: $this->devolucao,
            acaoDocumento: $this->acaoDocumento,
            destinatario: $this->destinatario,
            carga: $this->carga,
            nfe: $this->nfe,
            redespacho: $this->redespacho,
            documentoFiscal: $this->documentoFiscal,
            imposto: $this->imposto,
            frete: $this->frete,
            veiculo: $this->veiculo,
            meioTransporte: $this->meioTransporte,
            transportador: $this->transportador,
            consignatario: $this->consignatario,
            localColeta: $this->localColeta,
            items: $this->items,
            entregasCasadas: $this->entregasCasadas,
            volumes: $this->volumes,
            embarques: $this->embarques,
        );
    }
}
