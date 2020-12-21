<template>
  <!-- <div class="modal fade" id="InvestimentoModal-Rendimento">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header py-1 px-2">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="InvestimentoModal-Rendimento-close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body opacity-fetch" :class="time ? '' : 'opacity-fetch-active'">
          <form>

          </form>
        </div>
        <div class="modal-footer p-2">
          <button type="button" class="btn btn-sm btn-outline-info" @click="salvar">Salvar</button>
          <button v-if="this.itemModal.INAR_ID != 'novo'" type="button" class="btn btn-sm btn-outline-danger" @click="deletar">Deletar</button>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-dismiss="modal">Fechar</button>
        </div>
      </div>
    </div>
  </div> -->
  <Modal modal='modal-lg' :salvar="salvar" :deletar="deletar" :idItem="itemModal.INAR_ID" :timeModal="time" idModal="InvestimentoModal-Rendimento" >
    <div class="d-flex flex-wrap">
      <Column class="form-group col-6 col-md-3 col-xl-2"> <!-- Corretora -->
        <label for="form-INCR_ID">Corretora</label>
        <select class="form-control form-control-sm" id="form-INCR_ID" v-model="itemModal.INCR_ID" @change="validaForm">
          <option value="">Selecione...</option>
          <option v-for="c in $store.getters.I_CorretorasAtivas" :key="c.INCR_ID" :value="c.INCR_ID">{{c.INCR_DESCRICAO}}</option>
        </select>
      </Column>

      <Column class="form-group col-6 col-md-3 col-xl-2"> <!-- Tipo de Invest -->
        <label for="form-INTP_ID">Tipo de Invest</label>
        <select class="form-control form-control-sm" id="form-INTP_ID" v-model="itemModal.INTP_ID" @change="validaForm">
          <option value="">Selecione...</option>
          <option v-for="t in $store.getters.I_Tipos" :key="t.INTP_ID" :value="t.INTP_ID">{{t.INTP_DESCRICAO}}</option>
        </select>
      </Column>
      
      <Column class="form-group col-6 col-md-3 col-xl-2"> <!-- Tipo Ativo -->
        <label for="form-INAT_ID">Tipo Ativo</label>
        <select class="form-control form-control-sm" id="form-INAT_ID" v-model="itemModal.INAT_ID" @change="validaForm">
          <option value="">Selecione...</option>
          <option v-for="t in $store.getters.I_AtivoTipos(itemModal.INTP_ID)" :key="t.INAT_ID" :value="t.INAT_ID">{{t.INAT_DESCRICAO}}</option>
        </select>
      </Column>

      <Column class="form-group col-6 col-md-3 col-xl-2"> <!-- Ativo -->
        <label for="form-INAV_ID">Ativo</label>
        <select class="form-control form-control-sm" id="form-INAV_ID" v-model="itemModal.INAV_ID" @change="validaForm">
          <option value="">Selecione...</option>
          <option v-for="t in $store.getters.I_AtivoAtivos({INAT_ID: itemModal.INAT_ID, INTP_ID: itemModal.INTP_ID})" :key="t.INAV_ID" :value="t.INAV_ID">{{t.INAV_CODIGO}}</option>
        </select>
      </Column>

      <Column class="form-group col-6 col-md-3 col-xl-2"> <!-- Tiro de rendimento -->
        <label for="form-INAR_TIPO">Tipo</label>
        <select class="form-control form-control-sm" id="form-INAR_TIPO" v-model="itemModal.INAR_TIPO" @change="validaForm">
          <option value="">Selecione...</option>
          <option v-show="itemModal.INTP_ID == 1" value="R">Rendimento</option>
          <option v-show="itemModal.INTP_ID == 2" value="D">Dividendo</option>
          <option v-show="itemModal.INTP_ID == 2" value="J">Juros sobre Capital</option>
        </select>
      </Column>

      <Column class="form-group col-6 col-md-3 col-xl-2"> <!-- Data -->
        <label for="form-INAR_DATA">Data</label>
        <input class="form-control form-control-sm" type="date" id="form-INAR_DATA" v-model="itemModal.INAR_DATA">
      </Column>
      
      <Column class="form-group col-12"> <!-- Status -->
        <div class="form-group form-check m-0">
          <input type="checkbox" class="form-check-input" id="INAR_STATUS" v-model="itemModal.INAR_STATUS">
          <label class="form-check-label" for="INAR_STATUS">Rendimento {{itemModal.INAR_STATUS ? 'Ativo' : 'Inativo'}}</label>
        </div>
      </Column>

    </div>

    <br v-if="div_dadosItem">
    <br v-if="div_dadosItem">

    <Table v-if="div_dadosItem" colspan='3' :timeTable='time' :itemsTable='[dadosItem]' class="my-3 w-100">
      <template v-slot:thead>
        <th class="text-right" scope="col">Qnt Cotas</th>
        <th class="text-right" scope="col">Bruto</th>
        <th class="text-right" scope="col">Aplicado</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 1" scope="col">Rend. Mês</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 1" scope="col">Rend. Total</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 2" scope="col">Div. Mês</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 2" scope="col">Div. Total</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 2" scope="col">JSCP Mês</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 2" scope="col">JSCP Total</th>
        <th class="text-right" v-if="itemModal.INTP_ID == 2" scope="col">Preco Médio</th>
      </template>
      <template v-slot:tbody>
        <tr>
          <td class="text-right">{{dadosItem.COTAS}}</td>
          <td class="text-right">{{dadosItem.BRUTO | vReal }}</td>
          <td class="text-right">{{dadosItem.TOTAL | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 1">{{dadosItem.MES_RENDIMENTO   | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 1">{{dadosItem.TOTAL_RENDIMENTO | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 2">{{dadosItem.MES_DIVIDENDO    | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 2">{{dadosItem.TOTAL_DIVIDENDO  | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 2">{{dadosItem.MES_JSCP         | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 2">{{dadosItem.TOTAL_JSCP       | vReal }}</td>
          <td class="text-right" v-if="itemModal.INTP_ID == 2">{{dadosItem.PRECO_MEDIO      | vReal }}</td>
        </tr>
      </template>
    </Table>

    <br v-if="div_INAR_VALOR !== 'init'">

    <div class="d-flex flex-wrap">
      <div class="px-1 mb-2 form-group col-12"> <!-- RENDA FIXA -->

        <!-- TODOS CAMPOS PREENCHIDOS / DADOS NAO ENCONTRADO -->
        <template v-if="div_INAR_VALOR == 0">
          <label for="form-INAR_VALOR"> Não foi encontrado '<b>{{itemModal.INAV_CODIGO}}</b>' na corretora '<b>{{itemModal.INCR_DESCRICAO}}</b>': </label>
        </template>


        <!-- RENDA FIXA / TODOS CAMPOS PREENCHIDOS / NOVO / DADOS ENCONTRADO -->
        <template v-if="div_INAR_VALOR == 1">
          <label for="form-INAR_VALOR"> Adicione abaixo o <b>saldo</b> atual de '<b>{{itemModal.INAV_CODIGO}}</b>' que esta na corretora '<b>{{itemModal.INCR_DESCRICAO}}</b>': </label>
          <input class="form-control form-control-sm col-12 col-md-4 col-xl-2" type="number" step="0.01" id="form-INAR_VALOR" @change="setValorRendaFixa">

          <div v-show="itemModal.INAR_VALOR != ''"> <span>O rendimento a ser gravado será de: <b>{{itemModal.INAR_VALOR}}</b>. </span> </div>
        </template>


        <!-- RENDA FIXA / TODOS CAMPOS PREENCHIDOS / ID / DADOS ENCONTRADO -->
        <template v-if="div_INAR_VALOR == 2">
          <label for="form-INAR_VALOR"> Adicione abaixo o <b>saldo</b> atual de '<b>{{itemModal.INAV_CODIGO}}</b>' que esta na corretora '<b>{{itemModal.INCR_DESCRICAO}}</b>': </label>
          <input class="form-control form-control-sm col-12 col-md-4 col-xl-2" type="number" step="0.01" id="form-INAR_VALOR" v-model="itemModal.INAR_VALOR">

          <div v-show="itemModal.INAR_VALOR != ''"> <span>O rendimento a ser atualizado será de: <b>{{itemModal.INAR_VALOR}}</b>. </span> </div>
        </template>


        <!-- RENDA VARIAVEL / TODOS CAMPOS PREENCHIDOS / NOVO / DADOS ENCONTRADO -->
        <template v-if="div_INAR_VALOR == 3">
          <label for="form-INAR_VALOR"> Adicione abaixo o <b>valor</b> correspondente ao '<b>Dividendo</b>' de '<b>{{itemModal.INAV_CODIGO}}</b>' que esta na corretora '<b>{{itemModal.INCR_DESCRICAO}}</b>': </label>
          <input class="form-control form-control-sm col-12 col-md-4 col-xl-2" type="number" step="0.01" id="form-INAR_VALOR" v-model="itemModal.INAR_VALOR">
        </template>

      </div>
    </div>
  </Modal>

</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['INAR', 'setItem'],

    components: {
      Modal: () => import('@/components/Modal'),
      Table: () => import('@/components/Table'),
    },

    data() {
      return {
        arrTipoAtivos: [],
        arrAtivos: [],

        itemModal: {},
        time: false,
        
        dadosItem: {
          BRUTO: 0,
          TOTAL: 0,
          MES_RENDIMENTO: 0,
          TOTAL_RENDIMENTO: 0,
        },
        div_dadosItem: false,
        div_INAR_VALOR: 'init',
      }
    },

    methods: {
      changeCorretora(){
        // if( this.itemModal.INCR_ID == '') {
        //   this.itemModal.INTP_ID = ''
        // }
        this.validaForm()
      },
      changeTipo(){
        // if( this.itemModal.INTP_ID == '') {
        //   this.itemModal.INAT_ID = ''
        //   this.itemModal.INAV_ID = ''

        // } else {
        //   if(this.itemModal.INTP_ID == 1) this.itemModal.INAR_TIPO = 'R'
        //   if(this.itemModal.INTP_ID == 2) this.itemModal.INAR_TIPO = 'D'
        // }

        this.validaForm()
      },
      changeTipoAtivo(){
        // if(this.itemModal.INAT_ID == ''){
        //   this.itemModal.INAV_ID = ''
        // }
        this.validaForm()
      },

      // changeAtivo(){
      //   if( this.itemModal.INAR_ID == 'novo' ) {
          
      //     if(this.itemModal.INAV_ID) {
      //       // identifica descrição
      //       this.itemModal.INAV_CODIGO = this.$store.state.investimento.ativo.filter( i => i.INAV_ID == this.itemModal.INAV_ID )[0].INAV_CODIGO
      //     }
      //   }

      //   this.validaForm()
      // },

      // changeRendimento() {
      //   this.validaForm()
      // },

      // aux

      setValorRendaFixa(event){
        this.itemModal.INAR_VALOR = Number(+event.target.value - this.dadosItem.BRUTO).toFixed(2)
      },

      validaForm() {
        var checkForm = {
          INCR_ID: this.itemModal.INCR_ID,
          INTP_ID: this.itemModal.INTP_ID,
          INAT_ID: this.itemModal.INAT_ID,
          INAV_ID: this.itemModal.INAV_ID,
          INAR_TIPO: this.itemModal.INAR_TIPO,
          INAR_DATA: this.itemModal.INAR_DATA,
        }

        setTimeout(() => {
          if(service.checkForm(checkForm)){
            this.getDadosAtivo()
          } else{
            this.div_dadosItem = false
          }
        }, 0);
      },
      
      // --

      initModal(){
        this.div_INAR_VALOR = 'init'
        this.div_dadosItem = false;
        this.arrTipoAtivos = []
        this.arrAtivos     = []

        service.initForm([
          'INCR_ID',
          'INTP_ID',
          'INAT_ID',
          'INAV_ID',
          'INAR_TIPO',
          'INAR_DATA',
        ])

        this.itemModal = {
          INAR_ID : 'novo',
          INAR_STATUS: 1,
          INAR_TIPO : '',
          INAR_VALOR : '',
          INAR_DATA : service.dataHoje(),
          INCR_ID : '',
          INCR_DESCRICAO : '',
          INTP_ID : '',
          INTP_DESCRICAO : '',
          INAT_ID : '',
          INAT_DESCRICAO : '',
          INAV_ID : '',
          INAV_CODIGO : '',
        }
      },

      getItemID(){
        this.time = false
        
        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.INAR_ID = this.INAR

        service.invest.rendimento.get(option).then( ({STATUS, data, msg}) => {
          
          if(STATUS == 'success'){
            if(data.length > 0){
              this.itemModal = data[0]
              this.validaForm()
              // this.changeTipoAtivo()
              // this.getDadosAtivo()
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

          this.time = true
        })
      },

      getDadosAtivo(){
        service.initForm([
          'INCR_ID',
          'INTP_ID',
          'INAT_ID',
          'INAV_ID',
          'INAR_TIPO',
          'INAR_DATA',
        ])

        var url = '';
        url += 'componente';
        url += `?usuario=${this.$store.getters.USUA_ID}`;
        url += `&dataAte=${this.$store.getters.Periodo}`;
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`;
        url += `&INCR_ID=${this.itemModal.INCR_ID}`;
        url += `&INAV_ID=${this.itemModal.INAV_ID}`;
        url += `&retorno=I_carteiraComposicao`;

        service.busca(url)
        .then( ({STATUS, data, msg}) => {
           if(STATUS == 'success'){

            const { items } = data.I_carteiraComposicao

            if(items.length > 0) {
              this.dadosItem = items[0]
              this.div_dadosItem = true;

              if(this.itemModal.INTP_ID == 1 && this.itemModal.INAR_ID == 'novo')
                this.div_INAR_VALOR = 1

              if(this.itemModal.INTP_ID == 1 && this.itemModal.INAR_ID != 'novo')
                this.div_INAR_VALOR = 2

              if(this.itemModal.INTP_ID == 2)
                this.div_INAR_VALOR = 3

            } else {
              this.div_INAR_VALOR = 0
              this.div_dadosItem  = false;

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
      },

      // --

      salvar(){
        this.time = false

        let data = {}
        data.INAR_ID     = this.itemModal.INAR_ID
        data.INAR_TIPO   = this.itemModal.INAR_TIPO
        data.INAR_VALOR  = this.itemModal.INAR_VALOR
        data.INAR_DATA   = this.itemModal.INAR_DATA
        data.INAR_STATUS = this.itemModal.INAR_STATUS
        data.INAV_ID     = this.itemModal.INAV_ID
        data.INCR_ID     = this.itemModal.INCR_ID
        data.INCT_ID     = this.$store.getters.I_INCT_ID

        let option = {}
        option.USUA_ID = this.$store.getters.USUA_ID
        option.INAR_ID = this.itemModal.INAR_ID
        option.data = data

        let checkForm = {}
        if(this.itemModal.INAR_ID == 'novo')
        checkForm.INAR_TIPO  = this.itemModal.INAR_TIPO
        checkForm.INAR_DATA  = this.itemModal.INAR_DATA
        checkForm.INAR_VALOR = this.itemModal.INAR_VALOR

        setTimeout( () => {
          if(service.checkForm(checkForm)){
            if(this.itemModal.INAR_ID == 'novo'){
              service.invest.rendimento.post(option).then( ({STATUS, data, msg}) => {
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
              service.invest.rendimento.put(option).then( ({STATUS, data, msg}) => {
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
          
            service.closeModal('InvestimentoModal-Rendimento-close')
            this.time = true
            this.setItem('getDados')
          }
        }, service.timeLoading)
      },

      deletar(){
        var comfirm = confirm("ao excluir será apagado permanentemente as informações, deseja continuar?")

        if(comfirm){

          let option = {}
            option.USUA_ID = this.$store.getters.USUA_ID
            option.INAR_ID = this.itemModal.INAR_ID
          
          service.invest.rendimento.del(option).then( ({STATUS, data, msg}) => {
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
          this.setItem('getDados')
        }
        service.closeModal('InvestimentoModal-Rendimento-close')
      },

    },

    watch: {
      'INAR'(newValue, oldValue) {

        this.initModal();

        if(newValue == 'novo') {
          this.time = true

        } else if(newValue != 'init' || newValue != 'getDados' ) {
          this.dadosItem = {
            BRUTO: 0,
            TOTAL: 0,
            MES_RENDIMENTO: 0,
            TOTAL_RENDIMENTO: 0,
          }
          this.getItemID()

        }
      }
    },
  }
</script>