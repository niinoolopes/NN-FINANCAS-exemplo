
<template>
  <section>
    <PageContentNav>
        <!-- <template v-slot:menu></template> -->
      <template v-slot:btn>
        <ButtonplusItem   :add='novoItem' ID='' class='mr-1'/>
        <ButtonSalvar     :salvar='salvar' :disabled='true' class='mr-1' />
        <ButtonClearDados :limpar ='clearItems' class='mr-1' />
        <ButtongetDados   :getDados='getDados' />
      </template>
    </PageContentNav>
      
    <Table colspan='8' :timeTable='time' :itemsTable='itemsTable' >
      <template v-slot:thead>
        <th class="th-m-100 text-left"  scope="col">Tipo Ativo</th>
        <th class="th-m-100 text-left"  scope="col">Ativo</th>
        <th class="th-m-85 text-center" scope="col">Cotação</th>
        <th class="th-m-85 text-center" scope="col">Data</th>
        <th class="th-m-85 text-center" scope="col">Status</th>
        <th class="th-m-85 text-center" scope="col">remove</th>
        <th></th>
      </template>
      <template v-slot:tbody>
        <tr v-for="(item, i) in itemsTable" :key="i">

          <td class="td-m-120 text-left">
            <select class="form-control form-control-sm" :id="`form-INAT_ID-${i}`" v-model="item.INAT_ID">
              <option value="">Selecione...</option>
              <option v-for="t in $store.getters.I_AtivoTiposAtivos(item.INTP_ID)" :key="t.INAT_ID" :value="t.INAT_ID">{{t.INAT_DESCRICAO}}</option>
            </select>
          </td>

          <td class="td-m-120 text-left">
            <select class="form-control form-control-sm" :id="`form-INAV_ID-${i}`" v-model="item.INAV_ID">
              <option value="">Selecione...</option>
              <option v-for="t in $store.getters.I_AtivoAtivos({INAT_ID: item.INAT_ID})" :key="t.INAV_ID" :value="t.INAV_ID">{{t.INAV_CODIGO}}</option>
            </select>
          </td>

          <td class="td-m-120">
            <input class="form-control form-control-sm" type="number" min='0' step="0.01" :id="`form-INAC_VALOR-${i}`" v-model="item.INAC_VALOR">
          </td>

          <td>
            <input class="form-control form-control-sm" type="date" :id="`form-INAC_DATA-${i}`" v-model="item.INAC_DATA">
          </td>

          <td class="td-m-120">
            <div class="d-flex justify-content-center w-100 pt-1">
              <input type="checkbox" class="cursor-pointer" :id="`INAC_STATUS-${i}`" v-model="item.INAC_STATUS">
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
    
  </section>
</template>

<script>
  import service from '@/service.js'

  export default {
    
    components: {
      ButtonplusItem:  () => import('@/components/Button_plusItem'),
      ButtongetDados:  () => import('@/components/Button_getDados'),
      ButtonSalvar:    () => import('@/components/Button_salvar'),
      ButtonClearDados:  () => import('@/components/Button_clearDados'),
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
        url += `&dataAte=${this.$store.getters.Periodo}`;
        url += '&retorno=I_cotacaoSugestao'
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
              this.items = data.I_cotacaoSugestao
              this.itemsTable = data.I_cotacaoSugestao
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

      salvar(){
        var checks = []

        this.itemsTable.map( (item,i) => {
          let checkForm = {}
          checkForm[`INAT_ID-${i}`]    = item.INAT_ID
          checkForm[`INAV_ID-${i}`]    = item.INAV_ID
          checkForm[`INAC_VALOR-${i}`] = item.INAC_VALOR
          checkForm[`INAC_DATA-${i}`]  = item.INAC_DATA

          if(!service.checkForm(checkForm)){
            checks.push(i)
          }
        })

        if(checks.length > 0) return false

        this.itemsTable.map( (item,i) => {
          let data = {}
          data.INAC_VALOR  = item.INAC_VALOR
          data.INAC_DATA   = item.INAC_DATA
          data.INAC_STATUS = item.INAC_STATUS ? 1 : 0
          data.INAV_ID     = item.INAV_ID

          let option = {}
            option.USUA_ID = this.$store.getters.USUA_ID
            option.data = data

          setTimeout( () => {
            service.invest.cotacao.post(option).then( ({STATUS, data, msg}) => {
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
            [`INAT_ID-${i}`],
            [`INAC_VALOR-${i}`],
            [`INAC_DATA-${i}`],
            [`INAV_ID-${i}`],
          ])
        })
      },



      clearItems(){
        this.itemsTable = this.items
      },
      
      novoItem(ID){
        this.itemsTable.unshift({
          INAC_DATA: '',
          INAC_STATUS: '',
          INAT_ID: '',
          INAV_CODIGO: '',
          INAV_ID: '',
          INCR_ID: '',
          INCT_ID: '',
          INTP_ID: '2',
          PRECO_COTACAO: '',
          PRECO_MEDIO: '',
        })
      },

      deleteItem(i){
        this.itemsTable = this.itemsTable.filter( (e,index) => i != index)
      },

    },
  }
</script>