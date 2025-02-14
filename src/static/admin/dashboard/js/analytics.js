// Progress Circle Charts
function createProgressCircle(elementId, percentage, color) {
    const options = {
        series: [percentage],
        chart: {
            height: 60,
            type: 'radialBar',
            sparkline: {
                enabled: true
            }
        },
        colors: [color],
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '50%',
                },
                track: {
                    background: '#2d2f3b'
                },
                dataLabels: {
                    show: false
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector('#' + elementId), options);
    chart.render();
}

// Revenue Chart
function createRevenueChart() {
    const options = {
        series: [{
            name: 'Revenue',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            background: 'transparent'
        },
        colors: ['#ff5733'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth'
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
            labels: {
                style: {
                    colors: '#8a8d98'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#8a8d98'
                }
            }
        },
        grid: {
            borderColor: '#2d2f3b'
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3
            }
        }
    };

    const chart = new ApexCharts(document.querySelector('#revenueChart'), options);
    chart.render();
}

// Customer Distribution Chart
function createCustomerDistributionChart() {
    const options = {
        series: [44, 55, 13, 43],
        chart: {
            type: 'donut',
            height: 350,
            background: 'transparent'
        },
        labels: ['New', 'Regular', 'VIP', 'Others'],
        colors: ['#ff5733', '#28c76f', '#6c5dd3', '#ff9f43'],
        plotOptions: {
            pie: {
                donut: {
                    size: '75%'
                }
            }
        },
        legend: {
            labels: {
                colors: '#8a8d98'
            }
        }
    };

    const chart = new ApexCharts(document.querySelector('#customerDistributionChart'), options);
    chart.render();
}

// Initialize all charts
document.addEventListener('DOMContentLoaded', function() {
    createProgressCircle('menusProgress', 75, '#ff5733');
    createProgressCircle('customersProgress', 68, '#28c76f');
    createProgressCircle('revenueProgress', 82, '#6c5dd3');
    createProgressCircle('employeeProgress', 62, '#ff9f43');
    createRevenueChart();
    createCustomerDistributionChart();
}); 