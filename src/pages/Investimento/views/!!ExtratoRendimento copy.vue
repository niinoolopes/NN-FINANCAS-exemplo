<template>
  <TemplateDefault title="Extrato rendimentos" >
    
    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar os rendimentos!"/>

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
        target: `InvestimentoModal-Rendimento`,
        novo: `novo`,
        setItem: setItem
      }'
    />

    <PageSection>
      <FiltroRendimento :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' />

      <TableRendimento :items="itemsTable" :setItem="setItem" :time="time" />
    </PageSection>
    
    <ModalRendimento :INAR="INAR" :setItem="setItem" />

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: {
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      FiltroRendimento:  () => import('@/pages/Investimento/components/Section_filtro_rendimento'),
      TableRendimento:   () => import('@/pages/Investimento/components/Table_Rendimento'),
      ModalRendimento:   () => import('@/pages/Investimento/components/Modal_Rendimento'),
    },

    data() {
      return {
        INAR : 'init',
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
        camposFiltro: {
          INCR_ID: '',
          INTP_ID: '',
          INAT_ID: '',
          INAV_ID: '',
          INAR_TIPO: '',
          INAR_VALOR: '',
          INAR_DATA: ''
        },
        time: false,
        items: [],
        itemsTable: [],
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      makeUrl() {

        var url = ''
        url += 'lista';
        url += `?usuario=${this.$store.getters.USUA_ID}`;
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
        url += `&retorno=I_rendimento`;

        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&INAR_STATUS=1`;
          url += `&data=${this.$store.getters.Periodo}`
          url += `&orderby=INAR_DATA:DESC`;
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&INAR_STATUS=1`;
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INAR_DATA:DESC`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&INAR_STATUS=1`;
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INAR_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&INAR_STATUS=0`;
          url += `&orderby=INAR_DATA:ASC`;
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
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.I_rendimento
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

      // FILTRO ITEMS
      setFiltro(arrFiltro) {
        this.camposFiltro = arrFiltro;
        this.itemsTable = service.invest.filtroRendimentos(this.camposFiltro, this.items)
      },

      clearFiltro() {
        this.camposFiltro = {}
        this.itemsTable   = this.items
      },

      // MODAL 
      setItem(INAR){
        this.INAR = INAR
        if(INAR == 'getDados') this.getDados()
      },
    },

    watch: {
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },

  }
</script>