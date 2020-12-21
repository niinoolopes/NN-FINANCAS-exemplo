<template>
  <div class="modal fade" id="InvestimentoOrdemItem">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header py-1 px-2">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="InvestimentoOrdemItem-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
          <form>

            <h4 class="p-0">Ativo</h4>

            <div class="d-flex flex-wrap">
              <div class="px-1 mb-2 mb-xl-0 form-group col-12 col-md-4 col-xl-2">
                <label for="form-INTP_ID">Tipo de Investimento</label>
                <select class="form-control form-control-sm" id="form-INTP_ID" v-model="itemModal.INTP_ID" @change='changeTIPO'>
                  <option value="">Selecione...</option>
                  <option v-for="t in $store.getters.I_Tipos" :key="t.INTP_ID" :value="t.INTP_ID">{{t.INTP_DESCRICAO}}</option>
                </select>
              </div>
              
              <div class="px-1 mb-2 mb-xl-0 form-group col-6 col-md-4 col-xl-2">
                <label for="form-INAT_ID">Tipo de Ativo</label>
                <select class="form-control form-control-sm" id="form-INAT_ID" v-model="itemModal.INAT_ID" @change="chanteTipoAtivo">
                  <option value="">Selecione...</option>
                  <option v-for="t in $store.getters.I_AtivoTiposAtivos(itemModal.INTP_ID)" :key="t.INAT_ID" :value="t.INAT_ID">{{t.INAT_DESCRICAO}}</option>
                </select>
              </div>

              <div class="px-1 mb-2 mb-xl-0 form-group col-6 col-md-4 col-xl-2">
                <label for="form-INAV_ID">Ativo</label>
                <select class="form-control form-control-sm" id="form-INAV_ID" v-model="itemModal.INAV_ID">
                  <option value="">Selecione...</option>
                  <option v-for="t in $store.getters.I_AtivoAtivos({INAT_ID: itemModal.INAT_ID})" :key="t.INAV_ID" :value="t.INAV_ID">{{t.INAV_CODIGO}}</option>
                </select>
              </div>

              <div class="px-1 mb-2 mb-xl-0 form-group col-6 col-md-4 col-xl-2">
                <label for="form-INIT_CV">C/V</label>
                <select class="form-control form-control-sm" id="form-INIT_CV" v-model="itemModal.INIT_CV">
                  <option value="">Selecione...</option>
                  <option value="C">Compra</option>
                  <option value="V">Venda</option>
                </select>
              </div>
            </div>

            <hr v-if="div_2">

            <h4 v-if="div_2" class="p-0">Nota</h4>
            <div v-if="div_2" class="d-flex flex-wrap">
              <div class="px-1 mb-2 mb-xl-0 form-group col-6 col-md-4 col-xl-2">
                <label for="form-INIT_NEGOCIACAO">Negociação</label>
                <select class="form-control form-control-sm" id="form-INIT_NEGOCIACAO" v-model="itemModal.INIT_NEGOCIACAO">
                  <option value="">Selecione...</option>
                  <option value="1-BOVESPA">1-BOVESPA</option>
                </select>
              </div>

              <div class="px-1 mb-2 mb-xl-0 form-group col-6 col-md-4 col-xl-2">
                <label for="form-INIT_MERCADO">Mercado</label>
                <select class="form-control form-control-sm" id="form-INIT_MERCADO" v-model="itemModal.INIT_MERCADO">
                  <option value="">Selecione...</option>
                  <option value="FRACIONADO">FRACIONADO</option>
                  <option value="AVISTA">AVISTA</option>
                </select>
              </div>

              <div class="px-1 mb-2 mb-xl-0 form-group col-6 col-md-4 col-xl-2">
                <label for="form-INIT_DC">D/C</label>
                <select class="form-control form-control-sm" id="form-INIT_DC" v-model="itemModal.INIT_DC">
                  <option value="">Selecione...</option>
                  <option value="D">D</option>
                  <option value="C">C</option>
                </select>
              </div>
            </div>

            <hr>

            <h4 class="p-0">Quantidade/Valor</h4>
            <div class="d-flex flex-wrap">
              <div class="px-1 mb-2 mb-xl-0 form-group col-4 col-md-4 col-xl-2">
                <label for="form-INIT_COTAS">Cotas</label>
                <input class="form-control form-control-sm" type="number" min='0' step="1" id="form-INIT_COTAS" 
                  @change="modalTotalItem"
                  v-model="itemModal.INIT_COTAS">
              </div>

              <div class="px-1 mb-2 mb-xl-0 form-group col-4 col-md-4 col-xl-2">
                <label for="form-INIT_PRECO_UNICO">Preço Uni</label>
                <input class="form-control form-control-sm" type="number" min='0' step="0.01" id="form-INIT_PRECO_UNICO" 
                  @change="modalTotalItem"
                  v-model="itemModal.INIT_PRECO_UNICO">
              </div>

              <div class="px-1 mb-2 mb-xl-0 form-group col-4 col-md-4 col-xl-2">
                <label for="form-INIT_PRECO_TOTAL">Preço Total</label>
                <input class="form-control form-control-sm" type="number" min='0' step="0.01" id="form-INIT_PRECO_TOTAL" v-model="itemModal.INIT_PRECO_TOTAL" disabled>
              </div>
            </div>

            <hr>
                        
            <div class="px-1 mb-2 form-group col-12"> <!-- Status -->
              <div class="form-group form-check m-0">
                <input type="checkbox" class="form-check-input" id="INIT_STATUS" v-model="itemModal.INIT_STATUS">
                <label class="form-check-label" for="INIT_STATUS">Operação {{itemModal.INIT_STATUS ? 'Ativo' : 'Inativo'}}</label>
              </div>
            </div>


          </form>
        </div>
        <div class="modal-footer p-2">
          <button type="button" class="btn btn-sm btn-outline-info" @click="salvar()">Salvar</button>
          <button v-if="itemModal.INIT_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger" @click="deletar()">Deletar</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import {mapState} from 'vuex';
  import service from '@/service.js'

  export default {
    props: ['setItem','INIT','INOD_ID'],

    computed: { ...mapState(['usuario']) },

    data() {
      return {
        time: false,
        div_2: false,
        itemModal: {},
      }
    },

    methods: {
      changeTIPO(){
        this.div_2 = (this.itemModal.INTP_ID == 2) ? true : false ;
      },

      chanteTipoAtivo(){
        if(this.itemModal.INAT_ID == 4 || this.itemModal.INAT_ID == 5) {
          this.itemModal.INIT_NEGOCIACAO = '1-BOVESPA'
          
          if(this.itemModal.INAT_ID == 4) {
            this.itemModal.INIT_MERCADO = 'FRACIONADO'
            this.itemModal.INIT_DC = 'D'

          } else if(this.itemModal.INAT_ID == 5) {
            this.itemModal.INIT_MERCADO = 'AVISTA'
            this.itemModal.INIT_DC = 'C'
          }
        }
      },

      modalTotalItem(){
        var cotas      = Number(this.itemModal.INIT_COTAS )
        var preco_unit = Number(this.itemModal.INIT_PRECO_UNICO)

        this.itemModal.INIT_PRECO_TOTAL =  Number(cotas * preco_unit).toFixed(2)
      },
      

      // -- MODAL

      initModal(){
        this.itemModal = {
          INIT_ID: 'novo',
          INTP_ID: '2',
          INAT_ID: '',
          INAV_ID: '',
          INIT_CV: 'C',
          INIT_NEGOCIACAO: '',
          INIT_MERCADO: '',
          INIT_DC: '',
          INIT_COTAS: 1,
          INIT_PRECO_UNICO: 50,
          INIT_PRECO_TOTAL: 0,
          INIT_STATUS: 1,
          INOD_ID: '',

          // INAT_DESCRICAO: '',
          // INAT_STATUS: '1',
          // INAV_CODIGO: '',
          // INAV_LIQUIDEZ: 'sim',
          // INAV_STATUS: '1',
          // INAV_VENC: '',
          // INTP_DESCRICAO: '',
          // INTP_STATUS: '',
          USUA_ID: '',
        }

        this.changeTIPO();
      },

      getItemID(){
          service.invest.item.get({
            USUA_ID: this.$store.getters.USUA_ID,
            INIT_ID: this.INIT,
          })
          .then( ({STATUS, data, msg}) => {
            if (STATUS == "success") {
              this.initModal();
              this.itemModal = data[0];
              this.changeTIPO();

            } else if (STATUS == "error") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

            } else if (STATUS == "token") {
              this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
              this.$store.commit("SET_LOGIN", false);

            }
            this.time = true
          })
      },

      // -- CRUD

      salvar(){
        this.modalTotalItem()

        let data = {}
          data.INIT_ID          = this.itemModal.INIT_ID || ''
          data.INTP_ID          = this.itemModal.INTP_ID
          data.INAT_ID          = this.itemModal.INAT_ID
          data.INAV_ID          = this.itemModal.INAV_ID
          data.INIT_CV          = this.itemModal.INIT_CV

          data.INIT_NEGOCIACAO  = this.itemModal.INIT_NEGOCIACAO || ''
          data.INIT_MERCADO     = this.itemModal.INIT_MERCADO || ''
          data.INIT_DC          = this.itemModal.INIT_DC || ''

          data.INIT_COTAS       = this.itemModal.INIT_COTAS
          data.INIT_PRECO_UNICO = this.itemModal.INIT_PRECO_UNICO
          data.INIT_PRECO_TOTAL = this.itemModal.INIT_PRECO_TOTAL
          data.INIT_STATUS      = this.itemModal.INIT_STATUS ? 1 : 0
          data.INOD_ID          = this.INOD_ID || this.itemModal.INOD_ID

        let option = {}
          option.USUA_ID = this.usuario.USUA_ID
          option.INIT_ID = this.itemModal.INIT_ID

        let checkForm = {}
          checkForm.INTP_ID          = this.itemModal.INTP_ID
          checkForm.INAT_ID          = this.itemModal.INAT_ID
          checkForm.INAV_ID          = this.itemModal.INAV_ID
          checkForm.INIT_CV          = this.itemModal.INIT_CV
          checkForm.INIT_COTAS       = this.itemModal.INIT_COTAS
          checkForm.INIT_PRECO_UNICO = this.itemModal.INIT_PRECO_UNICO
          checkForm.INIT_PRECO_TOTAL = this.itemModal.INIT_PRECO_TOTAL

        if(this.itemModal.INTP_ID == 2){
          checkForm.INIT_NEGOCIACAO  = this.itemModal.INIT_NEGOCIACAO
          checkForm.INIT_MERCADO     = this.itemModal.INIT_MERCADO
          checkForm.INIT_DC          = this.itemModal.INIT_DC
        }

        if(service.checkForm(checkForm)){
          if(this.itemModal.INIT_ID == 'novo'){

            service.invest.item.post(option, data).then( ({STATUS, data, msg}) => {
              if(STATUS == 'success'){
                this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
                this.setItem('getDados')
              }
            })
          }else{
            service.invest.item.put(option, data).then( ({STATUS, data, msg}) => {
              if(STATUS == 'success'){
                this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item atualizado!' }) // message
                this.setItem('getDados')
              }
            })
          }
          service.initForm([
            "INTP_ID",
            "INAT_ID",
            "INAV_ID",
            "INIT_NEGOCIACAO",
            "INIT_CV",
            "INIT_MERCADO",
            "INIT_DC",
            "INIT_COTAS",
            "INIT_PRECO_UNICO",
            "INIT_PRECO_TOTAL",
          ])

          service.closeModal('InvestimentoOrdemItem-close')
        }

      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = { INIT_ID: this.itemModal.INIT_ID }
          
          service.invest.item.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item  excluido!' }) // message
            }else{
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao excluir, tente novamente!' }) // message
            }

            this.setItem('getDados')
          })
        }
        service.closeModal('InvestimentoOrdemItem-close')
      },
    },

    watch: {
      'INIT'(valueNew, msgOld) {
        this.initModal();

        if(valueNew === 'novo') {
          this.time = true;

        } else if(valueNew != 'init' && valueNew != 'getDados') {
          this.getItemID()

        }

      },
    },
  }
</script>