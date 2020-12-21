import api from '@/api.js'

export default {
  busca(endPoint){
    return api.get(`investimento/busca/${endPoint}`)
  },

  // CRUD
  ordem: {
    get({USUA_ID, INOD_ID, INCT_ID, dataDe, dataAte, limit}){
      
      let url = `investimento/ordem?usuario=${USUA_ID}`

      if(INOD_ID) url = `${url}&INOD_ID=${INOD_ID}`
      if(INCT_ID) url = `${url}&INCT_ID=${INCT_ID}`
      if(limit)   url = `${url}&limit=${limit}`
      if(dataDe)  url = `${url}&dataDe=${dataDe}`
      if(dataAte) url = `${url}&dataAte=${dataAte}`

      return api.get(url)
    },
    post({USUA_ID}, data){
      return api.post(`investimento/ordem?usuario=${USUA_ID}`, data)
    },
    put({USUA_ID,INOD_ID}, data){
      return api.post(`investimento/ordem/${INOD_ID}?usuario=${USUA_ID}`, data)
    },
    del({INOD_ID}){
      return api.get(`investimento/ordem/delete/${INOD_ID}`)
    }
  },

  item: {
    get({USUA_ID,INOD_ID,INIT_ID}){
      let url = `investimento/item?usuario=${USUA_ID}`

      if(INOD_ID) url = `${url}&INOD_ID=${INOD_ID}`
      if(INIT_ID) url = `${url}&INIT_ID=${INIT_ID}`

      return api.get(url)
    },
    post({USUA_ID}, data){
      return api.post(`investimento/item?usuario=${USUA_ID}`, data)
    },
    put({USUA_ID, INIT_ID}, data){
      return api.post(`investimento/item/${INIT_ID}?usuario=${USUA_ID}`, data)
    },
    del({INIT_ID}){
      return api.get(`investimento/item/delete/${INIT_ID}`)
    }
  },

  rendimento: {
    get({USUA_ID,INAR_ID}){
      return api.get(`investimento/rendimento?usuario=${USUA_ID}&INAR_ID=${INAR_ID}`)
    },
    post({USUA_ID, data}){
      return api.post(`investimento/rendimento?usuario=${USUA_ID}`, data)
    },
    put({USUA_ID, INAR_ID, data}){
      return api.post(`investimento/rendimento/${INAR_ID}?usuario=${USUA_ID}`, data)
    },
    del({USUA_ID, INAR_ID}){
      return api.get(`investimento/rendimento/delete/${INAR_ID}?usuario=${USUA_ID}`)
    }
  },
  
  cotacao: {
    get({USUA_ID, INCT_ID, INAC_ID, INAC_TIPO}){
      let url = `investimento/cotacao?usuario=${USUA_ID}`

      if(INCT_ID)   url = `${url}&INCT_ID=${INCT_ID}`
      if(INAC_ID)   url = `${url}&INAC_ID=${INAC_ID}`
      if(INAC_TIPO) url = `${url}&INAC_TIPO=${INAC_TIPO}`

      return api.get(url)
    },
    post({USUA_ID, INCT_ID, data}){
      return api.post(`investimento/cotacao?usuario=${USUA_ID}&INCT_ID=${INCT_ID}`, data)
    },
    put({USUA_ID, INCT_ID, INAC_ID, data}){
      return api.post(`investimento/cotacao/${INAC_ID}?usuario=${USUA_ID}&INCT_ID=${INCT_ID}`, data)
    },
    del({USUA_ID, INCT_ID, INAC_ID}){
      return api.get(`investimento/cotacao/delete/${INAC_ID}?usuario=${USUA_ID}&INCT_ID=${INCT_ID}`)
    }
  },
  
  split: {
    get({USUA_ID, INAS_ID}){
      let url = `investimento/ativo-split?usuario=${USUA_ID}`

      if(INAS_ID) url = `${url}&INAS_ID=${INAS_ID}`

      return api.get(url)
    },
    post({USUA_ID, data}){
      return api.post(`investimento/ativo-split?usuario=${USUA_ID}`, data)
    },
    put({USUA_ID,INAS_ID, data}){
      return api.post(`investimento/ativo-split/${INAS_ID}?usuario=${USUA_ID}`, data)
    },
    del({INAS_ID}){
      return api.get(`investimento/ativo-split/delete/${INAS_ID}`)
    }
  },

  // AUX 
  filtroItem(arrFiltro, arrItems){
    var items = arrItems

    if(arrFiltro.INCR_ID)       items = items.filter(e => e.INCR_ID       == arrFiltro.INCR_ID)
    if(arrFiltro.INTP_ID)       items = items.filter(e => e.INTP_ID       == arrFiltro.INTP_ID)
    if(arrFiltro.INAT_ID)       items = items.filter(e => e.INAT_ID       == arrFiltro.INAT_ID)
    if(arrFiltro.INAV_ID)       items = items.filter(e => e.INAV_ID       == arrFiltro.INAV_ID)
    if(arrFiltro.INAV_LIQUIDEZ) items = items.filter(e => e.INAV_LIQUIDEZ == arrFiltro.INAV_LIQUIDEZ)
    if(arrFiltro.DATA)          items = items.filter(e => e.INIT_DATA     == arrFiltro.DATA)
    if(arrFiltro.COTAS == 'com-cotas') items = items.filter(e => e.COTAS != 0)
    if(arrFiltro.COTAS == 'sem-cotas') items = items.filter(e => e.COTAS == 0)

    return items
  },
  
  filtroOperacoes(arrFiltro, arrItems){
    var items = arrItems

    if(arrFiltro.INCR_ID)   items = items.filter(e => e.INCR_ID   == arrFiltro.INCR_ID)
    if(arrFiltro.INAT_ID)   items = items.filter(e => e.INAT_ID   == arrFiltro.INAT_ID)
    if(arrFiltro.INAV_ID)   items = items.filter(e => e.INAV_ID   == arrFiltro.INAV_ID)
    if(arrFiltro.CV)        items = items.filter(e => e.INIT_CV   == arrFiltro.CV)
    if(arrFiltro.INOD_DATA) items = items.filter(e => e.INOD_DATA == arrFiltro.INOD_DATA)

    return items
  },
  
  filtroOrdem(arrFiltro, arrItems){
    var items = arrItems

    if(arrFiltro.INCR_ID)   items = items.filter(e => e.INCR_ID   == arrFiltro.INCR_ID)
    if(arrFiltro.INOD_DATA) items = items.filter(e => e.INOD_DATA == arrFiltro.INOD_DATA)
    if(arrFiltro.OBS)       items = items.filter(e => e.INOD_DESCRICAO.toLowerCase().indexOf(arrFiltro.OBS.toLowerCase()) > -1 );

    return items
  },
  
  filtroSplit(arrFiltro, arrItems){
    var items = arrItems

    if(arrFiltro.INAT)       items = items.filter(e => e.INAT_ID         == arrFiltro.INAT)
    if(arrFiltro.INAV)       items = items.filter(e => e.INAV_ID         == arrFiltro.INAV)
    if(arrFiltro.TIPO)       items = items.filter(e => e.INAS_TIPO       == arrFiltro.TIPO)
    if(arrFiltro.QUANTIDADE) items = items.filter(e => e.INAS_QUANTIDADE == arrFiltro.QUANTIDADE)
    if(arrFiltro.DATA)       items = items.filter(e => e.INAS_DATA       == arrFiltro.DATA)

    return items
  },
  
  filtroRendimentos(arrFiltro, arrItems){
    var items = arrItems

    if(arrFiltro.INCR_ID)    items = items.filter(e => e.INCR_ID    == arrFiltro.INCR_ID)
    if(arrFiltro.INTP_ID)    items = items.filter(e => e.INTP_ID    == arrFiltro.INTP_ID)
    if(arrFiltro.INAT_ID)    items = items.filter(e => e.INAT_ID    == arrFiltro.INAT_ID)
    if(arrFiltro.INAV_ID)    items = items.filter(e => e.INAV_ID    == arrFiltro.INAV_ID)
    if(arrFiltro.INAR_TIPO)  items = items.filter(e => e.INAR_TIPO  == arrFiltro.INAR_TIPO)
    if(arrFiltro.INAR_VALOR) items = items.filter(e => e.INAR_VALOR == arrFiltro.INAR_VALOR)
    if(arrFiltro.INAR_DATA)  items = items.filter(e => e.INAR_DATA  == arrFiltro.INAR_DATA)

    return items
  },
}