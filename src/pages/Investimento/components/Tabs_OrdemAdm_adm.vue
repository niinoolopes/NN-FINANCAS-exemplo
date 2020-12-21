<template>
  <form>
    <PageContentTitle titulo='Dados da Ordem'>
      <template v-slot:btn>
        <ButtongetDados :getDados='getDados' />
      </template>
    </PageContentTitle>

    <div class="d-flex flex-wrap opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
      
      <div class="col-12 col-md-4 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INOD_DATA">Data pregão</label>
        <input class="form-control form-control-sm" type="date" id="form-INOD_DATA" v-model="ordem.INOD_DATA">
      </div>

      <div class="col-6 col-md-4 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INCT_ID">Carteira</label>
        <select class="form-control form-control-sm" id="form-INCT_ID" v-model="ordem.INCT_ID">
          <option value="">Selecione...</option>
          <option v-for="(c,i) in $store.getters.I_CarteirasAtivas" :key="i" :value="c.INCT_ID">{{c.INCT_DESCRICAO}}</option>
        </select>
      </div> 
      
      <div class="col-6 col-md-4 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INCR_ID">Corretora</label>
        <select class="form-control form-control-sm" id="form-INCR_ID" v-model="ordem.INCR_ID">
          <option value="">Selecione...</option>
          <option v-for="(c,i) in $store.getters.I_CorretorasAtivas" :key="i" :value="c.INCR_ID">{{c.INCR_DESCRICAO}}</option>
        </select>
      </div>

      <div v-if="ordem.INOD_ID != 'novo'" class="col-6 col-md-4 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label>CNPJ</label>
        <p class="m-0">{{ordem.INCR_CPNJ}}</p>
      </div>
      
      <div v-if="ordem.INOD_ID != 'novo'" class="col-6 col-md-4 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label>Site</label>
        <a :href="ordem.INCR_SITE" class="d-block text-lowercase m-0" target="_blank">{{ordem.INCR_SITE}}</a>
      </div>
      
      <div class="col-12 form-group px-2">
        <label for="form-INOD_DESCRICAO">Observação</label>
        <textarea class="form-control form-control-sm" id="form-INOD_DESCRICAO" v-model="ordem.INOD_DESCRICAO" rows="4" ></textarea>
      </div>

    </div>

    <hr>

    <PageContentTitle titulo='Taxas' />

    <div class="d-flex flex-wrap opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">

      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_TAXA_LIQUIDACAO">Taxa de Liquidação</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_TAXA_LIQUIDACAO" v-model="ordem.INTX_TAXA_LIQUIDACAO">
      </div>
      
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_TAXA_REGISTRO">Taxa de Registro</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_TAXA_REGISTRO" v-model="ordem.INTX_TAXA_REGISTRO">
      </div>
      
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_TAXA_TERMO_OPERACOES">Taxa Termo de Operações</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_TAXA_TERMO_OPERACOES" v-model="ordem.INTX_TAXA_TERMO_OPERACOES">
      </div>
                
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_TAXA_ANA">Taxa A.N.A.</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_TAXA_ANA" v-model="ordem.INTX_TAXA_ANA">
      </div>

      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_EMOLUMENTOS">Emolumentos</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_EMOLUMENTOS" v-model="ordem.INTX_EMOLUMENTOS">
      </div>

      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_TAXA_OPERACIONAL">Taxa Operacional</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_TAXA_OPERACIONAL" v-model="ordem.INTX_TAXA_OPERACIONAL">
      </div>
                
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_EXECUCAO">Execução</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_EXECUCAO" v-model="ordem.INTX_EXECUCAO">
      </div>
      
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_TAXA_CUSTODIA">Taxa de Custodia</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_TAXA_CUSTODIA" v-model="ordem.INTX_TAXA_CUSTODIA">
      </div>
                
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_IMPOSTOS">Impostos</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_IMPOSTOS" v-model="ordem.INTX_IMPOSTOS">
      </div>

      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_IRRF_OPERACOES">IRRF Operações</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_IRRF_OPERACOES" v-model="ordem.INTX_IRRF_OPERACOES">
      </div>
      
      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_OUTRO">Outros</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_OUTRO" v-model="ordem.INTX_OUTRO">
      </div>

      <div class="col-3 col-md-4 col-lg-3 col-xl-2 form-group px-1 px-md-2 mb-2">
        <label for="form-INTX_VALOR_LIQUIDO_OPERACOES">Valor Liquido Operações</label>
        <input class="form-control form-control-sm" type="number" step="0.01" id="form-INTX_VALOR_LIQUIDO_OPERACOES" v-model="ordem.INTX_VALOR_LIQUIDO_OPERACOES">
      </div>
      
    </div>

    <hr>

    <div class="px-1 mb-2 form-group col-12"> 
      <div class="form-group form-check mb-3">
        <input type="checkbox" class="form-check-input" id="INOD_STATUS" v-model="ordem.INOD_STATUS">
        <label class="form-check-label" for="INOD_STATUS">Ordem {{ordem.INOD_STATUS ? 'Ativa' : 'Inativa'}}</label>
      </div>
    </div>

    <button v-show="time" type="button" class="btn btn-sm btn-outline-info mr-2" @click="salvar">Salvar</button>
    <button v-show="time" v-if="ordem.INOD_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger mr-2" @click="deletar">Excluir</button>
    <button v-show="time" v-if="ordem.INOD_ID == 'novo'" type="reset" class="btn btn-sm btn-outline-info mr-2">Limpar</button>

  </form>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['INOD_ID','setOrdem'],

    components: {
      ButtongetDados:     () => import('@/components/Button_getDados'),
    },

    data() {
      return {
        time: false,
        ordem: {}
      }
    },

    mounted() {
      this.time = false
      this.initOrdem()

      if( this.$store.getters.I_CarteiraPainel ){
        if(this.INOD_ID && this.INOD_ID != 'novo' ){
          this.getDados()
        }
        setTimeout(() => {
          this.time = true
        }, 500);
      }
    },

    methods: {
      initOrdem(){
        this.ordem = {
          INOD_ID: 'novo',
          INOD_DESCRICAO: '',
          INOD_DATA: service.dataHoje(),
          INOD_STATUS: 1,
          INAV_ID: '',
          INCR_ID: '',
          INCT_ID: this.$store.getters.I_INCT_ID,
          INTX_VALOR_LIQUIDO_OPERACOES: '0.00',
          INTX_TAXA_LIQUIDACAO: '0.00',
          INTX_TAXA_REGISTRO: '0.00',
          INTX_TAXA_TERMO_OPERACOES: '0.00',
          INTX_TAXA_ANA: '0.00',
          INTX_EMOLUMENTOS: '0.00',
          INTX_TAXA_OPERACIONAL: '0.00',
          INTX_EXECUCAO: '0.00',
          INTX_TAXA_CUSTODIA: '0.00',
          INTX_IMPOSTOS: '0.00',
          INTX_IRRF_OPERACOES: '0.00',
          INTX_OUTRO: '0.00',
          INTX_STATUS: '0.00',
          USUA_ID: '',
        };
      },

      getDados() {
        this.time = false

        setTimeout( () => {
          var url = ''
          url += 'lista'
          url += `?usuario=${this.$store.getters.USUA_ID}`
          url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
          url += `&INOD_ID=${this.INOD_ID}`
          url += '&retorno=I_ordem'

          service.busca(url)
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.ordem = data.I_ordem.items[0]
            }
            else if(STATUS == 'error'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: msg })
            }
            else if(STATUS == 'token'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: service.arrMessage })
              this.$store.commit('SET_LOGIN', false);
            }

            this.time = true
          })
        }, service.timeLoading)
      },

      salvar(){
        this.time = false

        let data = {}
          data.INOD_ID                      = this.ordem.INOD_ID
          data.INOD_DESCRICAO               = this.ordem.INOD_DESCRICAO
          data.INOD_DATA                    = this.ordem.INOD_DATA
          data.INOD_STATUS                  = this.ordem.INOD_STATUS
          data.INCR_ID                      = this.ordem.INCR_ID
          data.INCT_ID                      = this.ordem.INCT_ID

          data.INTX_ID                      = this.ordem.INTX_ID
          data.INTX_VALOR_LIQUIDO_OPERACOES = this.ordem.INTX_VALOR_LIQUIDO_OPERACOES
          data.INTX_TAXA_LIQUIDACAO         = this.ordem.INTX_TAXA_LIQUIDACAO
          data.INTX_TAXA_REGISTRO           = this.ordem.INTX_TAXA_REGISTRO
          data.INTX_TAXA_TERMO_OPERACOES    = this.ordem.INTX_TAXA_TERMO_OPERACOES
          data.INTX_TAXA_ANA                = this.ordem.INTX_TAXA_ANA
          data.INTX_EMOLUMENTOS             = this.ordem.INTX_EMOLUMENTOS
          data.INTX_TAXA_OPERACIONAL        = this.ordem.INTX_TAXA_OPERACIONAL
          data.INTX_EXECUCAO                = this.ordem.INTX_EXECUCAO
          data.INTX_TAXA_CUSTODIA           = this.ordem.INTX_TAXA_CUSTODIA
          data.INTX_IMPOSTOS                = this.ordem.INTX_IMPOSTOS
          data.INTX_IRRF_OPERACOES          = this.ordem.INTX_IRRF_OPERACOES
          data.INTX_OUTRO                   = this.ordem.INTX_OUTRO
          data.INTX_STATUS                  = this.ordem.INTX_STATUS
          data.USUA_ID                      = this.$store.getters.USUA_ID
          
        let option = {}
          option.INOD_ID = this.ordem.INOD_ID
          option.USUA_ID = this.$store.getters.USUA_ID
          option.data = data

        let checkForm = {}
          checkForm.INOD_DESCRICAO = this.ordem.INOD_DESCRICAO
          checkForm.INOD_DATA      = this.ordem.INOD_DATA
          checkForm.INCR_ID        = this.ordem.INCR_ID
          checkForm.INCT_ID        = this.ordem.INCT_ID


        setTimeout( () => {
          if( service.checkForm(checkForm) ){

            if(this.ordem.INOD_ID == 'novo'){
              service.invest.ordem.post(option, data).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.ordem = data
                  this.setOrdem(data.INOD_ID)
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ordem cadastrado!' }) // message
                  
                }
              })
            }else{
              service.invest.ordem.put(option, data).then( ({STATUS, data, msg}) => {
                if(STATUS == 'success'){
                  this.ordem = data
                  this.setOrdem(data.INOD_ID)
                  this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ordem atualizada!' }) // message
                        
                }
              })
            }

            service.initForm([
              "INOD_DESCRICAO",
              "INOD_DATA",
              "INCR_ID",
              "INCT_ID",
            ])

            this.initOrdem()
          }
          
        }, service.timeLoading)

        this.time = true
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){

          let option = { INOD_ID: this.ordem.INOD_ID }
          service.invest.ordem.del(option).then( ({STATUS, data, msg}) => {

            if(STATUS == 'success'){
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Ordem excluida!' }) // message
              
              setTimeout(() => {
                this.$router.push({name: "InvestimentoOrdem"})
              }, 1000);

            }else{
              this.$store.commit('SET_MESSAGE',{ active: true, type: 'erro', texto: 'Erro ao excluir, tente novamente!' }) // message

            }
          })
        }
      },

    },
  }
</script>