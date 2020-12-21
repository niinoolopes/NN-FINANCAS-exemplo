export default [
  {
    path: '/financa/geral',
    name: 'FinancaGeral',
    component: () => import('./views/Geral')
  },
  {
    path: '/financa/extrato',
    name: 'FinancaExtrato',
    component: () => import('./views/Extrato')
  },
  {
    path: '/financa/cadastrar/:FITP?',
    name: 'FinancaCadastrar',
    component: () => import('./views/Cadastrar')
  },
  {
    path: '/financa/lista-fixa',
    name: 'FinancaListaFixa',
    component: () => import('./views/ListaFixa')
  },
  {
    path: '/financa/analise-mes',
    name: 'FinancaAnaliseMes',
    component: () => import('./views/AnaliseMes')
  },
  {
    path: '/financa/analise-ano',
    name: 'FinancaAnaliseAno',
    component: () => import('./views/AnaliseAno')
  },
  {
    path: '/financa/analise-grupo-categoria/:idFINC?/:idFIGP?/:idFICT?',
    name: 'FinancaAnaliseGrupo',
    component: () => import('./views/AnaliseGrupo')
  },
]