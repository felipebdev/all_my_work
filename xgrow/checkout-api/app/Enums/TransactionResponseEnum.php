<?php

namespace App\Enums;

use App\Enums\BasicEnum;

abstract class TransactionResponseEnum extends BasicEnum 
{
    const CODE_0000 = 'Transação autorizada';
    const CODE_1000 = 'Transação não autorizada';
    const CODE_1001 = 'Cartão vencido';
    const CODE_1002 = 'Transação não permitida';
    const CODE_1003 = 'Rejeitado pelo emissor';
    const CODE_1004 = 'Cartão com restrição';
    const CODE_1005 = 'Transação não autorizada';
    const CODE_1006 = 'Tentativas de senha excedidas';
    const CODE_1007 = 'Rejeitado emissor';
    const CODE_1008 = 'Rejeitado emissor';
    const CODE_1009 = 'Transação não autorizada';
    const CODE_1010 = 'Valor inválido';
    const CODE_1011 = 'Cartão inválido';
    const CODE_1013 = 'Transação não autorizada';
    const CODE_1014 = 'Tipo de conta inválido';
    const CODE_1015 = 'Função não suportada';
    const CODE_1016 = 'Saldo insuficiente';
    const CODE_1017 = 'Senha inválida';
    const CODE_1019 = 'Transação não permitida';
    const CODE_1020 = 'Transação não permitida';
    const CODE_1021 = 'Rejeitado emissor';
    const CODE_1022 = 'Cartão com restrição';
    const CODE_1023 = 'Rejeitado emissor';
    const CODE_1024 = 'Transação não permitida';
    const CODE_1025 = 'Cartão bloqueado';
    const CODE_1027 = 'Excedida a quantidade de transações para o cartão';
    const CODE_1042 = 'Tipo de conta inválido';
    const CODE_1045 = 'Código de segurança inválido';
    const CODE_1048 = 'Nova senha inválida';
    const CODE_1049 = 'Banco/emissor do cartão inválido';
    const CODE_2000 = 'Cartão com restrição';
    const CODE_2001 = 'Cartão vencido';
    const CODE_2002 = 'Transação não permitida';
    const CODE_2003 = 'Rejeitado emissor';
    const CODE_2004 = 'Cartão com restrição';
    const CODE_2005 = 'Transação não autorizada';
    const CODE_2006 = 'Tentativas de senha excedidas';
    const CODE_2007 = 'Cartão com restrição';
    const CODE_2008 = 'Cartão com restrição';
    const CODE_2009 = 'Cartão com restrição';
    const CODE_5003 = 'Erro interno';
    const CODE_5006 = 'Erro interno';
    const CODE_5025 = 'Código de segurança (CVV) do cartão não foi enviado';
    const CODE_5054 = 'Erro interno';
    const CODE_5062 = 'Transação não permitida para este produto ou serviço';
    const CODE_5086 = 'Cartão poupança inválido';
    const CODE_5088 = 'Transação não autorizada Amex';
    const CODE_5089 = 'Erro interno';
    const CODE_5092 = 'O valor solicitado para captura não é válido';
    const CODE_5093 = 'Banco emissor Visa indisponível';
    const CODE_5095 = 'Erro interno';
    const CODE_5097 = 'Erro interno';
    const CODE_9102 = 'Transação inválida';
    const CODE_9103 = 'Cartão cancelado';
    const CODE_9107 = 'O banco/emissor do cartão ou a conexão parece estar offline';
    const CODE_9108 = 'Erro no processamento';
    const CODE_9109 = 'Erro no processamento';
    const CODE_9111 = 'Emissor não respondeu em tempo';
    const CODE_9112 = 'Emissor indisponível';
    const CODE_9113 = 'Transmissão duplicada';
    const CODE_9124 = 'Código de segurança inválido';
    const CODE_9999 = 'Erro não especificado';
    const CODE_IMSG = 'Algum dado enviado na criação da transação não condiz com o modo de leitura aceito pela adquirente.';
}