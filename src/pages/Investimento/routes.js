export default [
  {
    path: '/investimento/ordem',
    name: 'InvestimentoOrdem',
    component: () => import('./views/Ordem'),
  },
  {
    path: '/investimento/ordem/:INOD_ID?',
    name: 'InvestimentoOrdemAdm',
    component: () => import('./views/OrdemAdm'),
  },
  {
    path: '/investimento/extrato-rendimento',
    name: 'InvestimentoExtratoRendimento',
    component: () => import('./views/ExtratoRendimento'),
  },
  {
    path: '/investimento/geral',
    name: 'InvestimentoGeral',
    component: () => import('./views/Geral'),
  },
  {
    path: '/investimento/carteira',
    name: 'InvestimentoAnaliseCarteira',
    component: () => import('./views/AnaliseCarteira'),
  },
  {
    path: '/investimento/Ativo',
    name: 'InvestimentoAnaliseAtivo',
    component: () => import('./views/AnaliseAtivo'),
  },
  {
    path: '/investimento/Ativo/:INAV_ID?',
    name: 'InvestimentoAtivoAdm',
    component: () => import('./views/AtivoAdm'),
  },
  {
    path: '/investimento/cotacao',
    name: 'InvestimentoCotacao',
    component: () => import('./views/Cotacao'),
  },
  {
    path: '/investimento/SplitInplit',
    name: 'InvestimentoSplitInplit',
    component: () => import('./views/SplitInplit'),
  },
  {
    path: '/investimento/extrato-operacao',
    name: 'InvestimentoExtratoOperacao',
    component: () => import('./views/ExtratoOperacao'),
  },
]