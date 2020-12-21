import api from '@/api.js'

export default {
  busca(endPoint){
    return api.get(`financa/busca/${endPoint}`)
  },
  
  /*
  gets: {
    // getMes 
    getMes({id, mes, consulta}){
      return api.get(`financa/item/mes?carteira=${id}&mes=${mes}&consulta=${consulta}`)
    },


    consolidadoMes({id, mes}){
      return api.get(`financa/item/mes?carteira=${id}&mes=${mes}&consulta=consolidado`)
    },

    geral({id, mes}){
      return api.get(`financa/item/mes?carteira=${id}&mes=${mes}&consulta=geral`)
    },

    analiseMes({id, mes}){
      return api.get(`financa/item/mes?carteira=${id}&mes=${mes}&consulta=analiseUltimosMeses`)
    },

    // getAno
    consolidadoAno({id, mes}){
      return api.get(`financa/item/ano?carteira=${id}&consulta=consolidadoAnoTipo&ano=${mes}`)
    },

    historico({FINC_ID,de,ate}){
        return api.get(`financa/item/historico/${FINC_ID}`)
    }
  },

  analiseAno({id, mes}){
    return api.get(`financa/item/ano?carteira=${id}&mes=${mes}&consulta=analiseAno`)
  },

  analiseGrupo({id, mes, FIGP, FICT}){
    if(FICT){
      return api.get(`financa/item/grupo?carteira=${id}&periodo=${mes}&grupo=${FIGP}&categoria=${FICT}`)
    }
    return api.get(`financa/item/grupo?carteira=${id}&periodo=${mes}&grupo=${FIGP})`)
  },

  grupo({USUA_ID, FINC_ID}){
    return api.get(`financa/grupo?usuario=${USUA_ID}&carteira=${FINC_ID}`)
  },

  categoria({USUA_ID, FINC_ID}){
    return api.get(`financa/categoria?usuario=${USUA_ID}&carteira=${FINC_ID}`)
  },
  */


  // CRUD
  item: {
    get({USUA_ID, FNIT_ID, data}){
      return api.get(`financa/item?usuario=${USUA_ID}&FNIT_ID=${FNIT_ID}`, data)
    },
    post({USUA_ID, data}){
      return api.post(`financa/item?usuario=${USUA_ID}`, data)
    },
    put({FNIT_ID, data}){
      return api.post(`financa/item/${FNIT_ID}`, data)
    },
    del({data}){
      return api.get(`financa/item/delete/${data.FNIT_ID}`)
    },
  },

  listaFixa:{
    get({USUA_ID, FINC_ID}){
      return api.get(`financa/lista-fixa?usuario=${USUA_ID}&FINC_ID=${FINC_ID}`)
    },
    post({USUA_ID, data}){
      return api.post(`financa/lista-fixa?usuario=${USUA_ID}`, data)
    },
    put({FNLF_ID, data}){
      return api.post(`financa/lista-fixa/${FNLF_ID}`, data)
    },
    del({FNLF_ID}){
      return api.get(`financa/lista-fixa/delete/${FNLF_ID}`)
    },
  },


  // AUX 
  formatItemsToTable(items){
    var itemReturn = [];

    items.map( item => {
      if(item.FITP_ID == 1) item.FITP_CSS = 'text-success'
      if(item.FITP_ID == 2) item.FITP_CSS = 'text-danger'
      if(item.FNIS_ID == 1) item.FNIS_CSS = 'font-weight-bold text-success'
      if(item.FNIS_ID == 2) item.FNIS_CSS = 'font-weight-bold text-danger'
      if(item.FNIS_ID == 3) item.FNIS_CSS = 'font-weight-bold text-black-50'
      itemReturn.push(item)
    })

    return itemReturn
  },

  obsercacao(categorias, item){
    if(item.FICT_ID){
      let categoria = categorias.filter( c => c.FICT_ID === item.FICT_ID )[0] // identifica a categoria
      let msg = categoria.FICT_OBS // get observação
      let test = msg !== undefined && item.FNIT_OBS === '' // valida se pode adicionar
      return test ? msg : ''
    }
  },
  
  filtroItems(arrFiltro, arrItems){
    var items = arrItems

    if(arrFiltro.FITP_ID) items = items.filter( i => i.FITP_ID == arrFiltro.FITP_ID);
    if(arrFiltro.FIGP_ID) items = items.filter( i => i.FIGP_ID == arrFiltro.FIGP_ID);
    if(arrFiltro.FICT_ID) items = items.filter( i => i.FICT_ID == arrFiltro.FICT_ID);
    if(arrFiltro.FNIS_ID) items = items.filter( i => i.FNIS_ID == arrFiltro.FNIS_ID);
    if(arrFiltro.OBS)     items = items.filter( i => i.FNIT_OBS.toLowerCase().indexOf(arrFiltro.OBS.toLowerCase()) > -1 );

    return items.filter( i => i.FNIT_ID != 'novo');
  },

}