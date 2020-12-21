import routesFinanca      from '@/pages/Financa/routes.js'
import routesCofre        from '@/pages/Cofre/routes.js'
import routesInvestimento from '@/pages/Investimento/routes.js'

import routesConfig       from '@/pages/Config/routes.js'
import routesPainel       from '@/pages/Painel/routes.js'
import routesLogin        from '@/pages/Login/routes.js'

export default async (to, from, next) => {

    var name = 'NN-CRM'
    
    routesFinanca
      .map( route => {
        if( to.name == route.name) {
          name = 'NN-Finanças'
        }
      })
  
    routesCofre
      .map( route => {
        if( to.name == route.name) {
          name = 'NN-Cofre'
        }
      })
    
    routesInvestimento
      .map( route => {
        if( to.name == route.name) {
          name = 'NN-Investimentos'
        }
      })
  
    var tmp = [].concat(routesConfig, routesPainel, routesLogin)
    tmp
      .map( route => {
        if( to.name == route.name) {
          name = 'NN-CRM'
        }
      })
  
    document.title = name
    
    if(document.getElementById('crm-modulo'))
      document.getElementById('crm-modulo').innerText = name


  next()
}