import api from '@/api.js'

export default {
  // Busca 
  busca(endPoint){
    return api.get(`${endPoint}`)
  },

  financ:{
    carteira: {
      get({USUA_ID, FINC_ID}){
        let url = `financa/carteira?usuario=${USUA_ID}`;

        if(FINC_ID) url += `&FINC_ID=${FINC_ID}`

        return api.get(url)
      },
      post({USUA_ID, data}){
        return api.post(`financa/carteira?usuario=${USUA_ID}`, data)
      },
      put({USUA_ID, FINC_ID, data}){
        return api.post(`financa/carteira/${FINC_ID}?usuario=${USUA_ID}`, data)
      },
      del({USUA_ID, FINC_ID}){
        return api.get(`financa/carteira/delete/${FINC_ID}?usuario=${USUA_ID}`)
      },
    },
    
    grupo: {
      get({USUA_ID,FINC_ID}){
        return api.get(`financa/grupo?usuario=${USUA_ID}&FINC_ID=${FINC_ID}`)
      },
      post({data}){
        return api.post(`financa/grupo`, data)
      },
      put({FIGP_ID,data}){
        return api.post(`financa/grupo/${FIGP_ID}`, data)
      },
      del({FIGP_ID}){
        return api.get(`financa/grupo/delete/${FIGP_ID}`)
      },
    },

    categoria: {
      get({USUA_ID,FINC_ID,FIGP_ID}){
        if(FIGP_ID) return api.get(`financa/categoria?usuario=${USUA_ID}&FINC_ID=${FINC_ID}&FIGP_ID=${FIGP_ID}`)
        return api.get(`financa/categoria?usuario=${USUA_ID}&FINC_ID=${FINC_ID}`)
      },
      post({USUA_ID, data}){
        return api.post(`financa/categoria?usuario=${USUA_ID}`, data)
      },
      put({USUA_ID, FICT_ID,data}){
        return api.post(`financa/categoria/${FICT_ID}?usuario=${USUA_ID}`, data)
      },
      del({USUA_ID, FICT_ID}){
        return api.get(`financa/categoria/delete/${FICT_ID}?usuario=${USUA_ID}`)
      },
    },
  },

  invest: {
    carteira: {
      get({USUA_ID, INCT_ID}){
        let url = `investimento/carteira?usuario=${USUA_ID}`

        if(INCT_ID) url += `&INCT_ID=${INCT_ID}`

        return api.get(url)
      },
      post({USUA_ID, data}){
        return api.post(`investimento/carteira?usuario=${USUA_ID}`, data)
      },
      put({INCT_ID, USUA_ID, data}){
        return api.post(`investimento/carteira/${INCT_ID}?usuario=${USUA_ID}`, data)
      },
      del({INCT_ID}){
        return api.get(`investimento/carteira/delete/${INCT_ID}`)
      }
    },

    corretora: {
      get({INCR_ID, USUA_ID}){
        let url = `investimento/corretora?usuario=${USUA_ID}`

        if(INCR_ID) url += `&INCR_ID=${INCR_ID}`

        return api.get(url)
      },
      post({USUA_ID, data}){
        return api.post(`investimento/corretora?usuario=${USUA_ID}`, data)
      },
      put({INCR_ID, USUA_ID, data}){
        return api.post(`investimento/corretora/${INCR_ID}?usuario=${USUA_ID}`, data)
      },
      del({INCR_ID}){
        return api.get(`investimento/corretora/delete/${INCR_ID}`)
      }
    },

    ativo: {
      get({USUA_ID,INAV_ID}){
        let url = `investimento/ativo?usuario=${USUA_ID}`

        if(INAV_ID) url += `&INAV_ID=${INAV_ID}`

        return api.get(url)
      },
      post({USUA_ID, data}){
        return api.post(`investimento/ativo?usuario=${USUA_ID}`, data)
      },
      put({INAV_ID, USUA_ID, data}){
        return api.post(`investimento/ativo/${INAV_ID}?usuario=${USUA_ID}`, data)
      },
      del({INAV_ID}){
        return api.get(`investimento/ativo/delete/${INAV_ID}`)
      }
    },


    ativoTipo: {
      get({USUA_ID, INAT_ID}){
        let url = `investimento/ativo-tipo?usuario=${USUA_ID}`

        if(INAT_ID) url += `&INAT_ID=${INAT_ID}`

        return api.get(url)
      },
      post({USUA_ID, data}){
        return api.post(`investimento/ativo-tipo?usuario=${USUA_ID}`, data)
      },
      put({INAT_ID, USUA_ID, data}){
        return api.post(`investimento/ativo-tipo/${INAT_ID}?usuario=${USUA_ID}`, data)
      },
      del({INAT_ID}){
        return api.get(`investimento/ativo-tipo/delete/${INAT_ID}`)
      }
    },


    tipo: {
      get({USUA_ID}){
        return api.get(`investimento/tipo?usuario=${USUA_ID}`)
      },
      post({USUA_ID, data}){
        return api.post(`investimento/tipo?usuario=${USUA_ID}`, data)
      },
      put({INAT_ID, USUA_ID, data}){
        return api.post(`investimento/tipo/${INAT_ID}?usuario=${USUA_ID}`, data)
      },
      del({INAT_ID}){
        return api.get(`investimento/tipo/delete/${INAT_ID}`)
      }
    },
  },

  cofre: {
    carteira: {
      get({USUA_ID}){
        return api.get(`cofre/carteira?usuario=${USUA_ID}`)
      },
      post({USUA_ID, data}){
        return api.post(`cofre/carteira?usuario=${USUA_ID}`, data)
      },
      put({COCT_ID, USUA_ID, data}){
        return api.post(`cofre/carteira/${COCT_ID}?usuario=${USUA_ID}`, data)
      },
      del({COCT_ID}){
        return api.get(`cofre/carteira/delete/${COCT_ID}`)
      }
    },
  },

  usua: {
    dados: {
      put({USUA_ID, data}){
        return api.post(`usuario/${USUA_ID}`, data)
      }
    }
  },
}