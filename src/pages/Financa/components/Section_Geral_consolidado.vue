<template>
  <PageSection>

    <PageContentTitle titulo="Consolidado Mês">
      <template v-slot:btn>
        <button 
          type="button" 
          class="btn-hover btn btn-sm btn-outline-info py-0" 
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageContentTitle>

    <Cards :time="time" :labels="labels" :valores="valores" />
   
  </PageSection>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    computed: { ...mapState(['financa','periodo']) },

    components: {
      Cards: () => import('@/components/Cards'),
    },

    data() {
      return {
        time: false,
        labels: ['RECEITA', 'DESPESA', 'SOBRA', 'ESTIMADO'],
        valores: [0,0,0,0],
      }
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados(){
        this.time = false;
        this.valores = [0,0,0,0]

        setTimeout( () => {
          
          var endPoint = '';
          endPoint += 'mes-consolidado-valores';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&mes=${this.$store.getters.Periodo}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;

          service.finc.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              const {RECEITA, DESPESA, SOBRA, ESTIMADO} = data;
              
              this.valores[0] = RECEITA;
              this.valores[1] = DESPESA;
              this.valores[2] = SOBRA;
              this.valores[3] = ESTIMADO;
            }
            else if(STATUS == 'erro'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }

            this.time = true
          })
        }, service.timeLoading)
      },
    },

    watch: {
      'financa.FINC_ID'(newValue, oldValue) {
        this.getDados();
      },
      'periodo'(newValue, oldValue) {
        this.getDados();
      }
    },
  }
</script>