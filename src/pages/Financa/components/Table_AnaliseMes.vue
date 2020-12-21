<template>
  <div class="border over-x opacity-fetch" :class="time ? '' : 'opacity-fetch-active'" >
    <table class="table table-sm">
      <thead>
        <tr>
          <th v-for="( text , i ) in th" :key="i">
            <div v-if="i < 1" 
              class="th-m-120 text-left">{{text}}</div>
            <div v-else 
              class="th-m-85 text-right">{{text}}</div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="( arrValores , i ) in items" :key="i">
          <td v-for="( text , j ) in arrValores" :key="j">
            <div v-if="j < 1"
              class="th-m-120 text-left">{{text}}</div>
            <div v-else 
              class="td-m-85 text-right" :class="text == 0 ? 'text-black-50' : ''">{{text | vReal}}</div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
  export default {
    props: ['time', 'th', 'items']
  }
</script>