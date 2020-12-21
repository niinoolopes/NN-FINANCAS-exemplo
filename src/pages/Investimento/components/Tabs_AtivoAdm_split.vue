<template>
  <section class="w-100">

    <!-- <DateLimit :campos="campos" :setCampos="setCampos" :buscar="getDados" /> -->

    <!--
    <PageContentNav>
      <template v-slot:menu></template>
      <template v-slot:btn></template>
    </PageContentNav> 
    -->
<!-- 
    <TableCotacao
      :items='itemsTable'
      :time="time"
      :setItem="setItem" 
    /> -->

    <!-- <ModalCotacao 
      :INAC="itemModal" 
      :setItem="setItem"
    /> -->


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
      <TableSplit :items='itemsTable' :time="time" :setItem="setItem" />
    </PageSection>

    <ModalSplit :INAS="INAS" :setItem="setItem" />

  </section>
</template>

<script> 
  
  import {mapState} from 'vuex'
  import service from '@/service.js'

  export default {
    props: ['INAV_ID'],

    computed: { ...mapState(['investimento','periodo']) },

    components: {
      PageContentFiltro: () => import('@/components/PageContent_filtroBusca'),
      TableSplit:        () => import('@/pages/Investimento/components/Table_Split'),
      ModalSplit:        () => import('@/pages/Investimento/components/Modal_Split'),
    },
    data() {
      return {
        INAS : 'init',
        camposBuscaBoolean : false,
        camposBusca: {
          itemsTipo: false,
          // tipo: 'mes',
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
        url += `&INAV_ID=${this.INAV_ID}`
        url += `&INAS_STATUS=1`
        url += `&retorno=I_ativoSplit`
        url += `&dataDe=${this.camposBusca.dataDe}`;
        url += `&dataAte=${this.camposBusca.dataAte}`;
        url += `&limit=${this.camposBusca.limit}`;
        url += `&orderby=INAS_DATA:DESC|INAS_ID:DESC`;
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

      // FILTRO ITEMS
      setFiltro(campos) {
        this.itemsTable = service.invest.filtroSplit(campos, this.items)
      },

      clearFiltro() {
        this.itemsTable   = this.items
      },

      // MODAL 
      setItem(INAS){
        this.INAS = INAS
        if(INAS == 'getDados') this.getDados()
      },
    },

    watch: {
      '$store.getters.Periodo'(newValue, oldValue) {
        this.getDados()
      },
    },


    // data() {
    //   return {
    //     time: false,
    //     itemsTable: [],
    //     campos: {
    //       dataDe: '',
    //       dataAte: '',
    //       limit: '',
    //     },
    //     itemModal: 'init',
    //   }
    // },

    // mounted () {
    //   this.getDados()
    // },

    // methods: {
      
    //   getDados() {
    //     this.time = false
    //     this.itemsTable = []

    //     setTimeout( () => {
          
    //       var endPoint = '';
    //       endPoint += 'ativo-split';
    //       endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
    //       endPoint += `&INAV_ID=${this.INAV_ID}`;
    //       if(this.campos.dataDe)  endPoint += `&dataDe=${this.campos.dataDe}`;
    //       if(this.campos.dataAte) endPoint += `&dataAte=${this.campos.dataAte}`;
    //       if(this.campos.limit)   endPoint += `&limit=${this.campos.limit}`;

    //       service.invest.busca(endPoint).then( ({STATUS, data, msg}) => {
            
    //         if(STATUS == 'success'){
    //           var {items, dataDe, dataAte, limit} = data
    //           this.itemsTable = items
    //           this.campos.limit = limit
    //           this.campos.dataDe  = dataDe
    //           this.campos.dataAte = dataAte
    //         }
    //         else if(STATUS == 'error') {
    //           this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
    //         }
    //         else if(STATUS == 'token') {
    //           this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
    //           this.$store.commit('SET_LOGIN', false);
    //         }

    //         this.time = true
    //       })
    //     }, service.timeLoading)
    //   },

    //   setItem(INAC) {
    //     this.itemModal = INAC
    //   },

    //   setCampos(campos) {
    //     this.campos = campos
    //   },

    // },
    
    // watch: {
    //   'investimento.INCT_ID'(newValue, oldValue) {
    //     this.getDados()
    //   },
    //   'periodo'(newValue, oldValue) {
    //     this.getDados()
    //   }
    // },
  }
</script>