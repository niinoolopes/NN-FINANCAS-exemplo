<template>
  <section>
    
    <PageContentNav>
        <template v-slot:menu> </template>
        <template v-slot:btn>
          <ButtonplusItem :setItem='setItem' novo='novo' target='InvestimentoModalAtivo' />
          <ButtongetDados :getDados='getDados' />
        </template>
    </PageContentNav>

    <Table colspan='8' :timeTable='timeTable' :itemsTable='itemsTable' >
      <template v-slot:thead>
        <th scope="col" class="responsivo text-center">#</th>
        <th scope="col" class="th-m-120 text-left"   >Tipo</th>
        <th scope="col" class="th-m-100 text-left"   >Tipo Ativo</th>
        <th scope="col" class="th-m-100 text-left"   >Código</th>
        <th scope="col" class="th-m-100 text-left"   >Descrição</th>
        <th scope="col" class="th-m-100 text-left"   >Liquidez</th>
        <th scope="col" class="th-m-100 text-center" >Data Venc</th>
        <th scope="col" class="th-m-100 text-center" >Status</th>
        <th></th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item,i) in itemsTable" :key="i">
          <th class="responsivo text-center">{{item.INAV_ID}}</th>
          <td class="td-m-120 text-left"    >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
          <td class="td-m-100 text-left"    >{{item.INAT_DESCRICAO}} <small>({{item.INAT_ID}})</small></td>
          <td class="td-m-100 text-left"    >{{item.INAV_CODIGO}}</td>
          <td class="td-m-100 text-left"    >{{item.INAV_DESCRICAO}}</td>
          <td class="td-m-100 text-left"    >{{item.INAV_LIQUIDEZ}}</td>
          <td class="td-m-100 text-left"    >{{item.INAV_VENC | convertDate}}</td>
          <td class="td-m-100 text-center"  >{{item.INAV_STATUS == 1? 'Ativo' : 'Inativo'}}</td>
          <td><TdiconEdit :setItem='setItem' :ID='item.INAV_ID' target='InvestimentoModalAtivo' /></td>
        </tr>
      </template>
    </Table>

    <Modal modal='modal-md' :salvar="salvar" :deletar="deletar" :idItem="itemModal.INCT_ID" :timeModal="timeModal" idModal="InvestimentoModalAtivo" >
      <Column class="col-12 col-md-6"> <!-- Tipo -->
        <label for="form-INTP_ID">Tipo</label>
        <select id="form-INTP_ID" class="form-control form-control-sm mb-md-2" 
          v-model="itemModal.INTP_ID"
          :disabled='!timeModal'>
          <option value="">Selecione...</option>
          <option v-for="t in $store.getters.I_Tipos" :key="t.INTP_ID" :value="t.INTP_ID">{{t.INTP_DESCRICAO}}</option>
        </select>
      </Column>

      <Column class="col-12 col-md-6"> <!-- Tipo de Ativo -->
        <label for="form-INAT_ID">Tipo de Ativo</label>
        <select id="form-INAT_ID" class="form-control form-control-sm mb-md-2" 
          v-model="itemModal.INAT_ID"
          :disabled='!timeModal'>
          <option value="">Selecione...</option>
          <option v-for="item in $store.getters.I_AtivoTipos(itemModal.INTP_ID)" :key="item.INAT_ID" :value="item.INAT_ID">{{item.INAT_DESCRICAO}}</option>
        </select>
      </Column>

      <Column class="col-12 col-md-6 col-lg-4"> <!-- Descrição -->
        <label for="form-INAV_CODIGO">Código</label>
        <input id="form-INAV_CODIGO" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INAV_CODIGO" 
          :disabled='!timeModal'>
      </Column>

      <Column class="col-12 col-md-6 col-lg-8"> <!-- Descrição -->
        <label for="form-INAV_DESCRICAO">Descrição</label>
        <input id="form-INAV_DESCRICAO" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INAV_DESCRICAO" 
          :disabled='!timeModal'>
      </Column>

      <Column class="col-12 col-sm-6 col-lg-4"> <!-- CNPJ -->
        <label for="form-INAV_CPNJ">CNPJ</label>
        <input id="form-INAV_CPNJ" class="form-control form-control-sm mb-md-2" type="text" 
          v-model="itemModal.INAV_CPNJ"
          :disabled='!timeModal' >
      </Column>

      <Column class="col-12 col-sm-6 col-lg-8"> <!-- Site -->
        <label for="form-INAV_SITE">Site</label>
        <input id="form-INAV_SITE" class="form-control form-control-sm mb-md-2" type="url" 
          v-model="itemModal.INAV_SITE"
          :disabled='!timeModal' >
      </Column>
      
      <Column class="col-12 col-sm-6 col-lg-4"> <!-- Liquidez -->
        <label for="form-INAV_LIQUIDEZ">Liquidez</label>
        <select id="form-INAV_LIQUIDEZ" class="form-control form-control-sm mb-md-2" 
          v-model="itemModal.INAV_LIQUIDEZ"
          :disabled='!timeModal'>
          <option value="">Selecione...</option>
          <option value="sim">Sim</option>
          <option value="não">Não</option>
        </select>
      </Column>

      <Column class="col-12 col-sm-6 col-lg-4"> <!-- Data Venc. -->
        <label for="form-INAV_VENC">Data Venc.</label>
        <input id="form-INAV_VENC" class="form-control form-control-sm mb-md-2" type="date" 
          v-model="itemModal.INAV_VENC"
          :disabled='!timeModal'>
      </Column>

      <Column v-if="itemModal.INAV_ID != 'novo'" class="col-12">
        <div class="form-check mt-2 mb-0">
          <input id="form-INAV_STATUS" type="checkbox" class="form-check-input" 
            v-model="itemModal.INAV_STATUS"
            :disabled='!timeModal'>
          <label for="form-INAV_STATUS" class="form-check-label">Ativo {{itemModal.INAV_STATUS ? 'Ativo' : 'Inativo'}}</label>
        </div>
      </Column>
    </Modal>

  </section>
</template>

<script>
  import service from "@/service.js"

  export default {
    components: {
      ButtongetDados:    () => import('@/components/Button_getDados'),
      ButtonplusItem:    () => import('@/components/Button_plusItemModal'),

      Modal: () => import('@/components/Modal'),
      Table: () => import('@/components/Table'),
      TdiconEdit:        () => import('@/components/Td_iconEdit'),
    },

    data() {
      return {
        arrAtivos: [],

        timeTable: false,
        itemsTable: [],
        INAV: 'init',
        timeModal: false,  
        itemModal: {},
      }
    },

    created () {
      this.getDados();
    },

    methods: {
      changeTipo(){
        this.arrAtivos = this.$store.getters.I_AtivoTipos(this.itemModal.INTP_ID)
      },
      // --

      getDados(commit = false) {
        this.timeTable = false;
        this.itemsTable = 'buscando';

        setTimeout( () => {
          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID

          service.config.invest.ativo.get(option).then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.itemsTable = data
              if(commit) this.$store.commit('INVESTIMENTO_ATIVO', data);
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
          option.INAV_ID = this.INAV

          service.config.invest.ativo.get(option).then( ({STATUS, data, msg}) => {
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
          "INTP_ID",
          "INAV_ID",
          "INAV_CODIGO",
          "INAT_ID",
          "INAV_LIQUIDEZ",
          "INAV_VENC",
        ])

        this.itemModal = {
          INAV_ID: 'novo',
          INTP_ID: '',
          INAT_ID: '',
          INAV_CODIGO: '',
          INAV_LIQUIDEZ: '',
          INAV_STATUS: 1,
          INAV_VENC: '',
        }
      },
      
      setItem(INAV) {
        this.INAV = INAV

        this.initModal()
        
        if(INAV == 'novo') {
          this.timeModal = true

        } else if (INAV == 'getDados') {
          this.getDados()

        } else if(INAV != 'init'){
          this.getItemID()
        } 
      },

      // --

      salvar() {
        let data = {}
        data.INTP_ID        = this.itemModal.INTP_ID
        data.INAT_ID        = this.itemModal.INAT_ID
        data.INAV_CODIGO    = this.itemModal.INAV_CODIGO
        data.INAV_DESCRICAO = this.itemModal.INAV_DESCRICAO
        data.INAV_CPNJ      = this.itemModal.INAV_CPNJ || ''
        data.INAV_SITE      = this.itemModal.INAV_SITE || ''
        data.INAV_LIQUIDEZ  = this.itemModal.INAV_LIQUIDEZ || ''
        data.INAV_VENC      = this.itemModal.INAV_VENC || ''
        data.INAV_STATUS    = this.itemModal.INAV_STATUS ? 1 : 0
        data.USUA_ID        = this.$store.getters.USUA_ID

        let checkForm = {}
        checkForm.INTP_ID        = this.itemModal.INTP_ID
        checkForm.INAT_ID        = this.itemModal.INAT_ID
        checkForm.INAV_CODIGO    = this.itemModal.INAV_CODIGO
        
        if(this.itemModal.INTP_ID == 1) {
          checkForm.INAV_LIQUIDEZ = this.itemModal.INAV_LIQUIDEZ
          checkForm.INAV_VENC     = this.itemModal.INAV_VENC
        }
        if(this.itemModal.INTP_ID == 2) {
          checkForm.INAV_CPNJ = this.itemModal.INAV_CPNJ
          data.INAV_LIQUIDEZ  = 'não'
        }
        
        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.INAV_ID = this.itemModal.INAV_ID
        option.data = data

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INAV_ID == 'novo'){
              service.config.invest.ativo.post(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ativo cadastrado!' }) // message
                  this.getDados('commit')
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
              service.config.invest.ativo.put(option).then( ({STATUS, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ativo atualizada!' }) // message
                  this.getDados('commit')
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
            service.closeModal('InvestimentoModalAtivo-close')
            this.setItem('init')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { INCR_ID: this.itemModal.INAV_ID }
          service.config.invest.ativo.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item excluido!' }) // message
              this.getDados('commit')
              this.setItem('init');
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

      // getState(){
      //   let option = {}
      //   option.USUA_ID = this.$store.getters.USUA_ID
        
      //   service.config.invest.ativo.get(option).then( ({STATUS, data, msg}) => {
      //     if(STATUS == 'success'){
      //       this.$store.commit('INVESTIMENTO_ATIVO', data);
      //     }
      //   })
      // },

    },
  }
</script>