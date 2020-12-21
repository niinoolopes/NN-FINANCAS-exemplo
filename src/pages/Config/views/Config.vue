<template>
  <TemplateDefault title="Configuração">

    <div v-for="item in menu" :key="item.text" class="col-6 col-sm-4 col-md-3 col-lg-2 p-1">
      <router-link class="hover-none text-muted p-3 d-block bg-white border" :to="item.to">
        <i class="py-2 fa-3x" :class="item.icon"></i>
        <p class="m-0 text-center">{{item.text}}</p>
      </router-link>
    </div> 
    
  </TemplateDefault> 
</template>

<script>
  export default {
    name: 'Config',

    data() {
      return {
        menu: [
          { 
            to: {name: 'ConfigUsuario'},
            icon: 'fas fa-user-cog',
            text: 'Usuário'
          },
          { 
            to: {name: 'ConfigFinanca'},
            icon: 'fas fa-donate',
            text: 'Finanças'
          },
          { 
            to: {name: 'ConfigInvestimento'},
            icon: 'fas fa-landmark',
            text: 'Investimentos'
          },
          { 
            to: {name: 'ConfigCofre'},
            icon: 'fas fa-piggy-bank',
            text: 'Cofrinho'
          },
          { 
            to: {name: 'ConfigRelatorio'},
            icon: 'fas fa-file-excel',
            text: 'Relatórios'
          },
        ]
      }
    },
  }
</script>

<style scoped>
  i {
    display: block;
    width: min-content;
    margin: auto;
  }
</style>