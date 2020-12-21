<template>
  <Table colspan='7' :timeTable='time' :itemsTable='items' >
    <template v-slot:thead>
      <th class="responsivo text-center" scope="col">#</th>
      <th class="th-m-100 text-left"     scope="col">Tipo</th>
      <th class="th-m-85  text-left"     scope="col">Proposito</th>
      <th class="th-m-100 text-left"     scope="col">Observação</th>
      <th class="th-m-85 text-center"    scope="col">Valor</th>
      <th class="th-m-85 text-center"    scope="col">Data</th>
      <th class="th-m-85 text-center"    scope="col">Status</th>
      <th></th>
    </template>
    <template v-slot:tbody>
      <tr v-for="(item, i) in items" :key="i">
        <th class="responsivo text-center" >{{ item.COIT_ID }}</th>
        <td class="th-m-100 text-left"     >{{ item.COTP_DESCRICAO }}</td>
        <td class="th-m-85  text-left"     >{{ item.COIT_PROPOSITO }}</td>
        <td class="td-m-100 text-left"     >{{ item.COIT_OBS }}</td>
        <td class="th-m-85 text-center"    >{{ item.COIT_VALOR | vReal }}</td>
        <td class="th-m-85 text-center"    >{{ item.COIT_DATA | convertDate }}</td>
        <td class="th-m-85 text-center"    >{{ item.COIT_STATUS ? 'Ativo' : 'Inativo' }}</td>
        <td>
          <TdiconEdit 
            target='CofreModal-item'
            :setItem='setItem'
            :ID='item.COIT_ID'
          />
        </td>
      </tr>
    </template>
  </Table>

</template>

<script>
  export default {
    props: ['time', 'items', 'setItem'],

    components: {
      Table:       () => import('@/components/Table'),
      TdiconEdit:  () => import('@/components/Td_iconEdit'),
    },
  }
</script>