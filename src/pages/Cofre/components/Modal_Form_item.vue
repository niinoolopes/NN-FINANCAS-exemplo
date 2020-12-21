<template>
  <Modal modal='modal-lg' :salvar="salvar" :deletar="deletar" :idItem="itemModal.COCT_ID" :timeModal="timeModal" :idModal="idModal" >
    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label for="form-COCT_ID">Carteira</label>
      <select class="form-control form-control-sm" id="form-COCT_ID" v-model="itemModal.COCT_ID" :disabled='disabledModal'>
        <option value="">Selecione...</option>
        <option v-for="(c,i) in $store.getters.C_CarteirasAtivas" :key="i" :value="c.COCT_ID">{{c.COCT_DESCRICAO}}</option>
      </select>
    </Column>
    
    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label for="form-COTP_ID">Tipo</label>
      <select class="form-control form-control-sm" id="form-COTP_ID" v-model="itemModal.COTP_ID" :disabled='disabledModal'>
        <option value="">Selecione...</option>
        <option v-for="(t,i) in $store.getters.C_Tipos" :key="i" :value="t.COTP_ID">{{t.COTP_DESCRICAO}}</option>
      </select>
    </Column>

    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label for="form-COIT_VALOR">Valor</label>
      <input class="form-control form-control-sm" type="number" step="0.01" id="form-COIT_VALOR" v-model="itemModal.COIT_VALOR" :disabled='disabledModal'>
    </Column>
    
    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label for="form-COIT_DATA">Data</label>
      <input class="form-control form-control-sm" type="date" id="form-COIT_DATA" v-model="itemModal.COIT_DATA" :disabled='disabledModal'>
    </Column>

    <Column class="form-group col-12 col-md-12">
      <label for="form-COIT_OBS">Observação</label>
      <textarea class="form-control form-control-sm" id="form-COIT_OBS" v-model="itemModal.COIT_OBS" :disabled='disabledModal' rows="5" ></textarea>
    </Column>

    <Column class="form-group col-12">
      <div class="form-group form-check m-0">
        <input type="checkbox" class="form-check-input" id="form-COIT_STATUS" v-model="itemModal.COIT_STATUS" :disabled='disabledModal'>
        <label class="form-check-label" for="form-COIT_STATUS">{{itemModal.COIT_STATUS ? 'Registro Ativo' : 'Registro Inativo'}}</label>
      </div>
    </Column>

    <Column class="px-1 px-md-3 col-12">
      <hr class="my-2">
    </Column>

    <Column class="form-group col-12">
      <label for="form-COIT_PROPOSITO">Proposito</label>

      <div class="row">

        <div class="col-6 col-md-4 col-xl-2 px-1 px-md-2 m-0 mb-2 ">
          <div class="p-1 p-md-2 border d-flex justify-content-center align-items-center">
            <input type="text" class="form-control form-control-sm" id="form-COIT_PROPOSITO" v-model="itemModal.COIT_PROPOSITO" :disabled='disabledModal'>
          </div>
        </div>

        <div v-for="(text,i) in arrProposito.LISTA" :key="i" class="col-6 col-md-4 col-xl-2 px-1 px-md-2 m-0 mb-2">
          <div class="p-1 p-md-2 border d-flex justify-content-center align-items-center">
            <input class="mr-2" :id="`texto-${i}`" type="radio" :value="text.COIT_PROPOSITO" v-model="itemModal.COIT_PROPOSITO" :disabled='disabledModal'>
            <label class="m-0" :for="`texto-${i}`">{{text.COIT_PROPOSITO}}</label>
          </div>
        </div>

      </div>
    </Column>
  </Modal>
</template>

<script>
  import service from '@/service.js'

  export default {
    props:['COCT','setItem'],
    
    components: { 
      Modal: () => import('@/components/Modal'),
    },

    data() {
      return {
        idModal: "CofreModal-item",
        timeModal: false,
        disabledModal: false,
        itemModal: {
          COCT_ID: 0
        },
        arrProposito: {
          COCT_ID: 0,
          LISTA: []
        },
      }
    },

    methods: {
      initModal(){
        service.initForm([
          'COCT_ID',
          'COTP_ID',
          'COIT_VALOR',
          'COIT_DATA',
          'COIT_OBS',
          'COIT_PROPOSITO',
        ])

        this.itemModal = {
          COCT_ID: '',
          COTP_ID: '',
          COIT_VALOR: 0,
          COIT_DATA: service.dataHoje(),
          COIT_OBS: '',
          COIT_STATUS: 1,
          COIT_PROPOSITO: '',
        }
      },
        
      getItemID(COIT_ID){
        this.timeModal = false
        this.disabledModal = true

        service.cofre.item.get({
        USUA_ID: this.$store.getters.USUA_ID,
        COIT_ID: COIT_ID
        })
        .then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            if(data.length > 0){
              this.itemModal = data[0]

              setTimeout(() => {
                this.getProposito()
              }, 250);
              
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
        this.disabledModal = false
      },

      // --

      getProposito(){
        if(this.itemModal.COCT_ID > 0 && this.arrProposito.COCT_ID != this.itemModal.COCT_ID){

          // sempre que mudar no FORM a carteira para qual sera gravado o ITEM
          // busca os propositos relationados a carteira selecionada
          this.arrProposito.COCT_ID = this.itemModal.COCT_ID;

          this.arrProposito.LISTA = []

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

            this.timeModal = true;
          });
        }
      },

      // --

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

        if(service.checkForm(checkForm)){
          if(this.itemModal.COCT_ID == 'novo'){
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

          service.closeModal(`${this.idModal}-close`)
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
        }
        service.closeModal('CofreModal-item-close')
        // setInterval(() => this.setItem('init') , 1000);
      },
    },
    
    watch: {
      'COCT'(newValue, oldValue) {
        
  
        if(newValue == 'novo') {
          this.initModal();
          this.timeModal = true

        } else if(newValue != 'init' && newValue != 'getDados') {
          this.initModal();
          this.timeModal = false
          this.getItemID(newValue)
        }
      }
    },
  }
</script>