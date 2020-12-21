<template>
  <TemplateDefault>

    <div class="col-9 col-md-7 col-xl-5 mx-auto ">
      <div v-if="login == true">
        <LoadingContent />
      </div>
      
      <form id="form" class="login" v-else>
        <h1 class="display-3">Bem vindo!</h1>

        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" class="form-control form-control-sm" v-model="formUsuario.USUA_EMAIL">
        </div>

        <div class="form-group">
          <label for="senha">Senha</label>
          <input type="password" class="form-control form-control-sm" v-model="formUsuario.USUA_SENHA">
        </div>

        <button @click.prevent="sendForm" type="submit" class="btn btn-sm btn-outline-info">Entrar</button>
      </form>
    </div>

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: { 
      LoadingContent: () => import('@/components/Loading/LoadingContent.vue'),
     },

    data() {
      return {
        login: false,
        formUsuario: {
          USUA_EMAIL: 'niinoolopes0@gmail.com',
          USUA_SENHA: '',
          // USUA_EMAIL: '',
          // USUA_SENHA: '',
        }
      }
    },

    methods: {
      sendForm() {
        this.login = true

        setTimeout( () => {
          service.login.login(this.formUsuario).then( ({STATUS, data, msg}) => {

            if (STATUS == "success") {
              service.token.set({
                token: data.TOKEN,
                mes:   data.PERIODO,
              })
              this.$store.dispatch("SetData", data)

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: "Email ou Senha invalida, tente com dados corretos." })
              this.$store.commit("SET_LOGIN", false);
              this.login = false

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);
              this.login = false

            }
          })

        }, service.timeLoading)
      },
    },
    
  }
</script>

<style scoped>
  .login{
    margin-top: 20vh;
  }
</style>