// Data
const menuItems = [
    {
        id: 1,
        name: "Medium Spicy Spaghetti Italiano",
        orders: "89x",
        price: "$5.6",
        image: "/api/placeholder/60/60"
    },
    {
        id: 2,
        name: "Italiano Pizza With Garlic",
        orders: "89x",
        price: "$5.6",
        image: "/api/placeholder/60/60"
    },
    {
        id: 3,
        name: "Tuna Soup spinach with himalaya salt",
        orders: "89x",
        price: "$5.6",
        image: "/api/placeholder/60/60"
    }
];

const trendingKeywords = [
    { tag: "#pizza", times: 452, percentage: 80 },
    { tag: "#breakfast", times: 97, percentage: 40 },
    { tag: "#coffee", times: 61, percentage: 30 }
];

const customers = [
    { name: "Benny Chagur", type: "MEMBER", avatar: "/api/placeholder/40/40" },
    { name: "Chynita Bella", type: "MEMBER", avatar: "/api/placeholder/40/40" },
    { name: "David Heree", type: "Regular Customer", avatar: "/api/placeholder/40/40" },
    { name: "Evan D. Mas", type: "MEMBER", avatar: "/api/placeholder/40/40" },
    { name: "Supratman", type: "Regular Customer", avatar: "/api/placeholder/40/40" }
];

// DOM Elements
const menuContainer = document.querySelector('.menu-items-container');
const keywordsContainer = document.querySelector('.keywords-container');
const customersContainer = document.querySelector('.customers-container');

// Render Functions
function renderMenuItems() {
    menuContainer.innerHTML = menuItems.map(item => `
        <div class="menu-item">
            <img src="${item.image}" alt="${item.name}">
            <div class="ms-3">
                <h6>${item.name}</h6>
                <small class="text-muted">Order: ${item.orders}</small>
            </div>
            <div class="ms-auto">
                <h6>${item.price}</h6>
            </div>
        </div>
    `).join('');
}

function renderKeywords() {
    keywordsContainer.innerHTML = trendingKeywords.map(keyword => `
        <div class="trending-keyword">
            <div class="d-flex justify-content-between">
                <span>${keyword.tag}</span>
                <span>${keyword.times} times</span>
            </div>
            <div class="keyword-bar" style="width: ${keyword.percentage}%"></div>
        </div>
    `).join('');
}

function renderCustomers() {
    customersContainer.innerHTML = customers.map(customer => `
        <div class="customer-item">
            <img src="${customer.avatar}" alt="${customer.name}" class="customer-avatar">
            <div class="customer-info">
                <h6 class="customer-name">${customer.name}</h6>
                ${customer.type === "MEMBER" 
                    ? `<span class="badge-member">MEMBER</span>`
                    : `<span class="customer-type">${customer.type}</span>`
                }
            </div>
        </div>
    `).join('');
}

// Revenue Chart Configuration
const revenueOptions = {
    series: [{
        name: 'Revenue',
        data: [24, 30, 27, 32, 25, 29, 28, 33, 27, 31, 26, 28]
    }],
    chart: {
        type: 'area',
        height: 350,
        toolbar: {
            show: false
        },
        background: 'transparent'
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'smooth',
        width: 3,
        colors: ['#ff6b6b']
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
            stops: [0, 90, 100],
            colorStops: [
                {
                    offset: 0,
                    color: '#ff6b6b',
                    opacity: 0.4
                },
                {
                    offset: 100,
                    color: '#ff6b6b',
                    opacity: 0.1
                }
            ]
        }
    },
    xaxis: {
        categories: ['8:00', '9:00', '10:00', '11:00', '12:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00'],
        labels: {
            style: {
                colors: '#8a8d98'
            }
        },
        axisBorder: {
            show: false
        },
        axisTicks: {
            show: false
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
        borderColor: '#2d2f3b',
        strokeDashArray: 5,
        xaxis: {
            lines: {
                show: true
            }
        },
        yaxis: {
            lines: {
                show: true
            }
        }
    },
    tooltip: {
        theme: 'dark',
        y: {
            formatter: function (val) {
                return "$" + val
            }
        }
    }
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Render all components
    renderMenuItems();
    renderKeywords();
    renderCustomers();
    
    // Initialize Revenue Chart
    const revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
    revenueChart.render();
});

// Sidebar Toggle Function
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}

// Handle Period Selector
const periodButtons = document.querySelectorAll('.period-selector button');
periodButtons.forEach(button => {
    button.addEventListener('click', () => {
        periodButtons.forEach(btn => btn.classList.remove('active'));
        button.classList.add('active');
    });
});

// Handle Window Resize
window.addEventListener('resize', function() {
    if (window.innerWidth > 991.98) {
        document.getElementById('sidebar').classList.remove('active');
    }
});