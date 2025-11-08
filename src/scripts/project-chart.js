/**
 * BrokeHQ Project Chart
 *
 * Renders Chart.js line chart for project progress
 * Reads data from JSON script tag #projectChartData
 */

import Chart from 'chart.js/auto';

// Initialize chart when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('projectChart');
  const dataElement = document.getElementById('projectChartData');

  // Exit if chart elements don't exist
  if (!canvas || !dataElement) return;

  // Parse chart data from JSON script tag
  let chartData;
  try {
    chartData = JSON.parse(dataElement.textContent);
  } catch (error) {
    console.error('Failed to parse project chart data:', error);
    return;
  }

  // Create Chart.js instance
  new Chart(canvas, {
    type: 'line',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          mode: 'index',
          intersect: false,
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 8,
          titleFont: {
            size: 11,
            weight: '500'
          },
          bodyFont: {
            size: 13,
            weight: '600'
          },
          callbacks: {
            label: function(context) {
              return context.dataset.label + ': ' + context.parsed.y + ' tasks';
            }
          }
        }
      },
      scales: {
        x: {
          grid: {
            display: false
          },
          ticks: {
            font: {
              size: 11
            },
            color: 'rgb(115, 115, 115)'
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.05)'
          },
          ticks: {
            font: {
              size: 11
            },
            color: 'rgb(115, 115, 115)',
            precision: 0
          }
        }
      },
      interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false
      }
    }
  });
});
