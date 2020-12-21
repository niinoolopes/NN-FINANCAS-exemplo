<template>
  <section>
    
    <PageContentNav>
      <template v-slot:menu> </template>
      <template v-slot:btn>
        <ButtonplusItem :setItem='setItem' novo='novo' target='InvestimentoModalCorretora' />
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentNav>

    <Table colspan='6' :timeTable='timeTable' :itemsTable='itemsTable' >
      <template v-slot:thead>
          <th scope="col" class="responsivo text-center">#</th>
          <th scope="col" class="th-m-100 text-left"    >Descrição</th>
          <th scope="col" class="th-m-100 text-left"    >CPNJ</th>
          <th scope="col" class="th-m-100 text-left"    >Site</th>
          <th scope="col" class="th-m-100 text-center"  >Status</th>
          <th></th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item,i) in itemsTable" :key="i">
          <th class="responsivo text-center">{{item.INCR_ID}}</th>
          <td class="td-m-120 text-left"    >{{item.INCR_DESCRICAO}} <small>({{item.INCR_ID}})</small></td>
          <td class="th-m-100 text-left"    >{{item.INCR_CPNJ}}</td>
          <td class="th-m-100 text-left"    >{{item.INCR_SITE}}</td>
          <td class="th-m-100 text-center"  >{{item.INCR_STATUS == 1? 'Ativa' : 'Inativa'}}</td>
          <td> <TdiconEdit :setItem='setItem' :ID='item.INCR_ID' target='InvestimentoModalCorretora' /> </td>
        </tr>
      </template>
    </Table>

    <Modal modal='modal-md' :salvar="salvar" :deletar="btnDelete" :idItem="itemModal.INCR_ID" :timeModal="timeModal" idModal="InvestimentoModalCorretora" >
      <Column class="col-12">
        <label for="form-INCR_DESCRICAO">Descrição</label>
        <input id="form-INCR_DESCRICAO" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INCR_DESCRICAO"
          :disabled='!timeModal' >
      </Column>

      <Column class="col-12 col-sm-6 col-lg-4">
        <label for="form-INCR_CPNJ">CNPJ</label>
        <input id="form-INCR_CPNJ" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INCR_CPNJ"
          :disabled='!timeModal' >
      </Column>

      <Column class="col-12 col-sm-6 col-lg-8">
        <label for="form-INCR_SITE">Site</label>
        <input id="form-INCR_SITE" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INCR_SITE"
          :disabled='!timeModal' >
      </Column>

      <Column class="col-12" v-if="itemModal.INCR_ID != 'novo'" >
        <div class="form-check m-0">
          <input id="INCR_STATUS" type="checkbox" class="form-check-input" 
            v-model="itemModal.INCR_STATUS"
            :disabled='!timeModal' >
          <label for="INCR_STATUS" class="form-check-label">Corretora {{itemModal.INCR_STATUS ? 'Ativa' : 'Inativa'}}</label>
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
        INCR: 'init',
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
          
          service.config.invest.corretora.get(option).then( ({STATUS, data, msg}) => {
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
          option.INCR_ID = this.INCR
          
          service.config.invest.corretora.get(option).then( ({STATUS, data, msg}) => {
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

      initModal() {
        service.initForm([
          "INCR_DESCRICAO",
          "INCR_CPNJ",
          "INCR_SITE"
        ])

        this.itemModal = {
          INCR_ID: 'novo',
          INCR_DESCRICAO: '',
          INCR_CPNJ: '',
          INCR_SITE: '',
          INCR_STATUS: 1,
        }
      },
      
      setItem(INCR) {
        this.INCR = INCR

        this.btnDelete = (INCR <= 4) ? 'deletar' : this.deletar

        this.initModal()
        
        if(INCR == 'novo') {
          this.timeModal = true

        } else if ( INCR == 'geDados') {
          this.getState();
          this.getDados()

        } else if( INCR != 'init' || INCR != 'getDados' ){
          this.getItemID()
        } 
      } ,

      // --

      salvar() {
        let data = {}
        data.INCR_DESCRICAO = this.itemModal.INCR_DESCRICAO
        data.INCR_CPNJ      = this.itemModal.INCR_CPNJ
        data.INCR_SITE      = this.itemModal.INCR_SITE
        data.INCR_STATUS    = this.itemModal.INCR_STATUS
        data.USUA_ID        = this.$store.getters.USUA_ID

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.INCR_ID = this.itemModal.INCR_ID
        option.data = data

        let checkForm = {}
        checkForm.INCR_DESCRICAO = this.itemModal.INCR_DESCRICAO
       
        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INCR_ID == 'novo'){
              service.config.invest.corretora.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Corretora cadastrado!' }) // message
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
              service.config.invest.corretora.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Corretora atualizada!' }) // message
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
            service.closeModal('InvestimentoModalCorretora-close')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { INCR_ID: this.itemModal.INCR_ID }
          service.config.invest.corretora.del(option).then( ({STATUS, data, msg}) => {

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
        service.closeModal('InvestimentoModalCorretora-close')
      },

      getState(){
        let option = { USUA_ID: this.$store.getters.USUA_ID }
        service.config.invest.corretora.get(option).then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            this.$store.commit('INVESTIMENTO_CORRETORA', data);
          }
        })
      },
    
    },
  }
</script>