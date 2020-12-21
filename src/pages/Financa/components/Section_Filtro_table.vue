<template>
  <section class="col-12 px-1 py-2 mb-3 d-md-flex flex-md-wrap justify-content-between align-items-start shadow-sm">

    <div class="col p-0 d-flex flex-wrap mr-md-2">
      <div class="px-1 col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.FITP_ID" @change="setFiltro(filtro)">
          <option value="">Tipo</option>
          <option v-for="(s,i) in itemsFiltro.FITP" :key="i" :value="s.FITP_ID" >{{s.FITP_DESCRICAO}}</option>
        </select>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.FNIS_ID" @change="setFiltro(filtro)">
          <option value="">Situação</option>
          <option v-for="(s,i) in itemsFiltro.FNIS" :key="i" :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
        </select>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.FIGP_ID" @change="setFiltro(filtro)">
          <option value="">Grupo</option>
          <option v-for="(s,i) in itemsFiltro.FIGP" :key="i" :value="s.FIGP_ID" >{{s.FIGP_DESCRICAO}}</option>
        </select>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2">
        <select class="form-control form-control-sm m-0" v-model="filtro.FICT_ID" @change="setFiltro(filtro)">
          <option value="">Categoria</option>
          <option v-for="(s,i) in itemsFiltro.FICT" :key="i" :value="s.FICT_ID" >{{s.FICT_DESCRICAO}}</option>
        </select>
      </div>

      <div class="px-1 col-6 col-sm-4 col-md-2">
        <input class="form-control form-control-sm m-0" type="text" v-model="filtro.OBS" @keyup="setFiltro(filtro)" >
      </div>
    </div>
    
    <button type="button" class="div-btn btn-hover btn btn-sm btn-outline-info py-0" @click="limparFiltro()">
      <i class="fas fa-broom"></i>
    </button>
    
  </section>
</template>

<script>
  export default {
    props: ['itemsFiltro', 'setFiltro'],

    data() {
      return {
        filtro: {
          FITP_ID: '',
          FIGP_ID: '',
          FICT_ID: '',
          FNIS_ID: '',
          OBS: '',
        }
      }
    },

    methods: {
      limparFiltro() {
        this.filtro.FITP_ID = '';
        this.filtro.FIGP_ID = '';
        this.filtro.FICT_ID = '';
        this.filtro.FNIS_ID = '';
        this.filtro.OBS = '';

        this.setFiltro(this.filtro)
      }
    },
  }
</script>