import api from '@/api.js'

import Cofre from './pages/Cofre/service'
import Login from './pages/Login/service'
import Config from './pages/Config/service'
import Financa from './pages/Financa/service'
import Investimento from './pages/Investimento/service'

export default {

  cofre: Cofre,

  login: Login,

  config: Config,

  finc: Financa,

  invest: Investimento,

  // funções gerais
  busca(url){
    return api.get(`busca/${url}`)
  },

  closeModal: (id) => {
    setTimeout(() => {
      document.getElementById(id).click()
    }, 500);
  },

  initForm(campos){
    campos.forEach( (id, i) => {
      let modal = `form-${id}`
      if(document.getElementById(modal)){
        document.getElementById(modal).classList.remove('border-danger')
        document.getElementById(modal).classList.remove('border-success')
      }
    })
  },

  checkForm(data){
    var check = []
    let checkItems = Object.entries(data)

    checkItems.forEach( (item, i) => {
      let id = item[0]
      let conteudo = item[1]
      let modal = `form-${id}`
      let value = document.getElementById(modal).value

      if(conteudo == "" || conteudo == undefined || value == ''){
        check[i] = id
        document.getElementById(modal).classList.add('border-danger')
        document.getElementById(modal).classList.remove('border-success')
        
      }else{
        check[i] = ''
        document.getElementById(modal).classList.remove('border-danger')
        document.getElementById(modal).classList.add('border-success')
      }
    })
    return check.filter( item => item ).length == 0 ? true : false
  },

  token: {
    get(){
      return JSON.parse( localStorage.getItem("nn-crm-token") )
    },
    set(data){
      localStorage.setItem("nn-crm-token", JSON.stringify(data) );
    },
  },

  diaHoje(){
    var _d = new Date();
    return `${_d.getDate()}`.length == 1 ? `0${_d.getDate()}` : `${_d.getDate()}`;
  },

  dataHoje(){
    var _d = new Date();

    var dia = `${_d.getDate()}`.length == 1 ? `0${_d.getDate()}` : `${_d.getDate()}`
    var mes = `${_d.getMonth() + 1}`.length == 1 ? `0${_d.getMonth() + 1}` : `${_d.getMonth() + 1}`
    var ano = `${_d.getFullYear()}`

    return `${ano}-${mes}-${dia}`;
  },

  arrMeses: [
    "Janeiro",
    "Fevereiro",
    "Março",
    "Abril",
    "Maio",
    "Junho",
    "Julho",
    "Agosto",
    "Setembro",
    "Outubro",
    "Novembro",
    "Dezembro"
  ],

  arrColors: [
    'rgba(40, 167, 69, 0.9)',
    'rgba(40, 100, 100, 0.9)',
    'rgba(100, 50, 69, 0.9)',
  ],

   arrMessage: [
    'Token expirado, faça login novamente!'
  ],

  timeLoading: 500,
  timeLoading150: 150,

  tableFake(arrDados) {
    if( Array.from(arrDados).length == 0) return []

    return Array.from(arrDados).map( item => {
      var fake = {};
      var keys = Object.keys(item);

      for (let i = 0; i < keys.length; i++) {
        var txt = keys[i]

        if( txt.indexOf('_ID') >= 0 ){
          fake[txt] = ' '

        } else if(txt.indexOf('_VALOR') >= 0 || txt.indexOf('_QUANTIDADE') >= 0 || txt.indexOf('_DATA') >= 0 ) {
          fake[txt] = '_ _ _'

        } else {
          var tmp = ''
          for (let i = 0; i <= txt.length / 1.80; i++) tmp += '_ '
          
          fake[txt] = tmp
        }
      }
      return fake
    })
  },
}