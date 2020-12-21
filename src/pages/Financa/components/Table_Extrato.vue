<template>
  <Table colspan='10' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th scope="col" class="responsivo text-center">#</th>
      <th scope="col" class="th-m-100 text-left">Situação</th>
      <th scope="col" class="th-m-100 text-left">Grupo</th>
      <th scope="col" class="th-m-100 text-left">Categoria</th>
      <th scope="col" class="th-m-100 text-center">Valor</th>
      <th scope="col" class="th-m-100 text-center">Data</th>
      <th scope="col" class="th-m-100 text-center">Tipo</th>
      <th scope="col" class="th-m-100 text-center">Status</th>
      <th scope="col" class="th-m-100 text-left pl-2 pl-md-3">Observação</th>
      <th class="icon"></th>
    </template>
    <template v-slot:tbody> 
      <tr v-for="(item, i) in items" :key="i">
        <th class="responsivo text-center">{{ item.FNIT_ID }}</th>

        <td class="td-m-100 text-left" :class="item.FITP_CSS">{{item.FITP_DESCRICAO}} <small>({{item.FITP_ID}})</small></td>

        <td class="td-m-100 text-left" >
          <router-link 
            :to="{name: 'FinancaAnaliseGrupo', params: { idFINC: item.FINC_ID, idFIGP: item.FIGP_ID }}"
          >{{item.FIGP_DESCRICAO}} <small>({{item.FIGP_ID}})</small></router-link>
        </td>

        <td class="td-m-100 text-left">
          <router-link 
            :to="{name: 'FinancaAnaliseGrupo', params: { idFINC: item.FINC_ID, idFIGP: item.FIGP_ID, idFICT: item.FICT_ID}}"
          >{{item.FICT_DESCRICAO}} <small>({{item.FICT_ID}})</small></router-link>
        </td>

        <td class="td-m-85 text-center">
          <input class="form-control form-control-sm p-0 m-0 text-center" type="number" step="0.01" id="form-FNIT_VALOR" 
            @change="changeValor(item.FNIT_ID, item.FNIT_VALOR)"
            v-model="item.FNIT_VALOR">
        </td>

        <td class="td-m-85 text-center">
          <input class="form-control form-control-sm p-0 m-0 text-center" type="date" id="form-FNIT_DATA" 
            @change="changeData(item.FNIT_ID, item.FNIT_DATA)"
            v-model="item.FNIT_DATA">
        </td>

        <td class="td-m-85 text-right">
          <select class="form-control form-control-sm p-0 m-0" 
            :class="item.FNIS_CSS"
            @change="changeTipo(item.FNIT_ID, item.FNIS_ID)"
            v-model="item.FNIS_ID">
            <option v-for="(s,i) in $store.getters.F_Situacoes" :key="i" :value="s.FNIS_ID" >{{s.FNIS_DESCRICAO}}</option>
          </select>
        </td>

        <td class="td-m-85 text-center">
          <select class="form-control form-control-sm p-0 m-0" 
            @change="changeStatus(item.FNIT_ID, item.FNIT_STATUS)"
            v-model="item.FNIT_STATUS">
            <option value="1">Ativo</option>
            <option value="0">Inativo</option>
          </select>
        </td>

        <td class="td-m-85 text-left pl-2 pl-md-3 overflow-hidden">
          <span class="d=block">{{ item.FNIT_OBS.substring(0, 50) }} {{ item.FNIT_OBS.length >= 50 ? '...' : '' }} </span>
        </td>

        <td>
          <TdiconEdit 
            target='FinancaModal-Item'
            :setItem='setItem'
            :ID='item.FNIT_ID'
          />
        </td>
      </tr>
    </template>
  </Table>
</template>

<script>
  import service from '@/service.js'

  export default {
    props: ["time", "items", "setItem"],

    components: {
      TdiconEdit:  () => import('@/components/Td_iconEdit'),
      Table:       () => import('@/components/Table'),
    },

    methods: {
      find(FNIT_ID){
        return this.items.filter( i => i.FNIT_ID == FNIT_ID)[0]
      },

      changeValor(FNIT_ID, FNIT_VALOR) {
        var item = this.find(FNIT_ID)
        this.putItem(item)
      },

      changeData(FNIT_ID, FNIT_DATA) {
        var item = this.find(FNIT_ID)
        this.putItem(item)
      },

      changeTipo(FNIT_ID, FNIS_ID) {
        var item = this.find(FNIT_ID)
        this.putItem(item)
      },

      changeStatus(FNIT_ID, FNIT_STATUS) {
        var item = this.find(FNIT_ID)
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
          data.USUA_ID     = item.USUA_ID

        let option = {}
          option.USUA_ID = item.USUA_ID
          option.FNIT_ID = item.FNIT_ID
          option.data    = data

        service.finc.item.put(option).then( ({STATUS, data, msg}) => {
          if(STATUS == 'success'){
            this.$store.commit('SET_MESSAGE',{ active: true, type: 'ok', texto: 'Item atualizado!' }) // message
            this.setItem('getDados')

          } else if (STATUS == "error") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: msg });

          } else if (STATUS == "token") {
            this.$store.commit('SET_MESSAGE', { active: true, type: "erro", texto: service.arrMessage, });
            this.$store.commit("SET_LOGIN", false);
          }
        })
      }
    },
  }
</script>

<style scoped>
.th-obs ,
.td-obs {
  min-width: 175px;
  text-align: left;
}
.td-obs div.obs {
  overflow: hidden;
}
.td-obs div.obs p::after {
  content: ' ...';
}

td div.obs {
  /* margin-left: auto; */
  padding: 0 0 0 10px;
}
@media (min-width: 768px) {
  td div.obs {
    width: 35vw;
  }
}
@media (min-width: 996px) {
  td div.obs {
    width: 20vw;
  }
}
@media (min-width: 1200px) {
  td div.obs {
    width: 15vw;
  }
}
td div.obs p {
  width: max-content;
  margin: 0;
}

</style>
