<template>
  <div class="border over-x">
    <table class="table table-sm">
      <thead>
        <tr>
          <th class="th-m-85 text-center" scope="col">Ano</th>
          <th class="th-m-85 text-right"  scope="col">Total</th>
          <th class="th-m-85 text-right"  scope="col">Janeiro</th>
          <th class="th-m-85 text-right"  scope="col">Fevereiro</th>
          <th class="th-m-85 text-right"  scope="col">Março</th>
          <th class="th-m-85 text-right"  scope="col">Abril</th>
          <th class="th-m-85 text-right"  scope="col">Maio</th>
          <th class="th-m-85 text-right"  scope="col">Junho</th>
          <th class="th-m-85 text-right"  scope="col">Julho</th>
          <th class="th-m-85 text-right"  scope="col">Agosto</th>
          <th class="th-m-85 text-right"  scope="col">Setembro</th>
          <th class="th-m-85 text-right"  scope="col">Outubro</th>
          <th class="th-m-85 text-right"  scope="col">Novembro</th>
          <th class="th-m-85 text-right"  scope="col">Dezembro</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(arr,i) in items" :key="i">
          <td v-for=" (text, j) in arr" :key="j">
            <div v-if="j < 1" class="text-center">{{text}}</div>
            <div v-else class="text-right" :class="text == 0 ? 'text-black-50' : ''">{{text | vReal}}</div>
          </td>
        </tr>

      </tbody>
    </table>
  </div>
</template>

<script>
  export default {
    props: ['items', 'texte'],
  }
</script>