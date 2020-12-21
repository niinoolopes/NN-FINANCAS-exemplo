<template>
  <Table colspan='8' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th class="responsivo text-center" scope="col">#</th>
      <th class="th-m-120 text-left"     scope="col">Tipo Invest.</th>
      <th class="th-m-100 text-left"     scope="col">Tipo Ativo</th>
      <th class="th-m-100 text-left"     scope="col">Ativo</th>
      <th class="th-m-100 text-left"     scope="col">Tipo</th>
      <th class="th-m-85 text-center"    scope="col">Quantidade</th>
      <th class="th-m-85 text-center"    scope="col">Data</th>
      <th class="th-m-85 text-center"    scope="col">Status</th>
      <th></th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item, i) in items" :key="i">
        <td class="responsivo text-center">{{item.INAS_ID}}</td>
        <td class="td-m-120 text-left"    >{{item.INTP_DESCRICAO}} <small>({{item.INTP_ID}})</small></td>
        <td class="td-m-100 text-left"    >{{item.INAT_DESCRICAO}} <small>({{item.INAT_ID}})</small></td>
        <td class="td-m-100 text-left" >
          <router-link :to="{name: 'InvestimentoAtivoAdm', params:{ INAV_ID: item.INAV_ID }}">
            {{item.INAV_CODIGO}} <small>({{item.INAV_ID}})</small>
          </router-link>
        </td>
        <td class="td-m-85  text-left"    >{{item.INAS_TIPO == 'S' ? 'Split' : 'Inplit'}}</td>
        <td class="td-m-85 text-center"   >{{item.INAS_QUANTIDADE}}</td>
        <td class="td-m-85 text-center"   >{{item.INAS_DATA | convertDate}}</td>
        <td class="td-m-85 text-center"   >{{item.INAS_STATUS ? 'Ativo' : 'Inativo'}}</td>
        <td>
          <TdiconEdit 
            target='InvestimentoModal-Split'
            :setItem='setItem'
            :ID='item.INAS_ID'
          />
        </td>
      </tr>
    </template>
  </Table>
</template>

<script>
  export default {
    props: ['items', 'time', 'setItem'],

    components: {
      Table:      () => import('@/components/Table'),
      TdiconEdit: () => import('@/components/Td_iconEdit'),
    },
  }
</script>