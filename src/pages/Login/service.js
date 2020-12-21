import api from '@/api.js'

export default {
  login(form) {
    return api.post('login', form)
  },
  remake({mes}){
    return api.get(`login-remake?mes=${mes}`)
  }
}