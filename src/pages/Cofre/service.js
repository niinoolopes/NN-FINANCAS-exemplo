import api from '@/api.js'

export default {
  // Busca 
  busca(endPoint){
    return api.get(`cofre/busca/${endPoint}`)
  },


  // CRUD
  item: {
    get({COIT_ID, USUA_ID}){
      return api.get(`cofre/item?usuario=${USUA_ID}&COIT_ID=${COIT_ID}`)
    },
    post({USUA_ID, data}){
      return api.post(`cofre/item?usuario=${USUA_ID}`, data)
    },
    put({COIT_ID, USUA_ID, data}){
      return api.post(`cofre/item/${COIT_ID}?usuario=${USUA_ID}`, data)
    },
    del({COIT_ID}){
      return api.get(`cofre/item/delete/${COIT_ID}`)
    }
  },


  // AUX
  formatItemsToTable(items){
    var itemReturn = [];

    items.map( item => {
      if(item.COTP_ID == 1) item.COTP_CSS = 'text-success'
      if(item.COTP_ID == 2) item.COTP_CSS = 'text-danger'
      item.COIT_OBS = item.COIT_OBS.substring(0, 50);
      itemReturn.push(item)
    })

    return itemReturn
  },
}