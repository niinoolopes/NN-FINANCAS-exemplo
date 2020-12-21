<template>
  <TemplateDefault title="Carteira">

    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar as ordens!"/>
    
    <!--
    <PageNav>
      <template v-slot:menu> </template>
      <template v-slot:btn> </template>
    </PageNav>
    -->

    <PageSection>
      <PageContentTitle :titulo="`Renda Fixa`">
        <template v-slot:btn>
          <ButtongetDados border='0' :getDados='getDados_1' class="mr-1"/>
          <Buttoninfo     border='0' :click='() => INTP_1.detalheItem = !INTP_1.detalheItem' />
        </template>
      </PageContentTitle>
      
      <!-- <FiltroItem v-if='INTP_1.items != `init` && INTP_1.items.length' :itemsFiltro='INTP_1.itemsFiltro' :setFiltro='setFiltro_1' /> -->
      <CarteiraCardsitems :time="INTP_1.time" :items="INTP_1.itemsTable"/>
    </PageSection>

    <PageSection>
      <PageContentTitle :titulo="`Renda Variavel`">
        <template v-slot:btn>
          <ButtongetDados border='0' :getDados='getDados_2' class="mr-1"/>
          <Buttoninfo     border='0' :click='() => INTP_2.detalheItem = !INTP_2.detalheItem' />
        </template>
      </PageContentTitle>

      <FiltroItem v-if='INTP_2.items != `init` && INTP_2.items.length' :itemsFiltro='INTP_2.itemsFiltro' :setFiltro='setFiltro_2' />
      <CarteiraCardsitems :time="INTP_2.time" :items="INTP_2.itemsTable" :detalheItem='INTP_2.detalheItem' />
    </PageSection>


  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {
    components: { 
      ButtongetDados:     () => import('@/components/Button_getDados'),
      Buttoninfo:         () => import('@/components/Button_info'),
      FiltroItem:         () => import('@/pages/Investimento/components/Section_filtro_item'),
      CarteiraCardsitems: () => import('@/pages/Investimento/components/Section_Carteira_cards_items'),
    },

    data() {
      return {
        INTP_1: {
          INTP_ID: 1,
          detalheItem: false,
          time: false,
          items: [],
          itemsTable: [],
          itemsFiltro: [],
        },
        INTP_2: {
          INTP_ID: 2,
          detalheItem: false,
          time: false,
          items: [],
          itemsTable: [],
          itemsFiltro: [],
        },
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados({tipo: 'INTP_1'})
        this.getDados({tipo: 'INTP_2'})
      }
    },

    methods: {
      getDados_1() {
        this.getDados({tipo: 'INTP_1'})
      },
      
      getDados_2() {
        this.getDados({tipo: 'INTP_2'})
      },

      getDados({tipo} = {}) {

        this[tipo].time = false;
        this[tipo].items = 'init'
        this[tipo].itemsTable = 'init'

        var url = ''
        url += 'componente'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
        url += `&dataAte=${this.$store.getters.Periodo}`
        url += `&retorno=I_carteiraComposicao`
        if(this[tipo]) url += `&INTP_ID=${this[tipo].INTP_ID}`


        setTimeout( () => {
          service.busca(url)
          .then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              const {items, itemsFiltro} = data.I_carteiraComposicao
              this[tipo].items       = items
              this[tipo].itemsTable  = items.filter(i => i.COTAS > 0)
              this[tipo].itemsFiltro = itemsFiltro
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this[tipo].time = true;

          })
        }, service.timeLoading)
      },

      setFiltro_1(campos) {
        this.INTP_1.itemsTable = service.invest.filtroItem(campos, this.INTP_1.items)
      },

      setFiltro_2(campos) {
        this.INTP_2.itemsTable = service.invest.filtroItem(campos, this.INTP_2.items)
      },
    },
  }
</script>