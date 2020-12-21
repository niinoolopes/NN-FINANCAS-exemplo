<template>
  <form class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
    <div class="row">

      <div class="px-1 px-md-3 mb-2 form-group col-12 col-lg-3">
        <label for="form-USUA_NOME">Nome</label>
        <input class="form-control form-control-sm" type="text" id="form-USUA_NOME" v-model="dadosUsuario.USUA_NOME">
      </div>

      <div class="px-1 px-md-3 mb-2 form-group col-12 col-lg-3">
        <label for="form-USUA_EMAIL">Email</label>
        <input class="form-control form-control-sm" type="email" id="form-USUA_EMAIL" v-model="dadosUsuario.USUA_EMAIL">
      </div>
      
      <div class="px-1 px-md-3 mb-2 col-12 col-lg-3">
        <label for="form-USUA_SENHA">Senha</label>
        <input class="form-control form-control-sm" type="password" id="form-USUA_SENHA" v-model="dadosUsuario.USUA_SENHA"
          placeholder="Alterar Senha">
      </div>

    </div>

    <hr>

    <button type="button" class="btn btn-sm btn-outline-info mr-2" @click="salvar">Salvar</button>
  </form>
</template>

<script>
  import service from "@/service.js"
  import {mapState} from 'vuex'

  export default {

    computed: { ...mapState(['usuario']) },

    data() {
      return {
        time: true,
        icon: true,
        dadosUsuario: {},
      }
    },

    mounted () {
      this.dadosUsuario = this.usuario
    },

    methods: {
      salvar() {
        this.time = false

        let data = {}
        data.USUA_NOME  = this.dadosUsuario.USUA_NOME
        data.USUA_EMAIL = this.dadosUsuario.USUA_EMAIL
        data.USUA_SENHA = this.dadosUsuario.USUA_SENHA

        let option = {}
        option.USUA_ID = this.usuario.USUA_ID
        option.data = data

        let checkForm = {}
        checkForm.USUA_NOME  = this.dadosUsuario.USUA_NOME
        checkForm.USUA_EMAIL = this.dadosUsuario.USUA_EMAIL

        setTimeout( () => {
          if(service.checkForm(checkForm)){

            service.config.usua.dados.put(option).then( ({STATUS, data, msg}) => {

              if(STATUS == 'success'){
                this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Dados atualizados!' }) // message

                service.initForm([
                'USUA_NOME',
                'USUA_EMAIL',
                ])
              } 
              else if (STATUS == "erro") {
                this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });
              } 
              else if (STATUS == "token") {
                this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                this.$store.commit("SET_LOGIN", false);
              }

            })

            // this.dadosUsuario.USUA_NOME  = data.USUA_NOME
            // this.dadosUsuario.USUA_EMAIL = data.USUA_EMAIL
            this.dadosUsuario.USUA_SENHA = ''
            this.time = true
          }
        }, service.timeLoading)
      },

    }
  }
</script>

<style scoped>

</style>