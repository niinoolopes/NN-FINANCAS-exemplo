<template>
  <section>
    
    <PageContentNav>
        <template v-slot:menu> </template>
        <template v-slot:btn>
          <ButtonplusItem :setItem='setItem' novo='novo' target='InvestimentoModalAtivoTipo' />
          <ButtongetDados :getDados='getDados' />
        </template>
    </PageContentNav>

    <Table colspan='8' :timeTable='timeTable' :itemsTable='itemsTable' >
      <template v-slot:thead>
        <th scope="col" class="responsivo text-center">#</th>
        <th scope="col" class="th-m-120 text-left"   >Tipo</th>
        <th scope="col" class="th-m-100 text-left"   >Tipo Ativo</th>
        <th scope="col" class="th-m-100 text-center" >Status</th>
        <th></th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item,i) in itemsTable" :key="i">
          <th class="responsivo text-center">{{item.INAT_ID}}</th>
          <td class="td-m-120 text-left"    >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
          <td class="td-m-100 text-left"    >{{item.INAT_DESCRICAO}} <small>({{item.INAT_ID}})</small></td>
          <td class="td-m-100 text-center"  >{{item.INAV_STATUS == 1? 'Ativo' : 'Inativo'}}</td>
          <td><TdiconEdit :setItem='setItem' :ID='item.INAT_ID' target='InvestimentoModalAtivoTipo' /></td>
        </tr>
      </template>
    </Table>
    
    <Modal modal='modal-md' :salvar="salvar" :deletar="btnDelete" :idItem="itemModal.INAT_ID" :timeModal="timeModal" idModal="InvestimentoModalAtivoTipo" >
      <Column class="col-12">
        <label for="form-INAT_DESCRICAO">Descrição</label>
        <input id="form-INAT_DESCRICAO" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INAT_DESCRICAO" 
          :disabled='!timeModal'>
      </Column>

      <Column class="col-6">
        <label for="form-INTP_ID">Tipo de Investimento</label>
        <select class="form-control form-control-sm" id="form-INTP_ID" 
          v-model="itemModal.INTP_ID"
          :disabled='!timeModal'>
          <option value="">Selecione...</option>
          <option v-for="(t,i) in $store.getters.I_Tipos" :key="i" :value="t.INTP_ID">{{t.INTP_DESCRICAO}}</option>
        </select>
      </Column> 

      <Column v-if="itemModal.INAT_ID != 'novo'" class="col-12">
        <div class="form-check mt-2 mb-0">
          <input id="form-INAT_STATUS" type="checkbox" class="form-check-input" 
            v-model="itemModal.INAT_STATUS"
            :disabled='!timeModal'>
          <label for="form-INAT_STATUS" class="form-check-label">Ativo {{itemModal.INAT_STATUS ? 'Ativo' : 'Inativo'}}</label>
        </div>
      </Column>
    </Modal>

  </section>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: {
      TdiconEdit:     () => import('@/components/Td_iconEdit'),
      ButtongetDados: () => import('@/components/Button_getDados'),
      ButtonplusItem: () => import('@/components/Button_plusItemModal'),
      Modal:          () => import('@/components/Modal'),
      Table:          () => import('@/components/Table'),
    },

    data() {
      return {
        timeTable: false,
        itemsTable: [],
        INAT: 'init',
        timeModal: false,  
        itemModal: {},
        btnDelete: null,
      }
    },

    created () {
      this.getDados();
    },

    methods: {
      getDados() {
        this.timeTable = false;
        this.itemsTable = 'buscando';

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID

          service.config.invest.ativoTipo.get(option).then( ({STATUS, data, msg}) => {
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
      
      getItemID() {
        this.timeModal = false;

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.INAT_ID = this.INAT

          service.config.invest.ativoTipo.get(option).then( ({STATUS, data, msg}) => {
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

      initModal(INAT_ID) {
        service.initForm([
          "INAT_DESCRICAO",
        ])

        this.itemModal = {
          INAT_ID: 'novo',
          INAT_DESCRICAO: '',
          INAT_STATUS: 1
        }
      },
      
      setItem(INAT) {
        this.INAT = INAT

        this.btnDelete = (INAT <= 7) ? 'deletar' : this.deletar

        this.initModal()
        
        if(INAT == 'novo') {
          this.timeModal = true

        } else if ( INAT == 'geDados') {
          this.getState();
          this.getDados()

        } else if( INAT != 'init' || INAT != 'getDados' ){
          this.getItemID()
        } 
      },

      // --

      salvar() {
        let data = {}
        data.INAT_DESCRICAO = this.itemModal.INAT_DESCRICAO
        data.INAT_STATUS    = this.itemModal.INAT_STATUS ? 1 : 0;
        data.INTP_ID        = this.itemModal.INTP_ID
        data.USUA_ID        = this.$store.getters.USUA_ID

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.INAT_ID = this.itemModal.INAT_ID
        option.data = data

        let checkForm = {}
        checkForm.INAT_DESCRICAO = this.itemModal.INAT_DESCRICAO

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INAT_ID == 'novo'){
              service.config.invest.ativoTipo.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ativo tipo cadastrado!' }) // message
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
              service.config.invest.ativoTipo.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ativo tipo atualizada!' }) // message
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
            service.closeModal('InvestimentoModalAtivoTipo-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { INAT_ID: this.itemModal.INAT_ID }
          service.config.invest.ativoTipo.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ativo tipo excluido!' }) // message
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
        service.closeModal('InvestimentoModalAtivoTipo-close')
      },

      getState(){
        let option = { USUA_ID: this.$store.getters.USUA_ID }
        service.config.invest.ativoTipo.get(option).then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            this.$store.commit('INVESTIMENTO_ATIVO_TIPO', data);
          }
        })
      },
    },
    
  }
</script>

<style lang="scss" scoped>

</style>