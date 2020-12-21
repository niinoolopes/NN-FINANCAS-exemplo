<template>
  <Table colspan='8' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th scope="col" class="responsivo text-center">#</th>
      <th scope="col" class="th-m-85  text-center"  >Seleção</th>
      <th scope="col" class="th-m-100 text-left"    >Grupo</th>
      <th scope="col" class="th-m-100 text-left"    >Categoria</th>
      <th scope="col" class="th-m-85  text-center"  >Valor</th>
      <th scope="col" class="th-m-85  text-center"  >Data</th>
      <th scope="col" class="th-m-85  text-center"  >Tipo</th>
      <th scope="col" class="th-m-100 text-left"    >Situação</th>
      <th scope="col" class="th-m-100 text-center"  >Status</th>
      <th></th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item,i) in items" :key="i">
        <th class="responsivo text-center">{{item.FNLF_ID}}</th>

        <td class="td-m-85 text-center">
          <div class="d-flex justify-content-center">
            <input type="checkbox" class="cursor-pointer" v-model="item.selected">
          </div>
        </td>

        <td class="td-m-100 text-left" >
          <router-link 
            :to="{name: 'FinancaAnaliseGrupo', params: { idFINC: item.FINC_ID, idFIGP: item.FIGP_ID }}"
          >{{ item.FIGP_DESCRICAO }}</router-link>
        </td>

        <td class="td-m-100 text-left">
          <router-link 
            :to="{name: 'FinancaAnaliseGrupo', params: { idFINC: item.FINC_ID, idFIGP: item.FIGP_ID, idFICT: item.FICT_ID }}"
          >{{ item.FICT_DESCRICAO }}</router-link>
        </td>

        <td class="td-m-85 text-center">
          <input class="form-control form-control-sm p-0 m-0 text-center" type="number" step="0.01" id="form-FNIT_VALOR" 
            @change="changeValor(item.FNLF_ID, item.FNIT_VALOR)"
            v-model="item.FNIT_VALOR">
        </td>

        <td class="td-m-85 text-center">
          <input class="form-control form-control-sm p-0 m-0 text-center" type="date" id="form-FNIT_DATA" 
            @change="changeData(item.FNLF_ID, item.FNIT_DATA)"
            v-model="item.FNIT_DATA">
        </td>

        <td class="td-m-85 text-right">
          <select class="form-control form-control-sm p-0 m-0" 
            :class="item.FNIS_CSS"
            @change="changeTipo(item.FNLF_ID, item.FNIS_ID)"
            v-model="item.FNIS_ID">
            <option v-for="(s,i) in $store.getters.F_Situacoes" :key="i" :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
          </select>
        </td>

        <td class="td-m-85 text-center">
          <select class="form-control form-control-sm p-0 m-0" 
            @change="changeStatus(item.FNLF_ID, item.FNIT_STATUS)"
            v-model="item.FNIT_STATUS">
            <option value="1">Ativo</option>
            <option value="0">Inativo</option>
          </select>
        </td>

        <td>
          <TdiconEdit 
            target='FinancaModal-ListaFixa'
            :setItem='setItem'
            :ID='item.FNLF_ID'
          />
        </td>
      </tr>
    </template>
  </Table>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ['time','items','setItem'],

    components: {
      Table:      () => import('@/components/Table'),
      TdiconEdit: () => import('@/components/Td_iconEdit'),
    },

    methods: {
      find(FNLF_ID){
        return this.items.filter( i => i.FNLF_ID == FNLF_ID)[0]
      },

      changeValor(FNLF_ID, FNIT_VALOR) {
        var item = this.find(FNLF_ID)
        item.FNIT_VALOR = FNIT_VALOR

        this.putItem(item)
      },

      changeData(FNLF_ID, FNIT_DATA) {
        var item = this.find(FNLF_ID)
        item.FNIT_DATA = FNIT_DATA

        this.putItem(item)
      },

      changeTipo(FNLF_ID, FNIS_ID) {
        var item = this.find(FNLF_ID)
        item.FNIS_ID = FNIS_ID

        this.putItem(item)
      },

      changeStatus(FNLF_ID, FNIT_STATUS) {
        var item = this.find(FNLF_ID)
        item.FNIT_STATUS = FNIT_STATUS

        this.putItem(item)
      },

      putItem(item) {
        let data = {}
          data.FINC_ID     = item.FINC_ID
          data.FITP_ID     = item.FITP_ID
          data.FIGP_ID     = item.FIGP_ID
          data.FICT_ID     = item.FICT_ID
          data.FNIS_ID     = item.FNIS_ID
          data.FNIT_VALOR  = item.FNIT_VALOR
          data.FNIT_DATA   = item.FNIT_DATA
          data.FNIT_OBS    = item.FNIT_OBS
          data.FNIT_STATUS = item.FNIT_STATUS
          data.USUA_ID     = this.$store.getters.USUA_ID


        service.finc.listaFixa.put({
          USUA_ID: item.USUA_ID,
          FNLF_ID: item.FNLF_ID,
          data:    data
        })
        .then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item cadastrado!' }) // message

          } else if (STATUS == "error") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

          } else if (STATUS == "token") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
            this.$store.commit("SET_LOGIN", false);
          }
        })
      },
      
    },
  }
</script>