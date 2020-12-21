<template>
  <section>

    <div v-if="itemModal !== 'init'" class="modal fade" id="CofreModal-Item">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          
          <div class="modal-header py-1 px-2">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="CofreModal-item-close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>

          <div class="modal-body">
            <form v-on:submit.prevent="salvar">
              <div class="row">
                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-COCT_ID">Carteira</label>
                  <select class="form-control form-control-sm" id="form-COCT_ID" v-model="itemModal.COCT_ID">
                    <option value="">Selecione...</option>
                    <option v-for="(c,i) in $store.getters.C_CarteirasAtivas" :key="i" :value="c.COCT_ID">{{c.COCT_DESCRICAO}}</option>
                  </select>
                </div>
                
                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-COTP_ID">Tipo</label>
                  <select class="form-control form-control-sm" id="form-COTP_ID" v-model="itemModal.COTP_ID">
                    <option value="">Selecione...</option>
                    <option v-for="(t,i) in $store.getters.C_Tipos" :key="i" :value="t.COTP_ID">{{t.COTP_DESCRICAO}}</option>
                  </select>
                </div>

                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-COIT_VALOR">Valor</label>
                  <input class="form-control form-control-sm" type="number" step="0.01" id="form-COIT_VALOR" v-model="itemModal.COIT_VALOR">
                </div>
                
                <div class="px-1 px-md-3 mb-2 form-group col-6 col-md-4 col-xl-2">
                  <label for="form-COIT_DATA">Data</label>
                  <input class="form-control form-control-sm" type="date" id="form-COIT_DATA" v-model="itemModal.COIT_DATA">
                </div>

                <div class="px-1 px-md-3 mb-2 form-group col-12 col-md-12">
                  <label for="form-COIT_OBS">Observação</label>
                  <textarea class="form-control form-control-sm" id="form-COIT_OBS" v-model="itemModal.COIT_OBS" rows="5" ></textarea>
                </div>
            
                <div class="px-1 px-md-3 mb-2 form-group col-12">
                  <div class="form-group form-check m-0">
                    <input type="checkbox" class="form-check-input" id="form-COIT_STATUS" v-model="itemModal.COIT_STATUS">
                    <label class="form-check-label" for="form-COIT_STATUS">{{itemModal.COIT_STATUS ? 'Registro Ativo' : 'Registro Inativo'}}</label>
                  </div>
                </div>

                <div class="px-1 px-md-3 col-12">
                  <hr class="my-2">
                </div>

                <div class="px-1 px-md-3 mb-2 form-group col-12">
                  
                  <label for="form-COIT_PROPOSITO">Proposito</label>

                  <div class="row">

                    <div class="col-6 col-md-4 col-xl-2 px-1 px-md-2 m-0 mb-2 ">
                      <div class="p-1 p-md-2 border d-flex justify-content-center align-items-center">
                        <input class="form-control form-control-sm" type="text" v-model="itemModal.COIT_PROPOSITO">
                      </div>
                    </div>

                    <div v-for="(text,i) in arrProposito.LISTA" :key="i" class="col-6 col-md-4 col-xl-2 px-1 px-md-2 m-0 mb-2">
                      <div class="p-1 p-md-2 border d-flex justify-content-center align-items-center">
                        <input class="mr-2" :id="`texto-${i}`" type="radio" :value="text.COIT_PROPOSITO" v-model="itemModal.COIT_PROPOSITO">
                        <label class="m-0" :for="`texto-${i}`">{{text.COIT_PROPOSITO}}</label>
                      </div>
                    </div>

                  </div>
                </div>

              </div>
            </form>
          </div>

          <div class="modal-footer p-2">
            <button type="button" class="btn btn-sm btn-outline-info"      @click="salvar()" :disabled="itemModal.COIT_OBS ? false : true" >Salvar</button>
            <button type="button" class="btn btn-sm btn-outline-danger"    @click="deletar()" v-if="itemModal.COCT_ID != 'novo'" >Deletar</button>
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
    props:['itemModal','getDados'],
    
    data() {
      return {
        arrProposito: {
          COCT_ID: 0,
          LISTA: []
        },
      }
    },

    methods: {
      salvar(){
        let data = {}
          data.COCT_ID        = this.itemModal.COCT_ID
          data.COTP_ID        = this.itemModal.COTP_ID
          data.COIT_VALOR     = this.itemModal.COIT_VALOR
          data.COIT_DATA      = this.itemModal.COIT_DATA
          data.COIT_OBS       = this.itemModal.COIT_OBS
          data.COIT_STATUS    = this.itemModal.COIT_STATUS ? 1 : 0
          data.COIT_PROPOSITO = this.itemModal.COIT_PROPOSITO
          data.USUA_ID        = this.$store.getters.USUA_ID

        let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.COIT_ID = this.itemModal.COIT_ID
          option.data = data

        let checkForm = {}
          checkForm.COCT_ID    = this.itemModal.COCT_ID
          checkForm.COTP_ID    = this.itemModal.COTP_ID
          checkForm.COIT_VALOR = this.itemModal.COIT_VALOR
          checkForm.COIT_DATA  = this.itemModal.COIT_DATA
          checkForm.COIT_OBS   = this.itemModal.COIT_OBS

        setTimeout( () => {
          if( service.checkForm(checkForm) ){
            if(checkForm.COCT_ID == 'novo') {
              service.cofre.item.post(option).then( ({STATUS, data, msg}) => {

                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message

                } else if (STATUS == "error") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

                } else if (STATUS == "token") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                  this.$store.commit("SET_LOGIN", false);

                }
              })

            } else {
              service.cofre.item.put(option).then( ({STATUS, data, msg}) => {

                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item Atualizado!' }) // message

                } else if (STATUS == "error") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

                } else if (STATUS == "token") {
                  this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
                  this.$store.commit("SET_LOGIN", false);

                }
              })
            }

            this.getDados()
            service.closeModal('CofreModal-item-close')
          }
        }, service.timeLoading)
      },

      getProposito(){
        if(this.itemModal.COCT_ID > 0 && this.arrProposito.COCT_ID != this.item.COCT_ID){

          // sempre que mudar no FORM a carteira para qual sera gravado o ITEM
          // busca os propositos relationados a carteira selecionada
          this.arrProposito.COCT_ID = this.itemModal.COCT_ID;

          var endPoint = "";
          endPoint += "proposito";
          endPoint += `?usuario=${this.$store.getters.USUA_ID}`;
          endPoint += `&COCT_ID=${this.itemModal.COCT_ID}`;

          service.cofre.busca(endPoint).then(({ STATUS, data, msg }) => {
            if (STATUS == "success") {
              this.arrProposito.LISTA = data

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

            this.time = true;
          });
        }
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = {
            COIT_ID: this.itemModal.COIT_ID
          }

          service.cofre.item.del(option).then( ({STATUS, data, msg}) => {

            if (STATUS == "success") {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item deletado!' }) // message

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }

          })
          this.getDados()
        }
        service.closeModal('CofreModal-item-close')
      },
    },
    
    watch: {
      'itemModal'(newValue, oldValue) {
        this.getProposito()
      }
    },
  }
</script>