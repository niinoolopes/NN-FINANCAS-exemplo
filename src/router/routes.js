import Cofre from '@/pages/Cofre/routes'
import Config from '@/pages/Config/routes'
import Financa from '@/pages/Financa/routes'
import Investimento from '@/pages/Investimento/routes'
import Login from '@/pages/Login/routes'
import Painel from '@/pages/Painel/routes'

export default {
  mode: 'history',
  base: process.env.BASE_URL,
  routes:[
    ...Cofre,
    ...Config,
    ...Financa,
    ...Investimento,
    ...Login,
    ...Painel
  ]
}