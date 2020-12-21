<script>
// https://codesandbox.io/s/93m1lpjrvr?file=/src/components/PieChart.vue:0-998

import { Bar } from "vue-chartjs";

export default {
  extends: Bar,

  data() {
    return {
      label: [],
      valor: [],
    }
  },

  props: ['titulo', 'labels', 'valores','FITP'],

  mounted() {
    this.renderGrafico()
  },

  methods: {
    renderGrafico(){
      this.label = this.labels;
      this.valor = this.valores.map( v => +v)
      
      this.renderChart(
        {
          labels: this.labels,

          datasets: [
            {
              label: this.titulo,
              data:  this.valor,
              barPercentage: 0.5,
              borderWidth: 2,
              "backgroundColor": (this.FITP == 1) ? "rgba( 40, 160,  75, 0.50)" : 'rgba( 220, 50, 75, 0.50)',
              "borderColor":     (this.FITP == 1) ? "rgba( 40, 160,  75, 0.75)" : 'rgba( 220, 50, 75, 0.75)',
            }
          ],
        },
        { 
          responsive: true, 
          maintainAspectRatio: false 
        }
      );
    },
  },

  watch: {
    'titulo'(newValue, oldValue) {
      this.renderGrafico()
    },
    'labels'(newValue, oldValue) {
      this.renderGrafico()
    },
    'valores'(newValue, oldValue) {
      this.renderGrafico()
    },
  }
};
</script>