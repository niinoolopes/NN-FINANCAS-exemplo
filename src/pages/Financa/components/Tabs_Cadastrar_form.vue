<template>
  <section>
    <form v-on:submit.prevent="salvar">
      <div class="d-flex flex-wrap">

        <Column class="col-6 col-md-4 col-xl-2 form-group">
          <label for="form-FINC_ID">Carteira</label>
          <select class="form-control form-control-sm" id="form-FINC_ID" v-model="item.FINC_ID" @change="changeCarteira">
            <option value="">Selecione...</option>
            <option v-for="(cart,i) in $store.getters.F_CarteirasAtivas" :key="i" :value="cart.FINC_ID">{{cart.FINC_DESCRICAO}}</option>
          </select>
        </Column>

       <Column class="col-lg-8 col-xl-10 d-none d-md-block"></Column>

        <Column class="col-6 col-md-4 col-xl-2 form-group">
          <label for="form-FITP_ID">Tipo</label>
            <select class="form-control form-control-sm" id="form-FITP_ID" v-model="item.FITP_ID" @change="changeTipo" :disabled="item.FINC_ID ? false : true">
              <option value="">Selecione...</option>
              <option v-for="(t,i) in $store.getters.F_TiposAtivos" :key="i" :value="t.FITP_ID">
                {{t.FITP_DESCRICAO}} {{t.selected}}
              </option>
            </select>
        </Column>
        
        <Column class="col-6 col-md-4 col-xl-2 form-group">
          <label for="form-FIGP_ID">Grupo</label>
            <select class="form-control form-control-sm" id="form-FIGP_ID" v-model="item.FIGP_ID" @change="changeGrupo" :disabled="item.FITP_ID ? false : true">
              <option value="">Selecione...</option>
              <option v-for="(g,i) in arrGrupo" :key="i" :value="g.FIGP_ID" >{{g.FIGP_DESCRICAO}}</option>
            </select>
        </Column>

        <Column class="col-6 col-md-4 col-xl-2 form-group">
          <label for="form-FICT_ID">Categoria</label>
          <select class="form-control form-control-sm" id="form-FICT_ID" v-model="item.FICT_ID" @change="changeCategoria" :disabled="item.FIGP_ID ? false : true">
            <option value="">Selecione...</option>
            <option v-for="(c,i) in arrCategoria" :key="i" :value="c.FICT_ID" >{{c.FICT_DESCRICAO}}</option>
          </select>
        </Column>

        <Column class="col-12 col-md-4 col-xl-2 form-group">
          <label for="form-FNIS_ID">Situação</label>
          <select class="form-control form-control-sm" id="form-FNIS_ID" v-model="item.FNIS_ID"
          :disabled="item.FICT_ID ? false : true">
          <option value="">Selecione...</option>
            <option v-for="(s,i) in $store.getters.F_Situacoes" :key="i" 
              :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
          </select>
        </Column>
        
        <Column class="col-6 col-md-4 col-xl-2 form-group">
          <label for="form-FNIT_VALOR">Valor</label>
          <input class="form-control form-control-sm" type="number" step="0.01" id="form-FNIT_VALOR" v-model="item.FNIT_VALOR" 
            :disabled="item.FNIS_ID ? false : true">
        </Column>
        
        <Column class="col-6 col-md-4 col-xl-2 form-group">
          <label for="form-FNIT_DATA">Data</label>
          <input class="form-control form-control-sm" type="date" id="form-FNIT_DATA" v-model="item.FNIT_DATA" 
            :disabled="item.FNIT_VALOR ? false : true">
        </Column>

        <Column class="col-12 form-group">
          <label for="form-FNIT_OBS">Observação</label>
          <textarea class="form-control form-control-sm" id="form-FNIT_OBS" v-model="item.FNIT_OBS" rows="5" 
            :disabled="item.FNIT_DATA ? false : true"
          ></textarea>
        </Column>
      
        <Column class="col-12 form-group">
          <div class="form-group form-check mb-0">
            <input type="checkbox" class="form-check-input" id="form-FNIT_STATUS" v-model="item.FNIT_STATUS" @click=" item.FNIT_STATUS = !item.FNIT_STATUS">
            <label class="form-check-label" for="form-FNIT_STATUS">{{item.FNIT_STATUS ? 'Registro Ativo' : 'Registro Inativo'}}</label>
          </div>
        </Column>

      </div>

      <hr>

      <button type="button" class="btn btn-sm btn-outline-info mr-2" @click="salvar">Salvar</button>
      <button type="reset" class="btn btn-sm btn-outline-info mr-2" @click="formInit">Limpar</button>
    </form>
  </section>
</template>

<script>
  import service from '@/service.js'

  export default {

    props:['FNIT_ID','setItem'],

    data() {
      return {
        arrGrupo: [],
        arrCategoria: [],
        item: {},
      }
    },

    mounted () {
      this.arrGrupo = []
      this.arrCategoria = []


      if( !this.$store.getters.F_CarteiraPainel ){
        this.item.FINC_ID = this.$store.getters.F_FINC_ID
        this.item.FITP_ID = this.$route.params.FITP
        this.formInit()
        this.changeTipo()

        if(typeof this.FNIT_ID == 'number') this.getItemID()
      }
    },

    methods: {
      changeCarteira(){
        if(this.item.FINC_ID == '') {
          this.item.FINC_ID = ''
          this.item.FITP_ID = ''
          this.item.FIGP_ID = ''
          this.item.FICT_ID = ''
        }else{
            this.selectGrupo()
        }
      },

      changeTipo(){
        if(this.item.FITP_ID == '') {
          this.item.FITP_ID = ''
          this.item.FIGP_ID = ''
          this.item.FICT_ID = ''
        }else{
            this.selectGrupo()
        }
      },

      changeGrupo(){
        this.selectCategoria()
      },

      changeCategoria(){
        this.item.FNIT_OBS = this.$store.getters.F_Observacao(this.item.FICT_ID, this.item.FNIT_OBS)
      },

      selectGrupo() {
        if( this.item.FINC_ID != '' && this.item.FITP_ID != '' ) {
          this.arrGrupo = this.$store.getters.F_GruposAtivos(this.item.FINC_ID, this.item.FITP_ID)
        } else {
          this.arrGrupo = [];
        }
      },

      selectCategoria() {
        if( this.item.FIGP_ID != '' ) {
          this.arrCategoria = this.$store.getters.F_CategoriasAtivas(this.item.FIGP_ID)
        } else {
          this.arrCategoria = [];
        }
      },

      // --
      makeUrl() {
        var url = ''
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&FNIT_ID=${this.FNIT_ID}`
        url += `&retorno=F_item`
        return url
      },

      getItemID(){
        service.busca( this.makeUrl() )
        .then(({ STATUS, data, msg }) => {
          if (STATUS == "success") {
            this.item = data.F_item[0]
            this.item.FNIT_DATA = service.dataHoje()
            this.item.FNIT_OBS = ''
            
            this.arrGrupo     = this.$store.getters.F_GruposAtivos(this.item.FINC_ID, this.item.FITP_ID)
            this.arrCategoria = this.$store.getters.F_CategoriasAtivas(this.item.FIGP_ID)
            
          } else if (STATUS == "error") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

          } else if (STATUS == "token") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
            this.$store.commit("SET_LOGIN", false);

          }
        });
      },

      salvar(){
        let data = {}
        data.FINC_ID     = this.item.FINC_ID
        data.FITP_ID     = this.item.FITP_ID
        data.FIGP_ID     = this.item.FIGP_ID
        data.FICT_ID     = this.item.FICT_ID
        data.FNIS_ID     = this.item.FNIS_ID
        data.FNIT_VALOR  = this.item.FNIT_VALOR
        data.FNIT_DATA   = this.item.FNIT_DATA
        data.FNIT_OBS    = this.item.FNIT_OBS
        data.FNIT_STATUS = this.item.FNIT_STATUS ? 1 : 0
        data.USUA_ID     = this.$store.getters.USUA_ID


        let option = {}
        option.data = data
        option.USUA_ID = this.$store.getters.USUA_ID

        let checkForm = {}
        checkForm.FINC_ID    = this.item.FINC_ID
        checkForm.FITP_ID    = this.item.FITP_ID
        checkForm.FIGP_ID    = this.item.FIGP_ID
        checkForm.FICT_ID    = this.item.FICT_ID
        checkForm.FNIS_ID    = this.item.FNIS_ID
        checkForm.FNIT_VALOR = this.item.FNIT_VALOR
        checkForm.FNIT_DATA  = this.item.FNIT_DATA
        checkForm.FNIT_OBS   = this.item.FNIT_OBS

        setTimeout( () => {
          if( service.checkForm(checkForm) ){
            service.finc.item.post(option).then( ({STATUS, data, msg}) => {
              if(STATUS == 'success'){
                this.formInit()
                this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
              } 
              else if (STATUS == "erro") {
                this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });
              } 
              else if (STATUS == "token") {
                this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                this.$store.commit("SET_LOGIN", false);
              }

              this.time = true;
            })
          }
        }, service.timeLoading)
      },

      formInit(){
        service.initForm([
        'FIGP_ID',
        'FINC_ID',
        'FITP_ID',
        'FICT_ID',
        'FNIS_ID',
        'FNIT_DATA',
        'FNIT_OBS',
        'FNIT_VALOR',
        ])

        this.item = {
          FINC_ID:     !this.$store.getters.F_CarteiraPainel ? this.$store.getters.F_FINC_ID : '',
          FITP_ID:     this.$route.params.FITP || '',
          FIGP_ID:     '',
          FICT_ID:     '',
          FNIS_ID:     '',
          FNIT_DATA:   service.dataHoje(),
          FNIT_ID:     'novo',
          FNIT_OBS:    '',
          FNIT_STATUS: 1,
          FNIT_VALOR:  '',
          USUA_ID:     '',
        }
      },
    },

    watch: {
      'FNIT_ID'(newValue, oldValue) {
        if(typeof newValue == 'number'){
          this.formInit()
          this.getItemID()
        } 
      },
      '$route.params.FITP'(newValue, oldValue) {
        this.formInit()
        this.changeTipo()
      }
    },
  }

</script>