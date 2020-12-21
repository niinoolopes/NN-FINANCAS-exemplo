<template>
  <section class="col-12 px-1 py-2 mb-3 d-md-flex flex-md-wrap justify-content-between align-items-start shadow-sm">

    <div class="div-campos p-0 d-flex flex-wrap">
      <div class="col-12 col-sm-6 col-lg-3 p-0 pr-1 mb-1 m-lg-0">
        <select class="form-control form-control-sm m-0" v-model="filtro.INCR_ID" @change="setFiltro(filtro)">
          <option value="">Filtrar por Corretora</option>
          <option v-for="(e,i) in itemsFiltro.INCR" :key="i" :value="e.INCR_ID" >{{e.INCR_DESCRICAO}}</option>
        </select>
      </div>

      <div class="col-12 col-sm-6 col-lg-3 p-0 pr-1 mb-1 m-lg-0">
        <select class="form-control form-control-sm m-0" v-model="filtro.INOD_DATA" @change="setFiltro(filtro)">
          <option value="">Filtrar por Situação</option>
          <option v-for="(e,i) in itemsFiltro.INOD_DATA" :key="i" :value="e.INOD_DATA" >{{e.INOD_DESCRICAO}}</option>
        </select>
      </div>

      <div class="col-12 col-sm-6 col-lg-3 p-0 pr-1 mb-1 m-lg-0">
        <input class="form-control form-control-sm m-0" type="text" v-model="filtro.OBS" @keyup="setFiltro(filtro)" >
      </div>

    </div>


    <div>
      <ButtonclearDados 
        :limpar='limparFiltro' />

      <ButtongetDados 
        v-if="typeof(getDados) == 'function' " 
        :getDados='getDados'
        class='ml-1' />
    </div>
    

  </section>
</template>

<script>
  export default {
    props: ['itemsFiltro', 'setFiltro', 'getDados'],
    
    components: {
      ButtongetDados:   () => import('@/components/Button_getDados'),
      ButtonclearDados: () => import('@/components/Button_clearDados'),
    },

    data() {
      return {
        filtro: {
          INCR_ID: '',
          INOD_DATA: '',
          OBS: '',
        }
      }
    },

    methods: {
      limparFiltro() {
        this.filtro.INCR_ID =  ''
        this.filtro.INOD_DATA =  ''
        this.filtro.OBS =  ''

        this.setFiltro(this.filtro)
      },
    },
    
  }
</script>