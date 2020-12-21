import servece from './service.js'
import config from '../config'

// --

const baseURL = config.baseURL

// -- FUNCTIONS

const makeFormData = (dados) => {

  let arrDados = Object.entries(dados)
  let data = new FormData();

  arrDados.map( item => {
    var key = item[0]
    var val = item[1]
    data.append(key, val)
  })

  return data;
}

const Header = () => {
  var data = servece.token.get() || '' ;
  var token = (data.length == '') ? '' : data.token

  return   {
    // "Content-Type": "application/json",
    "Authorization": token,
  };
}

// --


export default {
  async post(endPoint, DATA) {

    const options = {}
    options.method ='POST'
    options.body   = makeFormData(DATA)

    if( endPoint != 'login') {
      options.headers = Header();
    }

    return await fetch(`${baseURL}/${endPoint}`, options).then( r => r.json())
  },

  async get(endPoint) {
    
    const options = {
      method:'GET',
      headers: Header(),
    };

    return await fetch(`${baseURL}/${endPoint}`, options).then( r => r.json())
  },
  
  async put(endPoint, DATA) {
    
    const options = {
      method:'POST',
      headers: Header(),
      body: makeFormData(DATA)
    };

    return await fetch(`${baseURL}/${endPoint}`, options).then( r => r.json())
  },

  async delete(endPoint) {
    
    const options = {
      method:'GET',
      headers: Header(),
    };

    return await fetch(`${baseURL}/${endPoint}`, options).then( r => r.json())
  },
}