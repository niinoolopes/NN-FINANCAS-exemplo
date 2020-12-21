<template>
  <Table colspan='11' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th class="responsivo text-center" scope="col">#</th>
      <th class="th-m-100 text-left"     scope="col">Carteira</th>
      <th class="th-m-120 text-left"     scope="col">Corretora</th>
      <th class="th-m-100 text-left"     scope="col">Site</th>
      <th class="th-m-120 text-left"     scope="col">Descrição</th>
      <th class="th-m-85  text-center"   scope="col">Data</th>
      <th class="th-m-85  text-center"   scope="col">Status</th>
      <th></th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item,i) in items" :key="i">
        <td class="responsivo text-center">{{item.INOD_ID}}</td>
        <td class="td-m-100 text-left" >{{item.INCT_DESCRICAO}} <small>({{item.INCT_ID}})</small></td>
        <td class="td-m-120 text-left" >{{item.INCR_DESCRICAO}} <small>({{item.INCR_ID}})</small></td>
        <td class="td-m-100 text-left" ><a :href="item.INCR_SITE" class="d-block text-lowercase m-0" target="_blank">{{item.INCR_SITE}}</a></td>
        <td class="td-m-120 text-left" >{{item.INOD_DESCRICAO}}</td>
        <td class="td-m-85 text-center">{{item.INOD_DATA | convertDate}}</td>
        <td class="td-m-85 text-center">{{item.INOD_STATUS ? 'Ativa' : 'Inativa'}}</td>
        <td>
          <TdiconShare
            to='InvestimentoOrdemAdm'
            :params='{ INOD_ID: item.INOD_ID }'
          />
        </td>
      </tr>
    </template>
  </Table>
</template>

<script>
  export default {
    props: ['items', 'time'],
    
    components: {
      TdiconShare: () => import('@/components/Td_iconShare'),
      Table:       () => import('@/components/Table'),
    },
  }
</script>