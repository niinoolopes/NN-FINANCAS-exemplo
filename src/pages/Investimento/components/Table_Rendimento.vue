<template>
  <Table colspan='9' :timeTable='time' :itemsTable='itemsTable' >
    <template v-slot:thead>
      <th class="responsivo text-center" scope="col">#</th>
      <th class="th-m-120 text-left"     scope="col">Corretora</th>
      <th class="th-m-120 text-left"     scope="col">Tipo Invest.</th>
      <th class="th-m-100 text-left"     scope="col">Tipo Ativo</th>
      <th class="th-m-100 text-left"     scope="col">Ativo</th>
      <th class="th-m-100 text-left"     scope="col">Tipo</th>
      <th class="th-m-85 text-center"    scope="col">Valor</th>
      <th class="th-m-85 text-center"    scope="col">Data Venc.</th>
      <th width='35'></th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item,i) in itemsTable" :key="i">
        <td class="responsivo text-center">{{item.INAR_ID}}</td>
        <td class="td-m-120 text-left" >{{item.INCR_DESCRICAO}} <small>({{item.INCR_ID}})</small></td>
        <td class="td-m-100 text-left" >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
        <td class="td-m-100 text-left" >{{item.INAT_DESCRICAO}} <small>({{item.INAT_ID}})</small></td>
        <td class="td-m-100 text-left" >
          <router-link :to="{name: 'InvestimentoAtivoAdm', params:{ INAV_ID: item.INAV_ID }}">
            {{item.INAV_CODIGO}} <small>({{item.INAV_ID}})</small>
          </router-link>
        </td>
        <td class="td-m-85  text-left" >{{item.INAR_TIPO}}</td>
        <td class="td-m-85 text-center">{{item.INAR_VALOR | vReal}}</td>
        <td>{{item.INAR_DATA | convertDate}}</td>
        <td>
          <TdiconEdit 
            target='InvestimentoModal-Rendimento'
            :setItem='setItem'
            :ID='item.INAR_ID'
          />
        </td>
      </tr>
    </template>
  </Table>
</template>

<script>
  export default {
    props: ['items', 'setItem', 'time'],

    data() {
      return {
        itemsTable: []
      }
    },

    components: {
      Table:      () => import('@/components/Table'),
      TdiconEdit:  () => import('@/components/Td_iconEdit'),
    },
    
    watch: {
      'items'(newValue, oldValue) {

        if(Array.isArray(newValue) ) {
          newValue.map( (value, key) => {
            if(value.INAR_TIPO == 'R') value.INAR_TIPO = 'Rendimento';
            if(value.INAR_TIPO == 'J') value.INAR_TIPO = 'Juros sobre capital próprio';
            if(value.INAR_TIPO == 'D') value.INAR_TIPO = 'Dividendo';
            return value
          });
          this.itemsTable = newValue
        } else {
          this.itemsTable = 'init'
        }
      }
    },
  }
</script>