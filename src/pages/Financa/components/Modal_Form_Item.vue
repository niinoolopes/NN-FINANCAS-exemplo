<template>
  <section>
    <div v-if="FNIT != 'init'" class="modal fade" id="FinancaModal-Item">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="FinancaModal-item-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <div class="modal-body opacity-fetch" :class="timeModal ? '' : 'opacity-fetch-active'">
            <form>
              <div class="row">

                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-FINC_ID">Carteira</label>
                  <select class="form-control form-control-sm" id="form-FINC_ID" v-model="itemModal.FINC_ID" @change="changeCarteira">
                    <option value="">Selecione...</option>
                    <option v-for="(cart,i) in $store.getters.F_CarteirasAtivas" :key="i" :value="cart.FINC_ID">{{cart.FINC_DESCRICAO}}</option>
                  </select>
                </div> 

                <div class="col-lg-8 col-xl-10 d-none d-md-block"></div>

                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-FITP_ID">Tipo</label>
                    <select class="form-control form-control-sm" id="form-FITP_ID" v-model="itemModal.FITP_ID" @change="changeTipo" :disabled="itemModal.FINC_ID ? false : true">
                      <option value="">Selecione...</option>
                      <option v-for="item in $store.getters.F_TiposAtivos" :key="item.FITP_ID" :value="item.FITP_ID" @change="selectGrupo">
                        {{item.FITP_DESCRICAO}} {{item.selected}}
                      </option>
                    </select>
                </div>
                
                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-FIGP_ID">Grupo</label>
                    <select class="form-control form-control-sm" id="form-FIGP_ID" v-model="itemModal.FIGP_ID" @change="changeGrupo" :disabled="itemModal.FITP_ID ? false : true">
                      <option value="">Selecione...</option>
                      <option v-for="(g,i) in arrGrupo" :key="i" :value="g.FIGP_ID" >{{g.FIGP_DESCRICAO}}</option>
                    </select>
                </div>

                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-FICT_ID">Categoria</label>
                  <select class="form-control form-control-sm" id="form-FICT_ID" v-model="itemModal.FICT_ID" @change="changeCategoria"  :disabled="itemModal.FIGP_ID ? false : true">
                    <option value="">Selecione...</option>
                    <option v-for="(c,i) in arrCategoria" :key="i" :value="c.FICT_ID" >{{c.FICT_DESCRICAO}}</option>
                  </select>
                </div>

                <div class="px-1 px-md-3 mb-2 form-group col-12 col-md-4 col-xl-2">
                  <label for="form-FNIS_ID">Situação</label>
                  <select class="form-control form-control-sm" id="form-FNIS_ID" v-model="itemModal.FNIS_ID" :disabled="itemModal.FICT_ID ? false : true">
                  <option value="">Selecione...</option>
                    <option v-for="(s,i) in $store.getters.F_Situacoes" :key="i" :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
                  </select>
                </div>
                
                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-FNIT_VALOR">Valor</label>
                  <input class="form-control form-control-sm" type="number" step="0.01" id="form-FNIT_VALOR" v-model="itemModal.FNIT_VALOR" :disabled="itemModal.FNIS_ID ? false : true">
                </div>
                
                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-FNIT_DATA">Data</label>
                  <input class="form-control form-control-sm" type="date" id="form-FNIT_DATA" v-model="itemModal.FNIT_DATA" :disabled="itemModal.FNIT_VALOR ? false : true">
                </div>

                <div class="px-1 px-md-3 mb-2 form-group col-12 col-md-12">
                  <label for="form-FNIT_OBS">Observação</label>
                  <textarea class="form-control form-control-sm" id="form-FNIT_OBS" v-model="itemModal.FNIT_OBS" rows="5" 
                    :disabled="itemModal.FNIT_DATA ? false : true"
                  ></textarea>
                </div>
              
                <div class="px-1 px-md-3 mb-2 form-group col-12">
                  <div class="form-group form-check m-0">
                    <input type="checkbox" class="form-check-input" id="form-FNIT_STATUS" v-model="itemModal.FNIT_STATUS">
                    <label class="form-check-label" for="form-FNIT_STATUS">{{itemModal.FNIT_STATUS ? 'Registro Ativo' : 'Registro Inativo'}}</label>
                  </div>
                </div>

              </div>
            </form>
          </div>

          <div class="modal-footer p-2">
            <button type="button" class="btn btn-sm btn-outline-info"      @click="salvarItem()" :disabled="itemModal.FNIT_OBS ? false : true" >Salvar</button>
            <button type="button" class="btn btn-sm btn-outline-danger"    @click="deletar()" v-if="itemModal.FNIT_ID != 'novo'" >Deletar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
          </div>

        </div>
      </div>
    </div> 
  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    props:['FNIT','setItem'],

    data() {
      return {
        timeModal: false,
        itemModal: {},
        arrGrupo: [],
        arrCategoria: [],
      }
    },

    methods: {
      changeCarteira(){
        if(this.itemModal.FINC_ID == '') {
          this.itemModal.FINC_ID = ''
          this.itemModal.FITP_ID = ''
          this.itemModal.FIGP_ID = ''
          this.itemModal.FICT_ID = ''
        }else{
            this.selectGrupo()
        }
      },

      changeTipo(){
        if(this.itemModal.FITP_ID == '') {
          this.itemModal.FITP_ID = ''
          this.itemModal.FIGP_ID = ''
          this.itemModal.FICT_ID = ''
        }else{
            this.selectGrupo()
        }
      },

      changeGrupo(){
        this.selectCategoria()
      },

      changeCategoria(){
        this.itemModal.FNIT_OBS = this.$store.getters.F_Observacao(this.itemModal.FICT_ID, this.itemModal.FNIT_OBS)
      },

      // --

      selectGrupo(){
        if( this.itemModal.FINC_ID != '' && this.itemModal.FITP_ID != '' ) {
          this.arrGrupo = this.$store.getters.F_GruposAtivos(this.itemModal.FINC_ID, this.itemModal.FITP_ID)
        } else {
          this.arrGrupo = [];
        }
      },

      selectCategoria(){
        if( this.itemModal.FIGP_ID != '' ) {
          this.arrCategoria = this.$store.getters.F_CategoriasAtivas(this.itemModal.FIGP_ID)
        } else {
          this.arrCategoria = [];
        }
      },

      // --

      initModal(){
        service.initForm([
          'FICT_ID',
          'FIGP_ID',
          'FINC_ID',
          'FITP_ID',
          'FNIS_ID',
          'FNIT_DATA',
          'FNIT_OBS',
          'FNIT_VALOR',
        ])

        this.itemModal = {
          FNIT_ID: 'novo',
          FICT_ID: '',
          FIGP_ID: '',
          FINC_ID: this.$store.getters.F_FINC_ID,
          FITP_ID: '',
          FNIS_ID: '',
          FNIT_DATA: service.dataHoje(),
          FNIT_OBS: '',
          FNIT_VALOR: 0,
          FNIT_STATUS: 1
        }

        this.arrGrupo     = []
        this.arrCategoria = []
      },

      // --

      getItemID(FNIT_ID){
        this.timeModal = false

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.FNIT_ID = FNIT_ID

        service.finc.item.get(option).then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            if(data.length > 0){
              this.itemModal = data[0]
              this.selectGrupo()
              this.selectCategoria()
            } else {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
          }
          else if(STATUS == 'error'){
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
          }
          else if(STATUS == 'token'){
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
            this.$store.commit('SET_LOGIN', false);
          }

        })

        this.timeModal = true
      },

      salvarItem(){

        let data = {}
          data.FINC_ID     = this.itemModal.FINC_ID
          data.FITP_ID     = this.itemModal.FITP_ID
          data.FIGP_ID     = this.itemModal.FIGP_ID
          data.FICT_ID     = this.itemModal.FICT_ID
          data.FNIS_ID     = this.itemModal.FNIS_ID
          data.FNIT_VALOR  = this.itemModal.FNIT_VALOR
          data.FNIT_DATA   = this.itemModal.FNIT_DATA
          data.FNIT_OBS    = this.itemModal.FNIT_OBS
          data.FNIT_STATUS = this.itemModal.FNIT_STATUS ? 1 : 0
          data.USUA_ID     = this.$store.getters.USUA_ID

        let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.FNIT_ID = this.itemModal.FNIT_ID
          option.data    = data

        let checkForm = {}
          checkForm.FINC_ID    = this.itemModal.FINC_ID
          checkForm.FITP_ID    = this.itemModal.FITP_ID
          checkForm.FIGP_ID    = this.itemModal.FIGP_ID
          checkForm.FICT_ID    = this.itemModal.FICT_ID
          checkForm.FNIS_ID    = this.itemModal.FNIS_ID
          checkForm.FNIT_VALOR = this.itemModal.FNIT_VALOR
          checkForm.FNIT_DATA  = this.itemModal.FNIT_DATA
          checkForm.FNIT_OBS   = this.itemModal.FNIT_OBS

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.FNIT_ID == 'novo'){
              service.finc.item.post(option).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
                  this.setItem('getDados')
                  
                } else if (STATUS == "error") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

                } else if (STATUS == "token") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                  this.$store.commit("SET_LOGIN", false);
                }
              })
            } else {
              service.finc.item.put(option).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item atualizado!' }) // message
                  this.setItem('getDados')

                } else if (STATUS == "error") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

                } else if (STATUS == "token") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                  this.$store.commit("SET_LOGIN", false);
                }
              })
            }

            service.closeModal('FinancaModal-item-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { data: this.item }
          service.finc.item.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item atualizado!' }) // message
              this.setItem('getDados')

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao excluir, tente novamente!' }) // message

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);
            }
          })
        }
        service.closeModal('FinancaModal-item-close')
      },
    },

    watch: {
      'FNIT'(FNIT_ID, oldValue) {
        this.initModal()

        if(FNIT_ID == 'novo') {
          this.timeModal = true

        }else if(FNIT_ID != '' && FNIT_ID != 'init') {
          this.timeModal = false
          this.getItemID(FNIT_ID)

        }
      }
    },
  }
</script>