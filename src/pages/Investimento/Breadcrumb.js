export default {
  InvestimentoOrdem: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Ordem',},
  ],

  InvestimentoOrdemAdm: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Ordem', to:{name: 'InvestimentoOrdem'}},
    {text: 'Ordem ADM'},
  ],

  InvestimentoExtratoRendimento: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Extrato de rendimentos'},
  ],

  InvestimentoCarteira: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Carteira'},
  ],

  InvestimentoGeral: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Geral'},
  ],

  InvestimentoAtivo: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Ativo'},
  ],

  InvestimentoAtivoAdm: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Ativo', to:{name: 'InvestimentoAnaliseCarteira'}},
    {text: 'AtivoAdm'},
  ],

  InvestimentoCotacao: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Cotacao'},
  ],

  InvestimentoSplitInplit: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Split - Inplit'},
  ],

  InvestimentoExtratoOperacoes: [
    {text: 'Painel', to:{name: 'Painel'}},
    {text: 'Investimento'},
    {text: 'Extrato de operações'},
  ],
}