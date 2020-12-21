<script>
// https://codesandbox.io/s/93m1lpjrvr?file=/src/components/PieChart.vue:0-998

import { Pie } from "vue-chartjs";


export default {
  extends: Pie,

  props: ['arrDados'],
  /* 
   * o arrDados deve serguir o setuinte modelos de matriz:
   * 
   * arrDados = [ 1, 2, 3, 4 ]
   *
  */

  // data() {
  //   return {}
  // },

  mounted() {
    this.renderGrafico()
  },

  methods: {
    renderGrafico(){

      //  ---

      this.gradient1 = this.$refs.canvas.getContext("2d").createLinearGradient(0, 0, 0, 450);
      this.gradient2 = this.$refs.canvas.getContext("2d").createLinearGradient(0, 0, 0, 450);
      this.gradient3 = this.$refs.canvas.getContext("2d").createLinearGradient(0, 0, 0, 450);
      this.gradient4 = this.$refs.canvas.getContext("2d").createLinearGradient(0, 0, 0, 450);

      this.gradient1.addColorStop(0,   "rgba(40, 167, 69, 0.9)");
      this.gradient1.addColorStop(0.5, "rgba(40, 167, 69, 0.25)");
      this.gradient1.addColorStop(1,   "rgba(40, 167, 69, 0)");

      this.gradient2.addColorStop(0,   "rgba(220, 53, 69, 0.9)");
      this.gradient2.addColorStop(0.5, "rgba(220, 53, 69, 0.25)");
      this.gradient2.addColorStop(1,   "rgba(220, 53, 69, 0)");
      
      this.gradient3.addColorStop(0,   "rgba(52, 68, 64, 0.9)");
      this.gradient3.addColorStop(0.5, "rgba(52, 68, 64, 0.25)");
      this.gradient3.addColorStop(1,   "rgba(52, 68, 64, 0)");
      
      this.gradient4.addColorStop(0,   "rgba(0, 125, 250, 0.9)");
      this.gradient4.addColorStop(0.5, "rgba(0, 125, 250, 0.25)");
      this.gradient4.addColorStop(1,   "rgba(0, 125, 250, 0)");

      // --

      this.renderChart(
        {
          labels: this.arrDados.labels,
          datasets: [
            {
              backgroundColor: [ this.gradient1, this.gradient2, this.gradient3, this.gradient4],
              data: this.arrDados.valores
            }
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
    'arrDados.valores':{
      handler(val){
        this.renderGrafico()
       // do stuff
     },
     deep: true
    }
  },

};
</script>