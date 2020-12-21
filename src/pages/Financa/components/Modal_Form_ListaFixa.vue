<template>
  <Modal modal='modal-lg' :salvar="salvar" :deletar="deletar" :idItem="itemModal.FNLF_ID" :timeModal="timeModal" idModal="FinancaModal-ListaFixa" >
    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label class="m-0" for="form-FINC_ID"><small>Carteira</small></label>
      <select class="form-control form-control-sm" id="form-FINC_ID" v-model="itemModal.FINC_ID">
        <option value="">Selecione...</option>
        <option v-for="(cart,i) in $store.getters.F_CarteirasAtivas" :key="i" :value="cart.FINC_ID">{{cart.FINC_DESCRICAO}}</option>
      </select>
    </Column>

    <Column class="col-lg-8 col-xl-10 d-none d-md-block"></Column>

    <Column class="form-group col-6 col-md-4 col-xl-2">
        <label class="m-0" for="form-FITP_ID"><small>Tipo</small></label>
        <select class="form-control form-control-sm" id="form-FITP_ID" v-model="itemModal.FITP_ID" :disabled="itemModal.FINC_ID == '' ? true : false">
          <option value="">Selecione...</option>
          <option v-for="(t,i) in $store.getters.F_TiposAtivos" :key="i" :value="t.FITP_ID">{{t.FITP_DESCRICAO}} {{t.selected}}</option>
        </select>
    </Column>

    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label class="m-0" for="form-FIGP_ID"><small>Grupo</small></label>
        <select class="form-control form-control-sm" id="form-FIGP_ID" v-model="itemModal.FIGP_ID" :disabled="itemModal.FITP_ID == '' ? true : false">
          <option value="">Selecione...</option>
          <option v-for="(g,i) in $store.getters.F_GruposAtivos(itemModal.FINC_ID, itemModal.FITP_ID)" :key="i" :value="g.FIGP_ID" >{{g.FIGP_DESCRICAO}}</option>
        </select>
    </Column>

    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label class="m-0" for="form-FICT_ID"><small>Categoria</small></label>
      <select class="form-control form-control-sm" id="form-FICT_ID" v-model="itemModal.FICT_ID" :disabled="itemModal.FIGP_ID == '' ? true : false">
        <option value="">Selecione...</option>
        <option v-for="(c,i) in $store.getters.F_CategoriasAtivas(itemModal.FIGP_ID)" :key="i" :value="c.FICT_ID" >{{c.FICT_DESCRICAO}}</option>
      </select>
    </Column>

    <Column class="form-group col-12 col-md-4 col-xl-2">
      <label class="m-0" for="form-FNIS_ID"><small>Situação</small></label>
      <select class="form-control form-control-sm" id="form-FNIS_ID" v-model="itemModal.FNIS_ID" :disabled="itemModal.FICT_ID == '' ? true : false">
        <option value="">Selecione...</option>
        <option v-for="(s,i) in $store.getters.F_Situacoes" :key="i" :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
      </select>
    </Column>
    
    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label class="m-0" for="form-FNIT_VALOR"><small>Valor</small></label>
      <input class="form-control form-control-sm" type="number" id="form-FNIT_VALOR" step="0.01" v-model="itemModal.FNIT_VALOR"  :disabled="itemModal.FNIS_ID == '' ? true : false">
    </Column>
    
    <Column class="form-group col-6 col-md-4 col-xl-2">
      <label class="m-0" for="form-FNIT_DATA"><small>Data</small></label>
      <input class="form-control form-control-sm" type="date" id="form-FNIT_DATA" v-model="itemModal.FNIT_DATA"  :disabled="itemModal.FNIT_VALOR == '' ? true : false">
    </Column>

    <Column class="form-group mb-2 col-12">
      <label class="m-0" for="form-FNIT_OBS"><small>Observação</small></label>
      <textarea class="form-control form-control-sm" id="form-FNIT_OBS" v-model="itemModal.FNIT_OBS" rows="3" ></textarea>
    </Column>
  
    <Column class="col-12">
      <div class="form-group form-check m-0">
        <input type="checkbox" class="form-check-input" v-model="itemModal.FNIT_STATUS">
        <label class="form-check-label" id="form-FNIT_STATUS" for="FNIT_STATUS">{{itemModal.FNIT_STATUS ? 'Registro Ativo' : 'Registro Inativo'}}</label>
      </div>
    </Column>
  </Modal>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['FNLF', 'getDados'],
      
    components: { 
      Modal: () => import('@/components/Modal'),
    },

    data() {
      return {
        timeModal: false,
        itemModal: {}
      }
    },

    methods: {
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
          FNLF_ID: 'novo',
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

        // this.arrGrupo     = []
        // this.arrCategoria = []
      },

      getItemID(FNLF){
        this.timeModal = false

        var url = ''
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&FINC_ID=${this.$store.getters.F_FINC_ID}`
        url += `&FNLF_ID=${this.FNLF}`
        url += `&retorno=F_listaFixa`
        
        service.busca( url ).then(({ STATUS, data, msg }) => {
          if (STATUS == "success") {
            this.itemModal = data.F_listaFixa.items[0];

          } else if (STATUS == "error") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

          } else if (STATUS == "token") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
            this.$store.commit("SET_LOGIN", false);
          }

          this.timeModal = true;
        });


        // let option = {}
        // option.USUA_ID = this.$store.getters.USUA_ID
        // option.FNIT_ID = FNIT_ID

        // service.finc.item.get(option).then( ({STATUS, data, msg}) => {
        //   if(STATUS == 'success'){
        //     if(data.length > 0){
        //       this.itemModal = data[0]
        //       this.selectGrupo()
        //       this.selectCategoria()
        //     } else {
        //       this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
        //     }
        //   }
        //   else if(STATUS == 'error'){
        //     this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
        //   }
        //   else if(STATUS == 'token'){
        //     this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
        //     this.$store.commit('SET_LOGIN', false);
        //   }

        // })

        // this.timeModal = true
      },

      
      salvar(){
        let data = {}
        data.FINC_ID     = this.itemModal.FINC_ID
        data.FITP_ID     = this.itemModal.FITP_ID
        data.FIGP_ID     = this.itemModal.FIGP_ID
        data.FICT_ID     = this.itemModal.FICT_ID
        data.FNIS_ID     = this.itemModal.FNIS_ID
        data.FNIT_VALOR  = this.itemModal.FNIT_VALOR
        data.FNIT_DATA   = this.itemModal.FNIT_DATA
        data.FNIT_OBS    = this.itemModal.FNIT_OBS
        data.FNIT_STATUS = this.itemModal.FNIT_STATUS
        data.USUA_ID     = this.usuario.USUA_ID

        let option = {}
        option.USUA_ID = this.usuario.USUA_ID
        option.FNLF_ID = this.itemModal.FNLF_ID
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
            if(this.itemModal.FNLF_ID == 'novo'){
              service.finc.listaFixa.post(option).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
                }
              })
            }else{
              service.finc.listaFixa.put(option).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
                }
              })
            }
            service.closeModal('FinancaModal-ListaFixa-close')
          }
        }, service.timeLoading)

      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){
          let option = this.itemModal
          service.finc.listaFixa.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message
              this.getDados()
            }
          })
        }
        service.closeModal('FinancaModal-ListaFixa-close')
      },
    },

    watch: {
      'FNLF'(FNLF, oldValue) {
        this.initModal()

        if(FNLF == 'novo') {
          this.timeModal = true

        }else if(FNLF != 'init' && FNLF != 'getDados') {
          this.timeModal = false
          this.getItemID(FNLF)

        }
      }
    },
  }
</script>