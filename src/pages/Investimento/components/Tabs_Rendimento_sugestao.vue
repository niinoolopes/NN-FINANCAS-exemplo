
<template>
  <div>
    <PageContentNav>
        <!-- <template v-slot:menu></template> -->

      <template v-slot:btn>
        <!-- <ButtonplusItem   :add='novoItem' ID='' class='mr-1'/> -->
        <ButtonSalvar     :salvar='salvar' :disabled='true' class='mr-1' />
        <ButtonClearDados :limpar ='clearItems' class='mr-1' />
        <ButtongetDados   :getDados='getDados' />
      </template>
    </PageContentNav>
      
    <Table colspan='8' :timeTable='time' :itemsTable='itemsTable' >
      <template v-slot:thead>
        <th class="th-m-100 text-left"  scope="col">Corretora</th>
        <th class="th-m-100 text-left"  scope="col">Tipo Inves.</th>
        <th class="th-m-100 text-left"  scope="col">Tipo Ativo</th>
        <th class="th-m-100 text-left"  scope="col">Ativo</th>
        <th class="th-m-85 text-center" scope="col">Tipo Rend.</th>
        <th class="th-m-85 text-center" scope="col">Rendimento</th>
        <th class="th-m-85 text-center" scope="col">Data</th>
        <th class="th-m-85 text-center" scope="col">Status</th>
        <th class="th-m-85 text-center" scope="col">remove</th>
        <th></th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item, i) in itemsTable" :key="i">

          <td class="td-m-120 text-left">
            <select class="form-control form-control-sm" id="form-INCR_ID" v-model="item.INCR_ID" disabled='disabled'>
              <option value="">Selecione...</option>
              <option v-for="c in $store.getters.I_CorretorasAtivas" :key="c.INCR_ID" :value="c.INCR_ID">{{c.INCR_DESCRICAO}}</option>
            </select>
          </td>

          <td class="td-m-120 text-left">
            <select class="form-control form-control-sm" id="form-INTP_ID" v-model="item.INTP_ID" disabled='disabled'>
              <option value="">Selecione...</option>
              <option v-for="t in $store.getters.I_Tipos" :key="t.INTP_ID" :value="t.INTP_ID">{{t.INTP_DESCRICAO}}</option>
            </select>
          </td>

          <td class="td-m-120 text-left">
            <select class="form-control form-control-sm" :id="`form-INAT_ID-${i}`" v-model="item.INAT_ID" disabled='disabled'>
              <option value="">Selecione...</option>
              <option v-for="t in $store.getters.I_AtivoTiposAtivos(item.INTP_ID)" :key="t.INAT_ID" :value="t.INAT_ID">{{t.INAT_DESCRICAO}}</option>
            </select>
          </td>

          <td class="td-m-120 text-left">
            <select class="form-control form-control-sm" :id="`form-INAV_ID-${i}`" v-model="item.INAV_ID" disabled='disabled'>
              <option value="">Selecione...</option>
              <option v-for="t in $store.getters.I_AtivoAtivos({INAT_ID: item.INAT_ID})" :key="t.INAV_ID" :value="t.INAV_ID">{{t.INAV_CODIGO}}</option>
            </select>
          </td>

          <td>
            <select class="form-control form-control-sm" :id="`form-INAR_TIPO-${i}`" v-model="item.INAR_TIPO">
              <option value="">Selecione...</option>
              <option v-show="item.INTP_ID == 1" value="R">Rendimento</option>
              <option v-show="item.INTP_ID == 2" value="D">Dividendo</option>
              <option v-show="item.INTP_ID == 2" value="J">Juros sobre Capital</option>
            </select>
          </td>

          <td class="td-m-120">
            <input class="form-control form-control-sm" type="number" min='0' step="0.01" :id="`form-INAR_VALOR-${i}`" v-model="item.INAR_VALOR">
          </td>

          <td>
            <input class="form-control form-control-sm" type="date" :id="`form-INAR_DATA-${i}`" v-model="item.INAR_DATA">
          </td>

          <td class="td-m-120">
            <div class="d-flex justify-content-center w-100 pt-1">
              <input type="checkbox" class="cursor-pointer" :id="`INAR_STATUS-${i}`" v-model="item.INAR_STATUS">
            </div>
          </td>

          <td>
            <TdiconTrash
              :ID='i'
              :trash="deleteItem"
            />
          </td>
        </tr>
      </template>
    </Table> 
    
  </div>
</template>

<script>
  import service from '@/service.js'

  export default {
    
    components: {
      // ButtonplusItem:  () => import('@/components/Button_plusItem'),
      ButtonSalvar:    () => import('@/components/Button_salvar'),
      ButtonClearDados:  () => import('@/components/Button_clearDados'),
      ButtongetDados:  () => import('@/components/Button_getDados'),
      TdiconTrash: () => import('@/components/Td_iconTrash'),
      Table: () => import('@/components/Table'),
    },
    data() {
      return {
        time: false,
        items: [],
        itemsTable: [],
      }
    },

    mounted () {
      if( this.$store.getters.I_CarteiraPainel ){
        setTimeout(() => { this.getDados() }, service.timeLoading);
      }
    },

    methods: {
      makeUrl() {
        var url = ''
        url += 'lista'
        url += `?usuario=${this.$store.getters.USUA_ID}`
        url += `&INCT_ID=${this.$store.getters.I_INCT_ID}`
        url += `&INTP_ID=2`
        url += `&dataAte=${this.$store.getters.Periodo}`;
        url += '&retorno=I_rendimentoSugestao'
        return url
      },

      getDados(){
        this.time = false
        this.items = [];
        this.itemsTable = 'init';

        setTimeout( () => {
          service.busca( this.makeUrl() )
          .then( ({STATUS, data, msg}) => {
            if(STATUS == 'success'){
              this.items = data.I_rendimentoSugestao
              this.itemsTable = data.I_rendimentoSugestao
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
        }, 1000)

      },

      // --

      salvar(){
        var checks = []

        this.itemsTable.map( (item,i) => {          
          let checkForm = {}
          checkForm[`INAR_TIPO-${i}`]  = item.INAT_ID
          checkForm[`INAR_DATA-${i}`]  = item.INAV_ID
          checkForm[`INAR_VALOR-${i}`] = item.INAR_VALOR

          if(!service.checkForm(checkForm)){
            checks.push(i)
          }
        })

        if(checks.length > 0) return false

        this.itemsTable.map( (item,i) => {

          let data = {}
          data.INAR_TIPO   = item.INAR_TIPO
          data.INAR_VALOR  = item.INAR_VALOR
          data.INAR_DATA   = item.INAR_DATA
          data.INAR_STATUS = item.INAR_STATUS ? 1 : 0
          data.INAV_ID     = item.INAV_ID
          data.INCR_ID     = item.INCR_ID
          data.INCT_ID     = item.INCT_ID

          let option = {}
          option.USUA_ID = this.$store.getters.USUA_ID
          option.INAR_ID = item.INAR_ID
          option.data = data

          setTimeout( () => {
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
          }, service.timeLoading)

          service.initForm([
            [`INAR_TIPO-${i}`],
            [`INAR_DATA-${i}`],
            [`INAR_VALOR-${i}`],
          ])
        })
      },

      // --

      clearItems(){
        this.itemsTable = this.items
      },

      deleteItem(i){
        this.itemsTable = this.itemsTable.filter( (e,index) => i != index)
      },

    },
  }
</script>