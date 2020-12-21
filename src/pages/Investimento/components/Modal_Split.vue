<template>
  <Modal modal='modal-lg' :salvar="salvar" :deletar="deletar" :idItem="itemModal.INAS_ID" :timeModal="timeModal" idModal="InvestimentoModal-Split" >
    <Column class="form-group col-6 col-lg-3">
      <label for="form-INAT_ID">Tipo de Ativo</label>
      <select class="form-control form-control-sm" id="form-INAT_ID" v-model="itemModal.INAT_ID">
        <option value="">Selecione...</option>
        <option v-for="t in $store.getters.I_AtivoTiposAtivos(2)" :key="t.INAT_ID" :value="t.INAT_ID">{{t.INAT_DESCRICAO}}</option>
      </select>
    </Column>
    
    <Column class="form-group col-6 col-lg-2">
      <label for="form-INAV_ID">Ativo</label>
      <select class="form-control form-control-sm" id="form-INAV_ID" v-model="itemModal.INAV_ID">
        <option value="">Selecione...</option>
        <option v-for="t in $store.getters.I_AtivoAtivos({INAT_ID: itemModal.INAT_ID})" :key="t.INAV_ID" :value="t.INAV_ID">{{t.INAV_CODIGO}}</option>
      </select>
    </Column>

    <Column class="form-group col-6 col-lg-2"> 
      <label for="form-INAS_TIPO">Tipo</label>
      <select class="form-control form-control-sm" id="form-INAS_TIPO" v-model="itemModal.INAS_TIPO">
        <option value="">Selecione...</option>
        <option value="S">Split</option>
        <option value="I">Inplit</option>
      </select>
    </Column>
    
    <Column class="form-group col-6 col-lg-2"> 
      <label for="form-INAS_QUANTIDADE">Quantidade</label>
      <input class="form-control form-control-sm" type="number" min='0' step="1" id="form-INAS_QUANTIDADE" v-model="itemModal.INAS_QUANTIDADE">
    </Column>
    
    <Column class="form-group col-6 col-lg-3"> 
      <label for="form-INAS_DATA">Valor</label>
      <input class="form-control form-control-sm" type="date" id="form-INAS_DATA" v-model="itemModal.INAS_DATA">
    </Column>
    
    <Column class="form-group col-12"> 
      <div class="form-group form-check m-0">
        <input type="checkbox" class="form-check-input" id="INAS_STATUS" v-model="itemModal.INAS_STATUS">
        <label class="form-check-label" for="INAS_STATUS">{{itemModal.INAS_STATUS ? 'Ativo' : 'Inativo'}}</label>
      </div>
    </Column>
  </Modal>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['INAS', 'setItem'],
      
    components: { 
      Modal: () => import('@/components/Modal'),
    },

    data() {
      return {
        itemModal: {},
        timeModal: false,
      }
    },

    methods: {
      initModal(){
        service.initForm([
          'INAT_ID',
          'INAV_ID',
          'INAS_TIPO',
          'INAS_QUANTIDADE',
          'INAS_DATA',
        ])

        this.itemModal = {
          INAS_ID: 'novo',
          INAS_TIPO: 'S',
          INAS_QUANTIDADE: '',
          INAS_DATA: service.dataHoje(),
          INAS_STATUS: 1,
          INAT_ID: '',
          INAV_ID: '',
          USUA_ID: '',
        }
      },

      getItemID(){
        this.itemModal = {}
        this.timeModal = false
        
        setTimeout( () => {

          service.invest.split.get({
            USUA_ID: this.$store.getters.USUA_ID,
            INAS_ID: this.INAS,
          })
          .then( ({STATUS, data, msg}) => {
            
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
        data.INAS_ID         = this.itemModal.INAS_ID || ''
        data.INAS_TIPO       = this.itemModal.INAS_TIPO
        data.INAS_QUANTIDADE = this.itemModal.INAS_QUANTIDADE
        data.INAS_DATA       = this.itemModal.INAS_DATA
        data.INAS_STATUS     = this.itemModal.INAS_STATUS ? 1 : 0
        data.INAV_ID         = this.itemModal.INAV_ID

        let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.INAS_ID = this.itemModal.INAS_ID
          option.data = data

        let checkForm = {}
          checkForm.INAT_ID         = this.itemModal.INAT_ID
          checkForm.INAV_ID         = this.itemModal.INAV_ID
          checkForm.INAS_TIPO       = this.itemModal.INAS_TIPO
          checkForm.INAS_QUANTIDADE = this.itemModal.INAS_QUANTIDADE
          checkForm.INAS_DATA       = this.itemModal.INAS_DATA

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INAS_ID == 'novo'){
              service.invest.split.post(option).then( ({STATUS, data, msg}) => {
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
              service.invest.split.put(option).then( ({STATUS, data, msg}) => {
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
          
            service.closeModal('InvestimentoModal-Split-close')
            this.setItem('getDados')
          }
        }, service.timeLoading)

      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          
          service.invest.split.del({
            INAS_ID: this.itemModal.INAS_ID
          })
          .then( ({STATUS, data, msg}) => {
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
        service.closeModal('InvestimentoModal-Split-close')
        this.setItem('getDados')
      },

    },

    watch: {
      'INAS'(newValue, oldValue) {

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