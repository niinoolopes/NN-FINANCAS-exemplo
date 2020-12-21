<template>
  <PageContentNav>
    <template v-slot:menu>
      <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.INCR_ID" @change="setFiltro(filtro)">
          <option value="">Corretora</option>
          <option v-for="(e,i) in itemsFiltro.INCR" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column>
      <!-- <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.INTP_ID" @change="setFiltro(filtro)">
          <option value="">Tipo</option>
          <option v-for="(e,i) in itemsFiltro.INTP" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column> -->
      <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.INAT_ID" @change="setFiltro(filtro)">
          <option value="">Tipo</option>
          <option v-for="(e,i) in itemsFiltro.INAT" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column>
      <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.INAV_ID" @change="setFiltro(filtro)">
          <option value="">Ativo</option>
          <option v-for="(e,i) in itemsFiltro.INAV" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column>
      <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.INAV_LIQUIDEZ" @change="setFiltro(filtro)">
          <option value="">Liquidez</option>
          <option v-for="(e,i) in itemsFiltro.LIQUIDEZ" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column>
      <!-- <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.DATA" @change="setFiltro(filtro)">
          <option value="">Data</option>
          <option v-for="(e,i) in itemsFiltro.DATA" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column> -->
      <Column class="col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.COTAS" @change="setFiltro(filtro)">
          <option value="">Com/Sem Cotas</option>
          <option v-for="(e,i) in itemsFiltro.COTAS" :key="i" :value="e.VALUE" >{{e.DESCRICAO}}</option>
        </select>
      </Column>
    </template>

    <template v-slot:btn>
      <ButtonClearDados class="mb-2 mr-1" :limpar='limparFiltro' />
    </template>
  </PageContentNav>
</template>

<script>
  export default {
    props: ['itemsFiltro', 'setFiltro'],
    
    components: { 
      ButtonClearDados:  () => import('@/components/Button_clearDados')
    },

    data() {
      return {
        filtro: {
          INCR_ID: '',
          INTP_ID: '',
          INAT_ID: '',
          INAV_ID: '',
          INAV_LIQUIDEZ: '',
          DATA: '',
          COTAS: 'com-cotas',
        }
      }
    },

    methods: {
      limparFiltro() {
        this.filtro.INCR_ID =  ''
        this.filtro.INTP_ID =  ''
        this.filtro.INAT_ID =  ''
        this.filtro.INAV_ID =  ''
        this.filtro.INAV_LIQUIDEZ =  ''
        this.filtro.DATA  =  ''
        this.filtro.COTAS =  'com-cotas'

        this.setFiltro(this.filtro)
      }
    },
    
  }
</script>

<style scoped>
  [class*="col-"] {
    max-width: 150px;
  }
</style>