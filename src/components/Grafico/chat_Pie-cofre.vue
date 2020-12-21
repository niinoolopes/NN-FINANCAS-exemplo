<script>
// https://codesandbox.io/s/93m1lpjrvr?file=/src/components/PieChart.vue:0-998

import { Pie } from "vue-chartjs";

export default {
  extends: Pie,

  props: ['arrDados'],
  /* 
   * o arrDados deve serguir o setuinte modelos de matriz:
   * 
   * arrDados = {
   * 'labels':  [ 'texto1', 'texto2' ]
   * 'valores': [ 1, 2 ]
   * } 
   *
  */

  data() {
    return {
      label: [],
      valor: [],
    }
  },

  mounted() {
    this.renderGrafico()
  },

  methods: {
    renderGrafico(){

      this.label = this.arrDados.labels;
      this.valor = this.arrDados.valores;

      // --

      this.renderChart(
        {
          labels: this.label,
          datasets: [
            {
              data: this.valor
            },
          ]
        },
        { 
          responsive: true, 
          maintainAspectRatio: false 
        }
      );

      // --
      
    },
  },

  watch: {
    'arrDados.valores'(newValue, oldValue) {
      this.renderGrafico()
    }
  },
};
</script>