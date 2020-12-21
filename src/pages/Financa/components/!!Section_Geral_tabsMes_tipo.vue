<template>
  <PageSection css="article-limit">
    <PageContentTitle :titulo="tipo">
      <template v-slot:btn>
        <button 
          type="button" 
          class="btn-hover btn btn-sm btn-outline-info py-0 mr-2" 
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageContentTitle>
    
    <PageContentNav>
      <template v-slot:menu>
        <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
          <a class="nav-item nav-link active" data-toggle="tab" :href="`#grupo-${tipo}`">Grupos</a>
          <a class="nav-item nav-link"        data-toggle="tab" :href="`#categoria-${tipo}`">Categorias</a>
        </div>
      </template>

      <!-- <template v-slot:btn></template> -->
    </PageContentNav>

    <div class="tab-content" id="nav-tabContent">

      <div class="pt-2 tab-pane fade show active" :id="`grupo-${tipo}`" role="tabpanel" aria-labelledby="grupo-tab">
        <keep-alive>
          <TabsGeral :time="time" :tipo="tipo" :trs="dados.FIGP" :grafico="dados.FIGP_GRAFICO" dados="Grupo" />
        </keep-alive>
      </div>

      <div class="pt-2 tab-pane fade" :id="`categoria-${tipo}`" role="tabpanel" aria-labelledby="categoria-tab">
        <keep-alive>
          <TabsGeral :time="time" :tipo="tipo" :trs="dados.FICT" :grafico="dados.FICT_GRAFICO" dados="Categoria" />
        </keep-alive>
      </div>
    </div>
  </PageSection>
</template>

<script>

  import {mapState} from 'vuex';
  import service from "@/service.js"

  export default {
    props: ['tipo'],
    
    components: { 
      TabsGeral: () => import('../components/Tabs_Geral'),
     },

    computed: { ...mapState(['financa','periodo']) },

    data() {
      return {
        dados: {
          FIGP: [],
          FIGP_GRAFICO: { label:[], valores:[] },
          FICT: [],
          FICT_GRAFICO: { label:[], valores:[] },
        },
        time: false,
      }
    },

    mounted () {
      this.getDados()
    },

    methods: {
      getDados() {
        this.time = false

        var FITP_ID = (this.tipo == 'Receita') ? 1 : 2;

        setTimeout( () => {

          var endPoint = '';
          endPoint += 'mes-geral';
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&mes=${this.$store.getters.Periodo}`;
          endPoint += `&FINC_ID=${this.$store.getters.F_FINC_ID}`;
          endPoint += `&FITP_ID=${FITP_ID}`;

          service.finc.busca(endPoint).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              if(FITP_ID == 1) {
                this.dados = data.RECEITA
              }
              else if( FITP_ID == 2) {
                this.dados = data.DESPESA
              }
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
      }
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