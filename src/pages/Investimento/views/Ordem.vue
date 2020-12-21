<template>
  <TemplateDefault title="Ordens">
    
    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar os rendimentos!"/>

    <!-- 
    <PageNav>
      <template v-slot:menu> </template> 
      <template v-slot:btn> </template>
    </PageNav> 
    -->

    <PageContentFiltro 
      :changeBusca='changeBusca'
      :camposBusca='camposBusca'
      :camposBuscaBoolean='camposBuscaBoolean'
      :btn_getDados='getDados'
      :btn_plus='{
        tipo: `link`,
        to: `InvestimentoOrdemAdm`,
        disabled: !$store.getters.I_CarteiraPainel
      }'
    />

    <PageSection>
      <PageContentNav>
        <template v-slot:menu>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.INCR_ID" @change="setFiltro">
              <option value="">Corretora</option>
              <option v-for="(e,i) in itemsFiltro.INCR" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.INOD_DATA" @change="setFiltro">
              <option value="">Situação</option>
              <option v-for="(e,i) in itemsFiltro.INOD_DATA" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
          <Column class="col-6 col-sm-4 col-md-2">
            <input class="form-control form-control-sm m-0" type="text" v-model="camposFiltro.OBS" @keyup="setFiltro" >
          </Column>
        </template>

        <template v-slot:btn>
          <ButtonClearDados class="mb-1 mr-1 m-md-0" :limpar='clearFiltro' />
        </template>
      </PageContentNav>

      <TableOrdem :items='itemsTable' :time='time' />
    </PageSection>

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {

    components: {
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      ButtonClearDados:  () => import('@/components/Button_clearDados'),
      TableOrdem:        () => import('@/pages/Investimento/components/Table_Ordem')
    },

    data() {
      return {
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
        itemsFiltro: [],
        camposFiltro: {
          INCR_ID: '',
          INOD_DATA: '',
          OBS: '',
        },
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
        url += '&retorno=I_ordem'

        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&INOD_STATUS=1`
          url += `&data=${this.$store.getters.Periodo}`
          url += `&orderby=INOD_DATA:DESC|INOD_ID:ASC`
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&INOD_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INOD_DATA:DESC|INOD_ID:ASC`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&INOD_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INOD_ID:ASC`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&INOD_STATUS=0`;
          url += `&orderby=INOD_DATA:DESC|INOD_ID:ASC`
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
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.I_ordem
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

    },

    watch: {
      '$store.getters.I_INCT_ID'(newValue, oldValue) {
        this.getDados()
      },
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>