<template>
  <section>
    
    <PageContentNav>
      <template v-slot:menu> </template>
      <template v-slot:btn>
        <ButtonplusItem :setItem='setItem' novo='novo' target='InvestimentoModalCarteira' />
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentNav>

    <Table colspan='5' :timeTable='timeTable' :itemsTable='itemsTable' >
      <template v-slot:thead>
        <th class="responsivo text-center" scope="col">#</th>
        <th class="th-m-100 text-left"     scope="col">Descrição</th>
        <th class="th-m-85 text-center"    scope="col">Status</th>
        <th class="th-m-85 text-center"    scope="col">Painel</th>
        <th></th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item,i) in itemsTable" :key="i">
            <th class="responsivo text-center">{{item.INCT_ID}}</th>
            <td class="td-m-100 text-left"    >{{item.INCT_DESCRICAO}}</td>
            <td class="td-m-85 text-center"   >{{item.INCT_STATUS == 1 ? 'Ativa' : 'Inativa'}}</td>
            <td class="td-m-85 text-center"   >{{item.INCT_PAINEL == 1 ? 'x' : '_'}}</td>
            <td> <TdiconEdit :setItem='setItem' :ID='item.INCT_ID' target='InvestimentoModalCarteira' /> </td>
          </tr>
      </template>
    </Table>
  
    <Modal modal='modal-md' :salvar="salvar" :deletar="deletar" :idItem="itemModal.INCT_ID" :timeModal="timeModal" idModal="InvestimentoModalCarteira" >
      <Column class="mb-2 col-12">
        <label for="form-INCT_DESCRICAO">Descrição</label>
        <input id="form-INCT_DESCRICAO" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INCT_DESCRICAO"
          :disabled="!timeModal">
      </Column>

      <Column class="mb-2 col-12">
        <div class="form-group form-check m-0">
          <input id="form-INCT_STATUS" type="checkbox" class="form-check-input" 
            v-model="itemModal.INCT_STATUS"
            :disabled="!timeModal">
          <label for="form-INCT_STATUS" class="form-check-label col p-0">Carteira {{itemModal.INCT_STATUS ? 'Ativo' : 'Inativo'}}</label>
        </div>
      </Column>
      
      <Column v-if="itemModal.INCT_ID != 'novo'" class="col-12">
        <div class="form-group form-check m-0">
          <input id="form-INCT_PAINEL" type="checkbox" class="form-check-input" 
            v-model="itemModal.INCT_PAINEL"
            :disabled="!timeModal">
          <label for="form-INCT_PAINEL" class="form-check-label col p-0">Carteira {{itemModal.INCT_PAINEL ? 'principal' : 'não principal'}}</label>
        </div>
      </Column>
    </Modal>

  </section>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: {
      TdiconEdit:        () => import('@/components/Td_iconEdit'),
      ButtongetDados:    () => import('@/components/Button_getDados'),
      ButtonplusItem:    () => import('@/components/Button_plusItemModal'),
      Modal: () => import('@/components/Modal'),
      Table: () => import('@/components/Table'),
    },

    data() {
      return {
        timeTable: false,
        itemsTable: [],
        INCT: 'init',
        timeModal: false,  
        itemModal: {},
      }
    },

    created () {
      this.getDados();
    },

    methods: {
      getDados(){
        this.timeTable = false;
        this.itemsTable = 'buscando';

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID

          service.config.invest.carteira.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.itemsTable = data
            }
            else if(STATUS == 'error') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.timeTable = true;
          })
        }, service.timeLoading)
      },

      getItemID(){
        this.timeModal = false;

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.INCT_ID = this.INCT

          service.config.invest.carteira.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.itemModal = data[0]
            }
            else if(STATUS == 'error') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
            this.timeModal = true;
          })
        }, service.timeLoading)
      },

      // --

      initModal(){
        service.initForm([
          "INCT_DESCRICAO",
        ])

        this.itemModal = {
          INCT_ID: 'novo',
          INCT_DESCRICAO: '',
          INCT_STATUS: 1,
          INCT_PAINEL: 0,
        }
      },
      
      setItem(INCT) {
        this.INCT = INCT

        this.initModal()
        
        if(INCT == 'novo') {
          this.timeModal = true

        } else if ( INCT == 'getDados') {
          this.getState();
          this.getDados()

        } else if( INCT != 'init' || INCT != 'getDados' ){
          this.getItemID()
        } 
      },

      // --

      salvar() {
        let data = {}
        data.INCT_DESCRICAO = this.itemModal.INCT_DESCRICAO
        data.INCT_STATUS    = this.itemModal.INCT_STATUS ? 1 : 0
        data.INCT_PAINEL    = this.itemModal.INCT_PAINEL ? 1 : 0
        data.USUA_ID        = this.$store.getters.USUA_ID

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.INCT_ID = this.itemModal.INCT_ID
        option.data = data

        let checkForm = {}
        checkForm.INCT_DESCRICAO = this.itemModal.INCT_DESCRICAO

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INCT_ID == 'novo'){
              service.config.invest.carteira.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira cadastrado!' }) // message
                  this.setItem('getDados')
                }
                else if(STATUS == 'error') {
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
                }
                else if(STATUS == 'token') {
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
                  this.$store.commit('SET_LOGIN', false);
                }
              })
            } else {
              service.config.invest.carteira.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Carteira atualizada!' }) // message
                  this.setItem('getDados')
                }
                else if(STATUS == 'error') {
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
                }
                else if(STATUS == 'token') {
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
                  this.$store.commit('SET_LOGIN', false);
                }
              })
            }
            service.closeModal('InvestimentoModalCarteira-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { INCT_ID: this.itemModal.INCT_ID }
          service.config.invest.carteira.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item excluido!' }) // message
              this.setItem('getDados');
            }
            else if(STATUS == 'error') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token') {
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }
          })
        }
        service.closeModal('InvestimentoModalCarteira-close')
      },

      getState(){
        let option = { USUA_ID: this.$store.getters.USUA_ID }
        service.config.invest.carteira.get(option).then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            this.$store.commit('INVESTIMENTO_CARTEIRA', data);
          }
          else if(STATUS == 'error') {
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
          }
          else if(STATUS == 'token') {
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
            this.$store.commit('SET_LOGIN', false);
          }
        })
      },

    },
  }
</script>