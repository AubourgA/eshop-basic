import { Controller } from '@hotwired/stimulus'


export default class extends Controller {
 static targets = ['canvas']
  static values = {
    labels: Array,
    data: Array
  }

  connect() {
   

    const ctx = this.canvasTarget.getContext('2d')

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: this.labelsValue,
        datasets: [{
          label: 'Prix de vente (€)',
          data: this.dataValue,
          borderColor: '#10b981',
          tension:0.5,
          fill: false,
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
        },
        scales: {
          x: {
            ticks: {
                minRotation:90
            }
          },
          y:{
            beginAtZero: true
          }
        }
      }
    })
  }
}
