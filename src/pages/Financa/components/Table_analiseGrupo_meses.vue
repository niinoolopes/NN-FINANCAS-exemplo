<template>
  <div class="border over-x">
    <table class="table table-sm">
      <thead>
        <tr>
          <th class="text-center"  scope="col">Mês</th>
          <th class="text-right pr-2 pr-md-5" scope="col">Valor</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item,i) in items" :key="i">
          <td class="text-center text-black-50">{{item.MES}}</td>
          <td class="text-right pr-2 pr-md-5">{{item.VALOR | vReal}}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  export default {
    props: ['items']
  }
</script>