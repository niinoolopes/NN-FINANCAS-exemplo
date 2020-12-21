<template>
  <TemplateDefault title="Extrato operações" >
    
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
    />

    <PageSection>
      <FiltroOperacoes :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' />

      <TableOrdemAtivo :time="time" :setItem="setItem" :items="itemsTable" :btn="['modal','linkOrdem']" />
    </PageSection>

    <ModalOrdemAtivo :INOD_ID='false' :INIT='INIT' :setItem="setItem" />

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: {
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      FiltroOperacoes:   () => import('@/pages/Investimento/components/Section_filtro_operacao'),
      TableOrdemAtivo:   () => import('@/pages/Investimento/components/Table_Ordem_ativo'),
      ModalOrdemAtivo:   () => import('@/pages/Investimento/components/Modal_Ordem_ativo'),
    },

    data() {
      return {
        INIT : 'init',
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
        url += `&retorno=I_itemsOperacoes`;

        if(this.camposBusca.tipo == 'mes') {
          this.camposBuscaBoolean = true
          url += `&INIT_STATUS=1`;
          url += `&orderby=INOD_DATA:DESC|INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC`;
          url += `&data=${this.$store.getters.Periodo}`;
        }
        if(this.camposBusca.tipo == 'movimentacao') {
          this.camposBuscaBoolean = false
          url += `&INIT_STATUS=1`;
          url += `&orderby=INOD_DATA:DESC|INOD_ID:ASC|INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC`;
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
        }
        if(this.camposBusca.tipo == 'historico') {
          this.camposBuscaBoolean = false
          url += `&INIT_STATUS=1`;
          url += `&orderby=INIT_ID:DESC|INOD_ID:ASC|INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC`;
          url += `&dataDe=${this.camposBusca.dataDe}`;
          url += `&dataAte=${this.camposBusca.dataAte}`;
          url += `&limit=${this.camposBusca.limit}`;
        }
        if(this.camposBusca.tipo == 'inativo') {
          this.camposBuscaBoolean = true
          url += `&INIT_STATUS=0`;
          url += `&orderby=INOD_DATA:DESC|INOD_ID:ASC|INCR_DESCRICAO:ASC|INTP_DESCRICAO:ASC|INAT_DESCRICAO:ASC|INAV_CODIGO:ASC`;
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
              var {items, itemsFiltro, dataDe, dataAte, limit} = data.I_itemsOperacoes
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
        this.itemsTable = service.invest.filtroOperacoes(campos, this.items)
      },

      clearFiltro() {
        this.itemsTable   = this.items
      },

      // MODAL 

      setItem(INIT){
        this.INIT = INIT
        if(INIT == 'getDados') this.getDados()
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