export default [
  {
    path: '/',
    name: 'Login',
    component: () => import('./views/Login')
  },
  {
    path: '/logout',
    name: 'Logout',
    component: () => import('./views/Logout')
  },
]