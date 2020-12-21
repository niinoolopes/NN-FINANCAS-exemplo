<template>
  <TemplateDefault title="Extrato" >

    <Alert v-if="$store.getters.C_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar o extrato!"/>

    <PageContentFiltro 
      :changeBusca='changeBusca'
      :camposBusca='camposBusca'
      :camposBuscaBoolean='camposBuscaBoolean'
      :btn_getDados='getDados'
      :btn_plus='{
        tipo: `modal`,
        target: `CofreModal-item`,
        novo: `novo`,
        setItem: setItem
      }'
    />

    <PageSection>
      <TableExtrato :time="time" :items="itemsTable" :setItem="setItem" />
    </PageSection>

    <ModalCofreItem :COCT="COCT" :setItem="setItem"/>

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'
  
  export default {

    components: { 
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      TableExtrato:      () => import('@/pages/Cofre/components/Table_extrato'), 
      ModalCofreItem:    () => import('@/pages/Cofre/components/Modal_Form_item'),
    },

    data() {
      return {
        COCT : 'init',
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
        url += `&retorno=C_itemsExtrato`

        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&COIT_STATUS=1`
          url += `&data=${this.$store.getters.Periodo}`
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&COIT_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=COIT_DATA:DESC|COIT_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&COIT_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=COIT_ID:ASC`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&COIT_STATUS=0`;
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
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.C_itemsExtrato
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
      setFiltro(campos) {
        this.camposFiltro = campos
        this.itemsTable = service.invest.filtroSplit(campos, this.items)
      },

      clearFiltro() {
        this.camposFiltro = {}
        this.itemsTable   = this.items
      },

      // MODAL 
      setItem(COCT){
        this.COCT = COCT
        if(COCT == 'getDados') this.getDados()
      },
    },

    watch: {
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>
