export default [
  {
    path: '/config',
    name: 'Config',
    component: () => import('./views/Config')
  },
  {
    path: '/config/usuario',
    name: 'ConfigUsuario',
    component: () => import('./views/ConfigUsuario')
  },
  {
    path: '/config/investimento',
    name: 'ConfigInvestimento',
    component: () => import('./views/ConfigInvestimento')
  },
  {
    path: '/config/financa',
    name: 'ConfigFinanca',
    component: () => import('./views/ConfigFinanca')
  },
  {
    path: '/config/cofre',
    name: 'ConfigCofre',
    component: () => import('./views/ConfigCofre')
  },
  {
    path: '/config/relatorio',
    name: 'ConfigRelatorio',
    component: () => import('./views/ConfigRelatorio')
  },
]