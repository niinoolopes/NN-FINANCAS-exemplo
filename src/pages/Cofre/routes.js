export default [
  {
    path: '/cofre/cadastrar',
    name: 'CofreCadastrar',
    component: () => import('./views/Cadastrar')
  },
  {
    path: '/cofre/extrato',
    name: 'CofreExtrato',
    component: () => import('./views/Extrato')
  },
  {
    path: '/cofre/geral',
    name: 'CofreGeral',
    component: () => import('./views/Geral')
  },
]