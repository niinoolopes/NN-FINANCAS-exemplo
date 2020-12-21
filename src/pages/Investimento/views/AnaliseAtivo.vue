<template>
  <TemplateDefault title="Ativos">

    <Alert v-if="!$store.getters.I_CarteiraPainel" tipo="warning" texto="Selecione uma carteira no painel para visualizar os rendimentos!"/>

    <PageSection>
      <PageContentTitle :titulo="`Renda Fixa`">

        <template v-slot:btn>
          <ButtongetDados border='0' :getDados='getDados' />
        </template>
      </PageContentTitle>

      <FiltroItem :itemsFiltro='itemsFiltro' :setFiltro='setFiltro' />

      <TableAtivo :items="itemsTable" :time="time" />
    </PageSection>

  </TemplateDefault>
</template>

<script>
  import service from '@/service.js'

  export default {

    components: { 
      ButtongetDados: () => import('@/components/Button_getDados'),
      TableAtivo:     () => import('@/pages/Investimento/components/Table_Ativo'),
      FiltroItem:     () => import('@/pages/Investimento/components/Section_filtro_item'),
    },

    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
        itemsFiltro: [],
      }
    },
    
    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        this.getDados()
      }
    },

    methods: {
      getDados() {

        this.time = false;
        this.items = 'init'
        this.itemsTable = 'init'
        this.itemsFiltro = []

        var url = ''
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`;
        url += `&dataAte=${this.$store.getters.Periodo}`;
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
        url += `&retorno=I_carteiraComposicao`;

        setTimeout( () => {
          service.busca(url)
          .then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              const {items, itemsFiltro} = data.I_carteiraComposicao
              this.items       = items
              this.itemsTable  = items
              this.itemsFiltro = itemsFiltro
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.time = true;

          })
        }, service.timeLoading)
      },
      
      setFiltro(campos) {
        this.itemsTable = service.invest.filtroItem(campos, this.items)
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