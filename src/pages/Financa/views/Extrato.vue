<template>
  <TemplateDefault title="Extrato" >
    
    <Alert v-if="$store.getters.F_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar o extrato!"/>

    <!-- 
    <PageNav>
      <template v-slot:menu></template>
      <template v-slot:btn></template>
    </PageNav>
    -->

    <PageContentFiltro 
      :changeBusca='changeBusca'
      :camposBusca='camposBusca'
      :camposBuscaBoolean='camposBuscaBoolean'
      :btn_getDados='getDados'
      :btn_plus='{
        tipo: `modal`,
        target: `InvestimentoModal-Split`,
        novo: `novo`,
        setItem: setItem
      }'
    />

    <PageSection>
      <Filtro :itemsFiltro="itemsFiltro" :setFiltro="setFiltro"/>

      <TableExtrato :time="time" :items="itemsTable" :setItem="setItem" />
    </PageSection>

    <ModalExtrato :FNIT="FNIT" :setItem="setItem"/>

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {

    components: {
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      Filtro:            () => import('@/pages/Financa/components/Section_Filtro_table'),
      TableExtrato:      () => import("@/pages/Financa/components/Table_Extrato"),
      ModalExtrato:      () => import("@/pages/Financa/components/Modal_Form_Item.vue"),
    },

    data() {
      return {
        FNIT: 'init',
        camposBuscaBoolean : true,
        camposBusca: {
          itemsTipo: [
            {value:'mes', label: 'Mês'},
            {value:'movimentacao', label: 'Movimentação'},
            {value:'historico', label: 'Historico'},
            {value:'inativo', label: 'Inativo'},
          ],
          tipo: 'mes',
          limit: '',
          dataDe: '',
          dataAte: '',
        },
        itemsFiltro: [],
        camposFiltro: {},
        time: false,
        items: [],
        itemsTable: [],
      }
    },

    mounted () {
      if( !this.$store.getters.F_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      makeUrl() {
        var url = ''
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&retorno=F_itemsExtrato`
        
        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&FNIT_STATUS=1`
          url += `&data=${this.$store.getters.Periodo}`
          url += `&orderby=FNIT_DATA:DESC|FNIT_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&FNIT_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=FNIT_DATA:DESC|FNIT_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&FNIT_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=FNIT_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&FNIT_STATUS=0`;
        }
        return url
      },

      getDados(){
        this.time = false
        this.items = [];
        this.itemsTable = 'init';

        setTimeout( () => {
          service.busca( this.makeUrl() )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.F_itemsExtrato
              this.items = items
              this.itemsTable = items
              this.itemsFiltro = itemsFiltro
              if(limit) this.camposBusca.limit = limit
              if(dataDe) this.camposBusca.dataDe = dataDe
              if(dataAte) this.camposBusca.dataAte = dataAte
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

      changeBusca(objBusca){
        this.camposBusca = objBusca;
        this.getDados()
      },

      // MODAL 
      setItem(FNIT){
        this.FNIT = FNIT
        if(FNIT == 'getDados') this.getDados()
      },

      // FILTRO ITEMS
      setFiltro(campos) {
        this.itemsTable = service.finc.filtroItems(campos, this.items)
      },

      clearFiltro() {
        this.itemsTable   = this.items
      },
    },

  }
</script>