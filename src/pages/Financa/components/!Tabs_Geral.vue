<template>
  <div class="d-flex flex-wrap opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

    <div class="col-12 mb-3 mb-lg-0 col-lg-7 border over-x">
        <TableGeral :tr="trs" />
    </div>

    <div class="d-none d-lg-block col-lg-5">
      <GraficoBar 
        v-if="labels.length"
        :labels="labels"
        :valores="valores"
        :tipo="tipo"
        :title="`${tipo} por ${dados}`"
        :styles="{
            height: (labels.length < 5) ? '175px' : `${labels.length * 35}px`
        }" 
        />
    </div>

  </div>
</template>

<script>

  export default {
    props: ['tipo', 'dados', 'trs', 'grafico', 'time'],
    
    components: {
      TableGeral: () => import('@/pages/Financa/components/Table_Geral'),
      GraficoBar: () => import('@/components/Grafico/Financa/BarHozizontal-GruposCategorias'),
    },

    data() {
      return {
        labels: [],
        valores: [],
      }
    },

    mounted () {
      this.renderTab()
    },

    methods: {
      renderTab() {
        const {label, valores} = this.grafico

        this.labels = label
        this.valores = valores.map( v => +Number(v).toFixed(2) )
      }
    },

    watch: {
      'grafico.label'(newValue, oldValue) {
        this.renderTab();
      },
      'grafico.valores'(newValue, oldValue) {
        this.renderTab();
      }
    },
  }
</script>