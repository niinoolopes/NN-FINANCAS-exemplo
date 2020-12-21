<template>
  <section>
    
    <PageContentNav>
      <template v-slot:menu>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2 p-0 pr-1 mb-1 m-sm-0">
          <select class="form-control form-control-sm mb-2 m-lg-0" @change="changeModulo" v-model="modulo">
            <option value="financa"     >Finança</option>
            <option value="cofre"       >Cofre</option>
            <option value="investimento">Investimento</option>
          </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2 p-0 pr-1 mb-1 m-sm-0">
          <select class="form-control form-control-sm mb-2 m-lg-0" v-model="carteira">
            <option v-for="(e,i) in arrCarteira" :key="i" :value="e.ID" >{{e.DESCRICAO}}</option>
          </select>
        </div>
        <div class="col-12 col-sm-6 col-lg-4 col-xl-2 p-0 pr-1 p-md-0 mb-1 m-sm-0">
          <select class="form-control form-control-sm mb-2 m-lg-0" @change="getDados" v-model="ano">
            <option v-for="(item,i) in  itemsFiltro" :key="i" :value="item">{{item}}</option>
          </select>
        </div>
      </template>

      <template v-slot:btn>
        <button 
          type="button"
          class="btn-hover btn btn-sm btn-outline-info py-0"
          @click="getDados()"
          ><i class="fas fa-sync"></i></button>

      </template>
    </PageContentNav>

    <div class="d-flex flex-wrap justify-content-start opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
      <div v-for="(item,i) in items" :key="i" class="col-4 col-md-3 col-xl-1 mb-2 p-1">
        <div class="pt-1 pb-2 px-2 border rounded">
            <p class="m-0 text-center">{{item.LABEL}}</p>
            <hr class="mt-1 mb-2">
            <div class="d-flex justify-content-center align-items-center">

              <button 
                type="button" 
                class="btn btn-sm btn-outline-secondary mr-1 p-0"
                @click="gerarRelatorio({ i, data: item.PERIODO })"> 
                <i class="fas fa-redo-alt"></i>
              </button> 

              <template v-if="item.RELATORIO">
                <a 
                  class="btn btn-sm btn-outline-info ml-1 p-0" 
                  target="_blank" 
                  :download="`${item.PERIODO}.csv`" 
                  :href="item.URL"> 
                  <i class="fas fa-file-download"></i>
                </a> 
              </template>
            </div> 
        </div>
      </div>
    </div>
        
  </section>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: {
    },

    data() {
      return {
        time: false,
        items: [],
        itemsFiltro: [],
        carteira: 0,
        arrCarteira: [],
        modulo: 'financa',
        ano: '',
      }
    },

    created () {
      if( !this.$store.getters.F_CarteiraPainel ){
        this.carteira = this.$store.getters.F_FINC_ID
        this.getCarteira();
        this.getDados();
      }

    },

    methods: {
      getCarteira(){
        let arr = []

        if(this.modulo == 'financa')      arr = this.$store.getters.F_CarteirasAtivas.map( e => { return {ID: e.FINC_ID, DESCRICAO: e.FINC_DESCRICAO}});
        if(this.modulo == 'cofre')        arr = this.$store.getters.C_CarteirasAtivas.map( e => { return {ID: e.COCT_ID, DESCRICAO: e.COCT_DESCRICAO}});
        if(this.modulo == 'investimento') arr = this.$store.getters.I_CarteirasAtivas.map( e => { return {ID: e.INCT_ID, DESCRICAO: e.INCT_DESCRICAO}});

        this.arrCarteira = arr
      },

      changeModulo(){
        this.ano = ''
        this.getCarteira();
        this.getDados()
      },

      getDados(){
        this.time = false;
        this.items = []

        setTimeout(() => {

          var endPoint = "";
          endPoint += "relatorio/busca/lista";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          if(this.carteira) endPoint += `&carteira=${this.carteira}`;
          if(this.modulo)   endPoint += `&modulo=${this.modulo}`;
          if(this.ano)      endPoint += `&ano=${this.ano}`;

          service.config.busca(endPoint).then(({ STATUS, data, msg }) => {

            if (STATUS == "success") { 
              const { ano, items, itemsFiltro } = data
              this.ano         = ano
              this.items       = items
              this.itemsFiltro = itemsFiltro

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

            this.time = true;
          });
        }, service.timeLoading);
      },

      gerarRelatorio({i,data}) {
        this.time = false;

        setTimeout(() => {

          var endPoint = "";
          endPoint += "relatorio/busca/gerar-relatorio";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&modulo=${this.modulo}`;
          endPoint += `&data=${data}`;

          service.config.busca(endPoint).then(({ STATUS, data, msg }) => {

            if (STATUS == "success") { 
              const {LABEL,PERIODO,RELATORIO,URL} = data
                this.items[i].LABEL     = LABEL
                this.items[i].PERIODO   = PERIODO
                this.items[i].RELATORIO = RELATORIO
                this.items[i].URL       = URL

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

            this.time = true;
          });
        }, service.timeLoading);

      },
    },
  }
</script>

<style scoped>
  .border.rounded .d-flex i {
    padding: 6px 0;
    font-size: 14px;
    width: 30px;
    height: 25px;
  }
  @media screen and (max-width: 576px) {
    .border.rounded .d-flex i {
      width: 25px;
    }
  }
</style>