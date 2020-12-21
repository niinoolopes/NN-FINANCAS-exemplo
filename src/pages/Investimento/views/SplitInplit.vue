<template>
  <TemplateDefault title="Split / inplit">
    
    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar as ordens!"/>

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
        tipo: `modal`,
        target: `InvestimentoModal-Split`,
        novo: `novo`,
        setItem: setItem
      }'
    />

    <PageSection>
      <PageContentNav>
        <template v-slot:menu>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.INAT" @change="setFiltro">
              <option value="">Tipo de Ativo</option>
              <option v-for="(e,i) in itemsFiltro.INAT" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.INAV" @change="setFiltro">
              <option value="">Ativo</option>
              <option v-for="(e,i) in itemsFiltro.INAV" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.TIPO" @change="setFiltro">
              <option value="">Split / Inplit</option>
              <option v-for="(e,i) in itemsFiltro.INAS_TIPO" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.QUANTIDADE" @change="setFiltro">
              <option value="">Quantidade</option>
              <option v-for="(e,i) in itemsFiltro.INAS_QUANTIDADE" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
          <Column class="col-6 col-sm-4 col-md-2">
            <select class="form-control form-control-sm m-0" v-model="camposFiltro.DATA" @change="setFiltro">
              <option value="">Data</option>
              <option v-for="(e,i) in itemsFiltro.INAS_DATA" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
            </select>
          </Column>
        </template>

        <template v-slot:btn>
          <ButtonClearDados class="mb-1 mr-1 m-md-0" :limpar='clearFiltro' />
        </template>
      </PageContentNav>
    
      <TableSplit :items='itemsTable' :time="time" :setItem="setItem" />
    </PageSection>

    <ModalSplit :INAS="INAS" :setItem="setItem" />

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: { 
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      TableSplit:        () => import('@/pages/Investimento/components/Table_Split'),
      ModalSplit:        () => import('@/pages/Investimento/components/Modal_Split'),
      ButtonClearDados:  () => import('@/components/Button_clearDados')
    },

    data() {
      return {
        INAS : 'init',
        camposBuscaBoolean : true,
        camposBusca: {
          itemsTipo: [
            {value:'mes', label: 'Mês'},
            {value:'movimentacao', label: 'Movimentação'},
            {value:'historico', label: 'Historico'},
            {value:'inativo', label: 'Inativo'},
            {value:'split', label: 'Split'},
            {value:'inplit', label: 'Inplit'},
          ],
          tipo: 'mes',
          limit: '',
          dataDe: '',
          dataAte: '',
        },
        itemsFiltro: [],
        camposFiltro: {
          INTP: '',
          INAT: '',
          INAV: '',
          TIPO: '',
          QUANTIDADE: '',
          DATA: '',
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
        url += `&retorno=I_ativoSplit`

        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&INAS_STATUS=1`
          url += `&data=${this.$store.getters.Periodo}`
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&INAS_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INAS_DATA:DESC|INAS_ID:DESC`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&INAS_STATUS=1`
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
          url += `&orderby=INAS_ID:ASC`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&INAS_STATUS=0`;
        }
        if(this.camposBusca.tipo == 'split') {
          this.camposBuscaBoolean = true
          url += `&INAS_STATUS=1`
          url += `&INAS_TIPO=S`
        }
        if(this.camposBusca.tipo == 'inplit') {
          this.camposBuscaBoolean = true
          url += `&INAS_STATUS=1`
          url += `&INAS_TIPO=I`
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
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.I_ativoSplit
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
      setItem(INAS){
        this.INAS = INAS
        if(INAS == 'getDados') this.getDados()
      },

      // FILTRO ITEMS
      setFiltro() {
        this.itemsTable = service.invest.filtroSplit(this.camposFiltro, this.items)
      },

      clearFiltro() {
        this.itemsTable   = this.items
      },
    },

    watch: {
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },
  }
</script>