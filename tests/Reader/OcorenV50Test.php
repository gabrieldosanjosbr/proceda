<?php

namespace Proceda\Tests\Reader;

use PHPUnit\Framework\TestCase;
use Proceda\Conhecimento;
use Proceda\Documento\Ocoren;
use Proceda\Embarque;
use Proceda\Empresa;
use Proceda\Fixtures\EdiPath;
use Proceda\NotaFiscal;
use Proceda\Ocorrencia;
use Proceda\Proceda;
use Proceda\Schema\LayoutType;
use Proceda\Schema\LayoutVersion;
use Proceda\Transportador;
use Proceda\Unidade;

class OcorenV50Test extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testReadOcorenV50()
    {
        $layout = Proceda::ocoren(LayoutVersion::V50);
        $result = $layout->read(
            EdiPath::for(
                LayoutType::OCOREN,
                LayoutVersion::V50->value,
            )
        );

        $this->assertFalse(
            $result->validation->hasErrors(),
            'Validation errors occurred',
        );

        $intercambio = $result->intercambio;
        $this->assertSame('PROCEDA DEMO REMETENTE LTDA', $intercambio->getRemetente());
        $this->assertCount(1, $intercambio->getDocumentos());

        $documento = $intercambio->getDocumentos()[0];
        $this->assertInstanceOf(Ocoren::class, $documento);
        $this->assertSame('OCOR2026000001', $documento->getIdentificacao());

        $transportador = $documento->getTransportador();
        $this->assertInstanceOf(Transportador::class, $transportador);
        $this->assertCount(1, $transportador->getOcorrencias());
        $this->assertSame(
            'TRANSPORTADORA DEMO LOGISTICA LTDA',
            $transportador->getEmpresa()->getNome(),
        );

        $ocorrencia = $transportador->getOcorrencias()[0];
        $this->assertInstanceOf(Ocorrencia::class, $ocorrencia);
        $this->assertSame('20260703', $ocorrencia->getData());
        $this->assertSame('DEV', $ocorrencia->getDevolucao()->getSerie());
        $this->assertSame('000654321', $ocorrencia->getDevolucao()->getNumero());
        $this->assertCount(3, $ocorrencia->getTextosLivres());

        $embarques = $ocorrencia->getEmbarques();
        $embarque = $embarques[array_key_last($embarques)];
        $this->assertInstanceOf(Embarque::class, $embarque);
        $this->assertSame('EMBARQUE 123', $embarque->getIdentificacao());

        $notaFiscal = $ocorrencia->getNotaFiscal();
        $this->assertInstanceOf(NotaFiscal::class, $notaFiscal);
        $this->assertSame('123456789', $notaFiscal->getNumero());
        $this->assertSame('12345678000199', $notaFiscal->getEmissor()->getCnpj());

        $conhecimento = $ocorrencia->getConhecimento();
        $this->assertInstanceOf(Conhecimento::class, $conhecimento);
        $this->assertSame('A0001', $conhecimento->getSerie());
        $this->assertSame('000000123456', $conhecimento->getNumero());

        $emissor = $conhecimento->getEmissor();
        $this->assertInstanceOf(Empresa::class, $emissor);

        $filial = $emissor->getFilial();
        $this->assertInstanceOf(Unidade::class, $filial);
        $this->assertSame('FILIAL001', $filial->getIdentificacao());
    }
}
