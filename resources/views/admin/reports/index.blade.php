<x-app-layout>
  <x-slot name="header">
    <div class="flex justify-between items-center">
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Reports & Analytics') }}
      </h2>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <!-- الفلتر الجديد -->
      @include('admin.reports._filters')

      <!-- Growth Indicators -->
      <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Sales Growth -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 {{ $salesReport['growth']['trend'] === 'up' ? 'bg-green-500' : 'bg-red-500' }} rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M{{ $salesReport['growth']['trend'] === 'up' ? '13 7l5 5m0 0l-5 5m5-5H6' : '11 17l-5-5m0 0l5-5m-5 5h12' }}" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Sales Growth</dt>
                  <dd class="flex items-baseline">
                    <div class="text-2xl font-semibold text-gray-900">
                      {{ number_format($salesReport['growth']['percentage'], 1) }}%
                    </div>
                    <div class="ml-2 flex items-baseline text-sm font-semibold {{ $salesReport['growth']['trend'] === 'up' ? 'text-green-600' : 'text-red-600' }}">
                      {{ $salesReport['growth']['trend'] === 'up' ? '↑' : '↓' }}
                    </div>
                  </dd>
                </dl>
              </div>
            </div>
            <div class="mt-2 text-xs text-gray-500">
              Compared to previous period
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Sales</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ number_format($salesReport['total_sales'], 2) }} ريال</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ $salesReport['orders_count'] }}</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Average Order Value</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ number_format($salesReport['average_order_value'], 2) }} ريال</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex items-center">
              <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Completion Rate</dt>
                  <dd class="text-lg font-semibold text-gray-900">{{ $appointmentsReport['completion_rate'] }}%</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Period Comparison -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Period Comparison</h3>
          <div class="grid grid-cols-2 gap-4">
            <div class="p-4 bg-gray-50 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Current Period</h4>
              <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($salesReport['growth']['current_amount'], 2) }} ريال
              </p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg">
              <h4 class="text-sm font-medium text-gray-500">Previous Period</h4>
              <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ number_format($salesReport['growth']['previous_amount'], 2) }} ريال
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Sales Chart -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Sales Trend</h3>
          <div class="h-96">
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Stock Distribution Chart -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Distribution</h3>
          <div class="h-80">
            <canvas id="stockDistributionChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Top Products Analysis -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Top Selling Products</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Units Sold</th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Trend</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @forelse($salesReport['top_products'] as $product)
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $product['name'] }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    {{ number_format($product['total_quantity']) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    {{ number_format($product['total_revenue'], 2) }} ريال
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right">
                    @if($product['trend'] != 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product['trend'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product['trend'] > 0 ? '↑' : '↓' }} {{ abs($product['trend']) }}%
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            -
                        </span>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                    No products found
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Top Products -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Top Products</h3>
            </div>
            <div class="flow-root">
              <ul role="list" class="-my-5 divide-y divide-gray-200">
                @foreach($topProducts as $product)
                <li class="py-4">
                  <div class="flex items-center space-x-4">
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-medium text-gray-900 truncate">
                        {{ $product->name }}
                      </p>
                    </div>
                  </div>
                </li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>

        <!-- Inventory Status -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
          <div class="p-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium text-gray-900">Inventory Status</h3>
            </div>
            <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
              <div class="px-4 py-5 bg-gray-50 rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Products</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $inventoryReport['total_products'] }}</dd>
              </div>
              <div class="px-4 py-5 bg-gray-50 rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $inventoryReport['low_stock_count'] }}</dd>
              </div>
              <div class="px-4 py-5 bg-gray-50 rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Out of Stock</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $inventoryReport['out_of_stock_count'] }}</dd>
              </div>
              <div class="px-4 py-5 bg-gray-50 rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Average Stock Level</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">{{ $inventoryReport['average_stock'] }}</dd>
              </div>
            </dl>
          </div>
        </div>
      </div>

      <!-- Peak Hours Analysis -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Peak Hours Analysis</h3>
          <div class="h-80">
            <canvas id="peakHoursChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Customer Analysis -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Top Customers</h3>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead>
                <tr>
                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Spent</th>
                  <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Average Order</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                @foreach($salesReport['top_customers'] as $customer)
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $customer->user->name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    {{ $customer->orders }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    ${{ number_format($customer->total / 100, 2) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                    ${{ number_format(($customer->total / $customer->orders) / 100, 2) }}
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Appointments Analysis -->
      <div class="mb-8 bg-white overflow-hidden shadow-sm rounded-lg">
        <div class="p-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Appointments Overview</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Appointments Status Chart -->
            <div class="h-80">
              <canvas id="appointmentsChart"></canvas>
            </div>
            <!-- Appointments Stats -->
            <div class="grid grid-cols-2 gap-4">
              <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Total Appointments</h4>
                <p class="mt-2 text-2xl font-semibold text-gray-900">
                  {{ $appointmentsReport['total'] }}
                </p>
              </div>
              <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500">Completion Rate</h4>
                <p class="mt-2 text-2xl font-semibold text-gray-900">
                  {{ $appointmentsReport['completion_rate'] }}%
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Debug data
    console.log('Sales Data:', @json($salesReport['daily_data']));
    console.log('Stock Data:', @json($inventoryReport['stock_distribution']));

    // Sales Trend Chart
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesData = @json($salesReport['daily_data']);

    new Chart(salesCtx, {
      type: 'line',
      data: {
        labels: Object.keys(salesData),
        datasets: [{
          label: 'المبيعات',
          data: Object.values(salesData).map(d => d.sales),
          borderColor: '#4CAF50',
          backgroundColor: 'rgba(76, 175, 80, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 3
        }, {
          label: 'الطلبات',
          data: Object.values(salesData).map(d => d.orders),
          borderColor: '#2196F3',
          backgroundColor: 'rgba(33, 150, 243, 0.1)',
          fill: true,
          tension: 0.3,
          borderWidth: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
          intersect: false,
          mode: 'index'
        },
        plugins: {
          legend: {
            position: 'top',
            labels: {
              font: {
                size: 14,
                weight: 'bold'
              },
              padding: 20
            }
          },
          tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#000',
            titleFont: {
              size: 16,
              weight: 'bold'
            },
            bodyColor: '#000',
            bodyFont: {
              size: 14
            },
            borderColor: '#ddd',
            borderWidth: 1,
            padding: 15,
            callbacks: {
              label: function(context) {
                if (context.dataset.label === 'المبيعات') {
                  return `المبيعات: ${Number(context.raw).toLocaleString('ar-SA', {
                    style: 'currency',
                    currency: 'SAR'
                  })}`;
                }
                return `الطلبات: ${context.raw}`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: '#f0f0f0'
            },
            ticks: {
              callback: function(value) {
                return Number(value).toLocaleString('ar-SA', {
                  style: 'currency',
                  currency: 'SAR'
                });
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 12
              }
            }
          }
        }
      }
    });

    // Stock Distribution Chart
    const stockCtx = document.getElementById('stockDistributionChart').getContext('2d');
    const stockData = @json($inventoryReport['stock_distribution']);

    // إضافة console.log للتأكد من البيانات
    console.log('Stock Distribution Data:', stockData);

    new Chart(stockCtx, {
      type: 'bar',
      data: {
        labels: ['متوفر', 'منخفض', 'نفذ'],
        datasets: [{
          label: 'عدد المنتجات',
          data: [
            stockData['متوفر'] || 0,
            stockData['منخفض'] || 0,
            stockData['نفذ'] || 0
          ],
          backgroundColor: [
            '#4CAF50',  // متوفر - أخضر
            '#FFC107',  // منخفض - أصفر
            '#F44336'   // نفذ - أحمر
          ],
          borderWidth: 0,
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(255, 255, 255, 0.9)',
            titleColor: '#000',
            bodyColor: '#000',
            callbacks: {
              label: function(context) {
                return `عدد المنتجات: ${context.raw} منتج`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              color: '#f0f0f0'
            },
            ticks: {
              stepSize: 1,
              font: {
                size: 12
              }
            }
          },
          x: {
            grid: {
              display: false
            },
            ticks: {
              font: {
                size: 12,
                weight: 'bold'
              }
            }
          }
        }
      }
    });

    // Peak Hours Chart
    const peakHoursCtx = document.getElementById('peakHoursChart').getContext('2d');
    const peakHoursData = @json($salesReport['peak_hours']);

    new Chart(peakHoursCtx, {
      type: 'bar',
      data: {
        labels: peakHoursData.map(d => `${d.hour}:00`),
        datasets: [{
          label: 'Orders',
          data: peakHoursData.map(d => d.count),
          backgroundColor: 'rgba(59, 130, 246, 0.8)',
          borderColor: 'rgb(37, 99, 235)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            callbacks: {
              title: (items) => `Hour: ${items[0].label}`,
              label: (item) => `Orders: ${item.raw}`
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              stepSize: 1
            }
          }
        }
      }
    });

    // Appointments Chart
    const appointmentsCtx = document.getElementById('appointmentsChart').getContext('2d');
    const appointmentsData = @json($appointmentsReport['by_status']);

    new Chart(appointmentsCtx, {
      type: 'pie',
      data: {
        labels: Object.keys(appointmentsData),
        datasets: [{
          data: Object.values(appointmentsData),
          backgroundColor: [
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)',
            'rgb(244, 63, 94)',
            'rgb(234, 179, 8)'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right'
          }
        }
      }
    });
  });
  </script>
  @endpush
</x-app-layout>
