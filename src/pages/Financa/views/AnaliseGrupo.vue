<template>
  <TemplateDefault title="Análise Grupo/Categoria">

    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar o extrato!"/>

    <PageNav>
      <template v-slot:menu>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 p-0 pr-1 mb-1 m-sm-0">
          <select class="form-control form-control-sm m-0" v-model="FIGP_ID" @change="getCategoria">
            <option value="">Selecione...</option>
            <option v-for="(g,i) in arrGrupos" :key="i" :value="g.FIGP_ID">{{g.FIGP_DESCRICAO}}</option>
          </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-3 p-0 pr-1 mb-1 m-sm-0">
          <select class="form-control form-control-sm m-0" v-model="FICT_ID" @change="getDados">
            <option value="all">Todos</option>
            <option v-for="(c,i) in arrCategorias" :key="i" :value="c.FICT_ID">{{c.FICT_DESCRICAO}}</option>
          </select>
        </div>
      </template>

      <template v-slot:btn>
        <button 
          type="button"
          class="btn-hover btn btn-sm btn-outline-info py-0 mr-1"
          @click="getDados"><i class="fas fa-sync"></i></button>
      </template>
    </PageNav>


    <PageSection>
      <PageContentNav>
          <template v-slot:menu>
            <div class="nav nav-tabs d-flex over-x mb-1 mb-md-0" id="nav-tab" role="tablist">
              <a class="nav-item nav-link active" data-toggle="tab" href="#tab-analise">Analise</a>
              <a class="nav-item nav-link"        data-toggle="tab" href="#tab-registro">Registros</a>
            </div>
          </template>

          <template v-slot:btn></template>
      </PageContentNav>

      <div class="tab-content opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
        <div class="tab-pane fade show active" id="tab-analise">

          <div class="d-flex flex-wrap">
            <div class="col-12 col-lg-3 p-0 pr-2 mb-3">
              <TableMeses :items="registros.mesesTable" />
            </div>
            <div class="col-12 col-lg-9 p-0 pl-2 mb-3">
              <GraficoLine :arrLabels="arrMeses" :arrDados="registros.mesesGrafico" 
              />
            </div>
          </div>

          <TableAnos :items="registros.tableAnos" />
        </div>

        <div class="tab-pane fade" id="tab-registro">
          <article v-for="(item, i) in registros.itemsAno" :key="i">

            <h3 class="border-bottom" data-toggle="collapse" :data-target="`#multiCollapseExample2-${item.MES}`" >{{item.MES}}</h3>

            <div class="mb-3 collapse multi-collapse shadow-sm" :id="`multiCollapseExample2-${item.MES}`">
              
              <TableExtrato 
                time="1"
                :items="item.LISTA"
                :setItem="setItem" />

            </div>
          </article>

          <ModalExtrato :FNIT="FNIT" :setItem="setItem"/>
          
        </div>

      </div>
    </PageSection>
     
  </TemplateDefault>
</template>

<script>
  
  import service from '@/service.js'
  import { mapState } from 'vuex'

  export default {
    components: { 
      TableMeses:   () => import('@/pages/Financa/components/Table_analiseGrupo_meses'),
      TableAnos:    () => import('@/pages/Financa/components/Table_analiseGrupo_anos'),
      GraficoLine:  () => import('@/components/Grafico/Line-generico'),
      TableExtrato: () => import("@/pages/Financa/components/Table_Extrato.vue"),
      ModalExtrato: () => import("@/pages/Financa/components/Modal_Form_Item.vue"),
    },

    computed: { ...mapState(['financa','periodo']) },

    data() {
      return {
        registros: {
          tableAnos:  [],
          mesesTable: [],
          mesesGrafico: [],
          itemsAno: [],
        },
        arrMeses: [],

        arrGrupos: [],
        arrCategorias: [],

        FIGP_ID: true,
        FICT_ID: 'all',

        time: false,
        FNIT: 'init',
      }
    },

    created () {
      this.arrMeses = service.arrMeses;
    },
    
    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){

        if(this.FIGP_ID === true) {

          if(this.$route.params.idFINC) {
            this.arrGrupos = this.$store.getters.F_GruposAtivos( this.$route.params.idFINC )
            this.FIGP_ID = this.$route.params.idFIGP

            if(this.$route.params.idFICT) {
              this.FICT_ID = this.$route.params.idFICT
            }

          } else {
            this.arrGrupos = this.$store.getters.F_GruposAtivos( this.$store.getters.F_FINC_ID )
            this.FIGP_ID = this.arrGrupos[0].FIGP_ID
          }

          this.getCategoria()
          this.getDados()
        }
      }
    },

    methods: {
      getCategoria(){
        this.arrCategorias = this.$store.getters.F_CategoriasAtivas( this.FIGP_ID ) 
      },

      // --

      makeUrl() {
        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&FIGP_ID=${this.FIGP_ID}`
        if(this.FICT_ID != 'all') url += `&FICT_ID=${this.FICT_ID}`;
        url += `&data=${this.$store.getters.Periodo}`;
        url += `&retorno=F_analiseGrupo`;
        return url
      },

      getDados(){
        this.time = false;

        setTimeout(() => {
          service.busca( this.makeUrl() )
          .then( ({STATUS, data, msg}) => {
            if (STATUS == "success") {
              
              const {analiseAno, analiseAnoGrafico, analise5Anos, itemsLista} = data.F_analiseGrupo
              
              this.registros.mesesTable   = analiseAno;
              this.registros.tableAnos    = analise5Anos;
              this.registros.mesesGrafico = analiseAnoGrafico;

              this.registros.itemsAno     = itemsLista.map( i => {
                return {
                  MES: i.MES,
                  LISTA: service.finc.formatItemsToTable( i.LISTA )
                }
              })


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

      setItem(FNIT){
        this.FNIT = FNIT
      },
    },
    
    watch: {
      'financa.FINC_ID'(newValue, oldValue) {
        this.getDados()
      },
      'periodo'(newValue, oldValue) {
        this.getDados()
      },
    },

  }
</script>