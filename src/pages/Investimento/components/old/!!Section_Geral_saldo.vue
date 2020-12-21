<template>
  <PageSection>

    <PageContentTitle titulo="Saldo">
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' border='0px' />
      </template>
    </PageContentTitle>
    
    <Cards :time="time" :labels="labels" :valores="valores" />

  </PageSection>
</template>

<script>
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    computed: { ...mapState(['investimento','periodo']) },

    components: {
      ButtongetDados: () => import('@/components/Button_getDados'),
      Cards:          () => import('@/components/Cards'),
    },

    data() {
      return {
        time: false,
        labels: ['Val. Aplicado','Val. Atual', 'Rendimentos','Dividendos'],
        valores: [0,0,0,0],
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {
        this.time = false;
        this.valores = [0,0,0,0]

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'carteira-saldo';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&dataAte=${this.$store.getters.Periodo}`;
          endPoint += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;

          service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              const {TOTAL, BRUTO, TOTAL_DIVIDENDO, TOTAL_JSCP, TOTAL_RENDIMENTO} = data
              
              this.valores[0] = Number(TOTAL);
              this.valores[1] = Number(BRUTO);
              this.valores[2] = Number(TOTAL_RENDIMENTO);
              this.valores[3] = Number(TOTAL_DIVIDENDO) + Number(TOTAL_JSCP);
            }
            else if(STATUS == 'error'){
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
      'investimento.INCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      }
    },
  }
</script>