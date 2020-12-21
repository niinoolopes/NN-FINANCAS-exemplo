<template>
  <Modal modal='modal-lg' :salvar="salvar" :deletar="deletar" :idItem="itemModal.INAR_ID" :timeModal="timeModal" idModal="InvestimentoModal-Cotacao" >
    <Column class="form-group col-6 col-md-6 col-lg-3">
      <label for="form-INAT_ID">Tipo de Ativo</label>
      <select class="form-control form-control-sm" id="form-INAT_ID" v-model="itemModal.INAT_ID">
        <option value="">Selecione...</option>
        <option v-for="t in $store.getters.I_AtivoTiposAtivos(2)" :key="t.INAT_ID" :value="t.INAT_ID">{{t.INAT_DESCRICAO}}</option>
      </select>
    </Column>
    
    <Column class="form-group col-6 col-md-6 col-lg-3">
      <label for="form-INAV_ID">Ativo</label>
      <select class="form-control form-control-sm" id="form-INAV_ID" v-model="itemModal.INAV_ID">
        <option value="">Selecione...</option>
        <option v-for="t in $store.getters.I_AtivoAtivos({INAT_ID: itemModal.INAT_ID})" :key="t.INAV_ID" :value="t.INAV_ID">{{t.INAV_CODIGO}}</option>
      </select>
    </Column>
    
    <Column class="px-1 mb-2 form-group col-6 col-md-6 col-lg-3"> 
      <label for="form-INAC_VALOR">Valor</label>
      <input class="form-control form-control-sm" type="number" step="0.01" id="form-INAC_VALOR" v-model="itemModal.INAC_VALOR">
    </Column>
    
    <Column class="px-1 mb-2 form-group col-6 col-md-6 col-lg-3"> 
      <label for="form-INAC_DATA">Valor</label>
      <input class="form-control form-control-sm" type="date" id="form-INAC_DATA" v-model="itemModal.INAC_DATA">
    </Column>
    
    <Column class="px-1 mb-2 form-group col-12"> 
      <div class="form-group form-check m-0">
        <input type="checkbox" class="form-check-input" id="INAC_STATUS" v-model="itemModal.INAC_STATUS">
        <label class="form-check-label" for="INAC_STATUS">{{itemModal.INAC_STATUS ? 'Ativo' : 'Inativo'}}</label>
      </div>
    </Column>
  </Modal>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['INAC', 'setItem'],

    components: { 
        Modal: () => import('@/components/Modal'),
    },

    data() {
      return {
        timeModal: false,
        itemModal: {},
      }
    },

    methods: {
      initModal(){
        service.initForm([
          'INAT_ID',
          'INAC_VALOR',
          'INAC_DATA',
          'INAV_ID',
        ])

        this.itemModal = {
          INAC_ID : 'novo',
          INAC_VALOR : '',
          INAC_DATA : service.dataHoje(),
          INAC_STATUS: 1,
          INAT_ID: '',
          INAV_ID : '',
          USUA_ID : '',
        }
      },

      getItemID(){
        this.itemModal = {}
        this.timeModal = false
        
        setTimeout( () => {
          var options = {}
          options.USUA_ID = this.$store.getters.USUA_ID
          options.INAC_ID = this.INAC

          service.invest.cotacao.get(options).then( ({STATUS, data, msg}) => {
            
            if(STATUS == 'success'){
              if(data.length > 0){
                this.itemModal = data[0]
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

            this.timeModal = true
          })
        }, service.timeLoading150)
      },

      // --

      salvar(){
        let data = {}
        data.INAC_ID     = this.itemModal.INAC_ID
        data.INAC_VALOR  = this.itemModal.INAC_VALOR
        data.INAC_DATA   = this.itemModal.INAC_DATA
        data.INAC_STATUS = this.itemModal.INAC_STATUS ? 1 : 0
        data.INAV_ID     = this.itemModal.INAV_ID

        let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.INCT_ID = this.$store.getters.I_INCT_ID
          option.INAC_ID = this.itemModal.INAC_ID
          option.data = data

        let checkForm = {}
          checkForm.INAC_VALOR = this.itemModal.INAC_VALOR
          checkForm.INAC_DATA  = this.itemModal.INAC_DATA
          checkForm.INAT_ID    = this.itemModal.INAT_ID
          checkForm.INAV_ID    = this.itemModal.INAV_ID


        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INAC_ID == 'novo'){
              
              service.invest.cotacao.post(option).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
                }
                else if(STATUS == 'error'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
                }
                else if(STATUS == 'token'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
                  this.$store.commit('SET_LOGIN', false);
                }
              })
            }else{
              service.invest.cotacao.put(option).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item atualizado!' }) // message
                }
                else if(STATUS == 'error'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
                }
                else if(STATUS == 'token'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
                  this.$store.commit('SET_LOGIN', false);
                }
              })
            }
          
            service.closeModal('InvestimentoModal-Cotacao-close')
            this.setItem('getDados')
            this.initModal();
          }

        }, service.timeLoading)

      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.INCT_ID = this.$store.getters.I_INCT_ID
          option.INAC_ID = this.itemModal.INAC_ID
          
          service.invest.cotacao.del(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item excluido!' }) // message
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
          })
        }

        service.closeModal('InvestimentoModal-Cotacao-close')
        this.setItem('getDados')
        this.initModal();
      },

    },

    watch: {
      'INAC'(newValue, oldValue) {

        if(newValue == 'novo'){
          this.initModal();
          this.timeModal = true

        } else if(newValue != 'init' && newValue != 'getDados') {
          this.initModal();
          this.timeModal = false
          this.getItemID()
        }
      }
    },
  }
</script>