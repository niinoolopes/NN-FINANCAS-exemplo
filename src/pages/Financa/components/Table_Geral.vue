<template>
  <Table colspan='6' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th class="text-left " scope="col" :style="{fontSize:'12px', width:'120px'}" >{{descricao}}</th>
      <th class="text-right" scope="col" :style="{fontSize:'10px'}" >Total</th>
      <th class="text-right" scope="col" :style="{fontSize:'10px'}" >%</th>
      <th class="text-right" scope="col" :style="{fontSize:'10px'}" >Pago</th>
      <th class="text-right" scope="col" :style="{fontSize:'10px'}" >Pendente</th>
      <th class="text-right" scope="col" :style="{fontSize:'10px'}" >Talvez</th>
    </template>
    <template v-slot:tbody> 
      <tr v-for="(item,i) in items" :key="i">
        <td class="text-left">{{ item.DESCRICAO.length >= 15 ? item.DESCRICAO.substring(0,13)+'...' : item.DESCRICAO }}</td>
        <td class="text-right"><small>{{item.TOTAL | vReal}}</small></td>
        <td class="text-right"><small>{{item.PERCENTUAL}}</small></td>
        <td class="text-right"><small>{{item.PAGO | vReal}}</small></td>
        <td class="text-right"><small>{{item.PENDENTE | vReal}}</small></td>
        <td class="text-right"><small>{{item.TALVEZ | vReal}}</small></td>
      </tr>
    </template>
  </Table>
</template>

<script>
  export default {
    props: ['time', 'items', 'descricao'],
    components: {
      Table: () => import('@/components/Table'),
    },
  }
</script>