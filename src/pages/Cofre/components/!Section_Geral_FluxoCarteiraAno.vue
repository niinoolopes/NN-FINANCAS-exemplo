<template>
  <PageSection class="opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

    <PageContentTitle titulo="Saldo e Fluxo do Ano">

      <template v-slot:btn>
        <button 
          type="button" 
          class="btn-hover btn btn-sm btn-outline-info py-0" 
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageContentTitle>

    <GraficoLine 
      :arrLabels="arrMeses"
      :arrDados="arrDados" 
      color="cofre"
      :style="{height: '225px'}"
    />

  </PageSection>
</template>

<script>
  import {mapState} from 'vuex';
  import service from '@/service.js'

  export default {
    computed: { ...mapState(['cofre','periodo']) },
    
    components: { 
      GraficoLine: () => import('@/components/Grafico/Line-generico')
    },

    data() {
      return {
        time: false,
        arrMeses: [],
        arrDados: [
          {label: 'SALDO',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'ENTRADA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'RETIRADA', valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
        ]
      }
    },

    created () {
      this.arrMeses = service.arrMeses;
    },
    
    mounted () {
      if( !this.$store.getters.C_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.arrDados = [
          {label: 'SALDO',    valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'ENTRADA',  valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
          {label: 'RETIRADA', valores:[0,0,0,0,0,0,0,0,0,0,0,0]},
        ]
        this.time = false

        setTimeout(() => {

          var endPoint = "";
          endPoint += "carteira-fluxo-ano";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&COCT_ID=${this.$store.getters.C_COCT_ID}`;
          endPoint += `&periodo=${this.$store.getters.Periodo}`;
          
          service.cofre.busca(endPoint).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              const {SALDO, ENTRADA, RETIRADA} = data
              this.arrDados[0].valores = SALDO
              this.arrDados[1].valores = ENTRADA
              this.arrDados[2].valores = RETIRADA

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

            this.time = true;
          });
        }, service.timeLoading);
      },
    },
  
    watch: {
      'cofre.COCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      }
    },
  }
</script>