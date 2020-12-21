
<template>
  <section>
    <PageContentFiltro 
      :changeBusca='changeBusca'
      :camposBusca='camposBusca'
      :camposBuscaBoolean='camposBuscaBoolean'
      :btn_getDados='getDados'
      :btn_plus='{
        tipo: `modal`,
        target: `InvestimentoModal-Cotacao`,
        novo: `novo`,
        setItem: setItem
      }'
    />

    <TableCotacao :items='itemsTable' :time="time" :setItem="setItem" />

    <ModalCotacao :INAC="INAC" :setItem="setItem" />
  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: { 
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      TableCotacao:      () => import('@/pages/Investimento/components/Table_Cotacao'),
      ModalCotacao:      () => import('@/pages/Investimento/components/Modal_Cotacao'),
    },

    data() {
      return {
        INAC: 'init',

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
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
        url += '&retorno=I_cotacao'

        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&INAC_STATUS=1`
          url += `&data=${this.$store.getters.Periodo}`
          url += `&orderby=INAC_DATA:DESC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC`
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&INAC_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INAC_DATA:DESC|INAC_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&INAC_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INAC_ID:ASC`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&INAC_STATUS=0`;
          url += `&orderby=INAC_DATA:DESC|INAC_ID:DESC`
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
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.I_cotacao
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

      setFiltro() {
        this.itemsTable = service.invest.filtroOrdem(this.camposFiltro, this.items)
      },

      clearFiltro() {
        this.camposFiltro = {}
        this.itemsTable   = this.items
      },

      setItem(INAC){
        this.INAC = INAC
        if(INAC == 'getDados') this.getDados()
      },
    },

    watch: {
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>