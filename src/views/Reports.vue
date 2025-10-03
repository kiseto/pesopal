<template>
  <div class="bg-neutral-50 min-h-[calc(100vh-4rem)] py-10 px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header & Controls -->
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8 mb-8">
        <div>
          <h1 class="text-4xl font-bold text-gray-900">Reports & Analytics</h1>
          <div class="text-lg text-gray-500">Analyze your spending patterns and financial trends</div>
        </div>
        <div class="flex gap-4 items-center">
          <select v-model="selectedRange" class="cursor-pointer text-gray-900  border border-gray-300 rounded px-6 py-2">
            <option value="30">Last 30 days</option>
            <option value="90">Last 90 days</option>
            <option value="365">Last year</option>
          </select>
          <button @click="exportToExcel" class="cursor-pointer bg-green-600 text-white px-4 py-2 rounded font-semibold hover:bg-green-700 transition">Export Report</button>
        </div>
      </div>
      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Total Income</div>
          <div class="text-3xl font-bold text-green-600">₱{{ summary.income.toLocaleString() }}</div>
          <div class="text-base text-green-700 mt-2">+12% from last month</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Total Expenses</div>
          <div class="text-3xl font-bold text-red-600">₱{{ summary.expenses.toLocaleString() }}</div>
          <div class="text-base text-red-700 mt-2">+8% from last month</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Net Savings</div>
          <div class="text-3xl font-bold text-blue-600">₱{{ summary.savings.toLocaleString() }}</div>
          <div class="text-base text-blue-700 mt-2">+25% from last month</div>
        </div>
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8 flex flex-col">
          <div class="text-gray-500 text-md">Avg. Daily Spend</div>
          <div class="text-2xl font-bold text-gray-800">₱{{ summary.avgDaily.toLocaleString() }}</div>
          <div class="text-base text-gray-500 mt-2">Based on {{ selectedRange }} days</div>
        </div>
      </div>
      
      <!-- Charts Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 h-120 flex flex-col justify-center">
          <h1 class="text-gray-900 font-bold text-xl pt-4">Spending by Category ({{ selectedRange }} days)</h1>
          <ApexChart type="donut" width="100%" height="100%" :options="donutChartOptions" :series="donutChartSeries" />
        </div>

        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-6 h-120 flex flex-col justify-center">
          <h1 class="text-gray-900 font-bold text-xl pt-4">Monthly Spending Trends ({{ selectedRange }} days)</h1>
          <ApexChart type="line" width="100%" height="100%" :options="lineChartOptions" :series="lineChartSeries" />
        </div>
      </div>
      
      <!-- Breakdown & Export Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
        <!-- Category Breakdown -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="text-xl font-semibold text-gray-800 mb-6">Category Breakdown</div>
          <div v-for="cat in breakdown" :key="cat.title" class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
              <span :class="cat.color" class="h-5 w-5 rounded-full inline-block"></span>
              <span class="text-md">{{ cat.title }}</span>
            </div>
            <div class="text-right">
              <span class="text-gray-900 font-bold text-lg">₱{{ cat.amount.toLocaleString() }}</span>
              <span class="text-base text-gray-500 ml-2">{{ cat.percent }}%</span>
            </div>
          </div>
        </div>
        <!-- Export Reports -->
        <div class="bg-white rounded-2xl border-neutral-200 border-1 p-8">
          <div class="text-xl font-semibold text-gray-800 mb-6">Export Reports</div>
          <div class="flex flex-col gap-4 mb-6">
            <button @click="exportToPDF" class="cursor-pointer bg-red-600 text-white px-6 py-3 rounded font-semibold hover:bg-red-700 flex items-center gap-3"><span>Download PDF Report</span></button>
            <button @click="exportToCSV" class="cursor-pointer bg-blue-600 text-white px-6 py-3 rounded  font-semibold hover:bg-blue-700 flex items-center gap-3"><span>Export to CSV</span></button>
            <button @click="exportToExcel" class="cursor-pointer bg-green-600 text-white px-6 py-3 rounded  font-semibold hover:bg-green-700 flex items-center gap-3"><span>Export to Excel</span></button>
            
          </div>
          
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import ApexChart from 'vue3-apexcharts'
import ApiService from '../services/ApiService.js'

const selectedRange = ref(30)
const schedule = ref('Weekly')

// Real data from database
const dashboardData = ref(null)
const budgetCategories = ref([])
const monthlySpendingData = ref(null)
const categorySpendingData = ref(null)

// Computed summary based on real data
const summary = computed(() => {
  if (!dashboardData.value) {
    return {
      income: 0,
      expenses: 0,
      savings: 0,
      avgDaily: 0
    }
  }
  
  const data = dashboardData.value.summary
  const avgDaily = Math.round(data.total_expenses / selectedRange.value)
  
  return {
    income: data.total_income,
    expenses: data.total_expenses,
    savings: data.total_savings,
    avgDaily: avgDaily
  }
})

// Computed breakdown based on filtered category data
const breakdown = computed(() => {
  if (!categorySpendingData.value || categorySpendingData.value.length === 0) {
    return [
      { title: 'Food & Dining', amount: 11883, percent: 35.4, color: 'bg-green-400' },
      { title: 'Transportation', amount: 4805, percent: 14.3, color: 'bg-blue-400' },
      { title: 'Entertainment', amount: 4200, percent: 12.5, color: 'bg-purple-400' },
      { title: 'Shopping', amount: 6500, percent: 19.4, color: 'bg-yellow-400' },
      { title: 'Utilities', amount: 6200, percent: 18.4, color: 'bg-red-400' },
    ]
  }
  
  return categorySpendingData.value
})

//Apex Charts
// Donut Chart for Spending by Category
const donutChartOptions = computed(() => ({
  labels: breakdown.value.map(cat => cat.title),
  colors: ['#34d399', '#60a5fa', '#a78bfa', '#fbbf24', '#ef4444'],
  legend: {
    position: 'bottom'
  }
}))

const donutChartSeries = computed(() => breakdown.value.map(cat => cat.amount))

// Line Chart for Monthly Spending Trends - using real database data
const lineChartOptions = computed(() => ({
  xaxis: {
    categories: monthlySpendingData.value?.labels || ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  },
  chart: {
    width: '100%'
  },
  stroke: {
    curve: 'smooth',
    width: 3
  },
  colors: ['#dc2626'], // Red color for expenses
  tooltip: {
    y: {
      formatter: function (value) {
        return '₱' + value.toLocaleString()
      }
    }
  },
  yaxis: {
    labels: {
      formatter: function (value) {
        return '₱' + value.toLocaleString()
      }
    }
  }
}))

// Generate monthly spending data from database
const lineChartSeries = computed(() => {
  if (!monthlySpendingData.value) {
    return [{
      name: 'Monthly Spending',
      data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    }]
  }
  
  return [{
    name: 'Monthly Spending',
    data: monthlySpendingData.value.spending
  }]
})

// Load real data from APIs
async function loadReportsData(range = selectedRange.value) {
  try {
    console.log(`🔄 Loading reports data for ${range} days...`)
    
    // Load dashboard summary for financial overview
    const dashboardResult = await ApiService.getDashboardSummary()
    if (dashboardResult.success) {
      dashboardData.value = dashboardResult.data
      console.log('✅ Loaded dashboard data:', dashboardData.value)
    } else {
      console.error('❌ Failed to load dashboard data:', dashboardResult.message)
    }
    
    // Load budget categories for spending breakdown (keeping for fallback)
    const budgetResult = await ApiService.getBudgetCategories()
    if (budgetResult.success) {
      budgetCategories.value = budgetResult.data
      console.log('✅ Loaded budget categories:', budgetCategories.value)
    } else {
      console.error('❌ Failed to load budget categories:', budgetResult.message)
    }
    
    // Load category spending data filtered by range for donut chart
    const categoryResult = await ApiService.getCategorySpending(range)
    if (categoryResult.success) {
      categorySpendingData.value = categoryResult.data
      console.log('✅ Loaded category spending data:', categorySpendingData.value)
    } else {
      console.error('❌ Failed to load category spending data:', categoryResult.message)
    }
    
    // Load monthly spending data for chart filtered by range
    const monthlyResult = await ApiService.getMonthlySpending(range)
    if (monthlyResult.success) {
      monthlySpendingData.value = monthlyResult.data
      console.log('✅ Loaded monthly spending data:', monthlySpendingData.value)
      console.log('📊 Monthly breakdown:', monthlyResult.data.debug?.monthly_data)
    } else {
      console.error('❌ Failed to load monthly spending data:', monthlyResult.message)
    }
    
    console.log(`📊 Reports data loaded successfully for ${range} days`)
    
  } catch (error) {
    console.error('Error loading reports data:', error)
  }
}

// Watch for changes in selected range and reload data
watch(selectedRange, (newRange) => {
  console.log(`📊 Range changed to ${newRange} days, reloading data...`)
  loadReportsData(newRange)
})

// Export Functions
async function exportToPDF() {
  const reportData = {
    date: new Date().toLocaleDateString(),
    period: `Last ${selectedRange.value} days`,
    summary: summary.value,
    breakdown: breakdown.value
  }
  
  console.log('📄 Generating comprehensive PDF Report...')
  
  // Load comprehensive data for the report
  const comprehensiveResult = await ApiService.getComprehensiveReportData(selectedRange.value)
  let comprehensiveData = null
  
  if (comprehensiveResult.success) {
    comprehensiveData = comprehensiveResult.data
    console.log('✅ Loaded comprehensive data for report:', comprehensiveData)
  } else {
    console.error('❌ Failed to load comprehensive data, using basic report')
  }
  
  // Create a new window with the report content
  const printWindow = window.open('', '_blank', 'width=800,height=600')
  
  const reportHTML = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>PesoPal Comprehensive Financial Report</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                color: #333;
                line-height: 1.4;
            }
            .header {
                text-align: center;
                border-bottom: 2px solid #2563eb;
                padding-bottom: 20px;
                margin-bottom: 30px;
            }
            .header h1 {
                color: #2563eb;
                margin: 0;
                font-size: 28px;
            }
            .header p {
                color: #666;
                margin: 5px 0;
            }
            .summary-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
                margin-bottom: 30px;
            }
            .summary-card {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 20px;
                background-color: #f9fafb;
            }
            .summary-card h3 {
                margin: 0 0 10px 0;
                color: #6b7280;
                font-size: 14px;
                text-transform: uppercase;
            }
            .summary-card .amount {
                font-size: 24px;
                font-weight: bold;
                margin: 0;
            }
            .income { color: #059669; }
            .expense { color: #dc2626; }
            .savings { color: #2563eb; }
            .daily { color: #374151; }
            .section {
                margin: 30px 0;
                page-break-inside: avoid;
            }
            .section h2 {
                color: #1f2937;
                border-bottom: 1px solid #e5e7eb;
                padding-bottom: 10px;
                margin-bottom: 15px;
            }
            .data-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            .data-table th, .data-table td {
                border: 1px solid #e5e7eb;
                padding: 8px 12px;
                text-align: left;
            }
            .data-table th {
                background-color: #f3f4f6;
                font-weight: bold;
                color: #374151;
            }
            .data-table tr:nth-child(even) {
                background-color: #f9fafb;
            }
            .status-completed { color: #059669; font-weight: bold; }
            .status-progress { color: #d97706; font-weight: bold; }
            .status-overbudget { color: #dc2626; font-weight: bold; }
            .status-ontrack { color: #059669; font-weight: bold; }
            .footer {
                margin-top: 40px;
                text-align: center;
                color: #6b7280;
                font-size: 12px;
                border-top: 1px solid #e5e7eb;
                padding-top: 20px;
            }
            @media print {
                body { margin: 0; }
                .no-print { display: none; }
                .section { page-break-inside: avoid; }
            }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>PesoPal Comprehensive Financial Report</h1>
            <p>Generated on ${reportData.date}</p>
            <p>Period: ${reportData.period}</p>
        </div>
        
        <div class="summary-grid">
            <div class="summary-card">
                <h3>Total Income</h3>
                <p class="amount income">₱${reportData.summary.income.toLocaleString()}</p>
            </div>
            <div class="summary-card">
                <h3>Total Expenses</h3>
                <p class="amount expense">₱${reportData.summary.expenses.toLocaleString()}</p>
            </div>
            <div class="summary-card">
                <h3>Net Savings</h3>
                <p class="amount savings">₱${Math.round(reportData.summary.savings).toLocaleString()}</p>
            </div>
            <div class="summary-card">
                <h3>Avg. Daily Spend</h3>
                <p class="amount daily">₱${reportData.summary.avgDaily.toLocaleString()}</p>
            </div>
        </div>
        
        ${comprehensiveData ? `
        <div class="section">
            <h2>Recent Transactions</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    ${comprehensiveData.transactions.map(t => `
                        <tr>
                            <td>${t.description}</td>
                            <td>${t.category}</td>
                            <td>${t.type}</td>
                            <td class="${t.type === 'income' ? 'income' : 'expense'}">${t.formatted_amount}</td>
                            <td>${t.date}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Budget Categories</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Budget</th>
                        <th>Spent</th>
                        <th>Remaining</th>
                        <th>Usage %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${comprehensiveData.budget_categories.map(b => `
                        <tr>
                            <td>${b.title}</td>
                            <td>₱${b.budget_amount.toLocaleString()}</td>
                            <td>₱${b.spent_amount.toLocaleString()}</td>
                            <td>₱${b.remaining.toLocaleString()}</td>
                            <td>${b.percentage}%</td>
                            <td class="status-${b.status.toLowerCase().replace(' ', '')}">${b.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Savings Goals</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Goal</th>
                        <th>Target</th>
                        <th>Saved</th>
                        <th>Remaining</th>
                        <th>Progress %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    ${comprehensiveData.savings_goals.map(s => `
                        <tr>
                            <td>${s.title}</td>
                            <td>₱${s.target_amount.toLocaleString()}</td>
                            <td>₱${s.saved_amount.toLocaleString()}</td>
                            <td>₱${s.remaining.toLocaleString()}</td>
                            <td>${s.percentage}%</td>
                            <td class="status-${s.status.toLowerCase().replace(' ', '')}">${s.status}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        
        <div class="section">
            <h2>Recent Invoices</h2>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    ${comprehensiveData.invoices.map(i => `
                        <tr>
                            <td>${i.title}</td>
                            <td>${i.category}</td>
                            <td>₱${i.amount.toLocaleString()}</td>
                            <td>${i.status}</td>
                            <td>${i.due_date}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
        ` : ''}
        
        <div class="section">
            <h2>Spending by Category</h2>
            ${reportData.breakdown.map(cat => `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f3f4f6;">
                    <span style="font-weight: 500;">${cat.title}</span>
                    <span>
                        <span style="font-weight: bold; color: #1f2937;">₱${cat.amount.toLocaleString()}</span>
                        <span style="color: #6b7280; margin-left: 10px;">${cat.percent}%</span>
                    </span>
                </div>
            `).join('')}
        </div>
        
        <div class="footer">
            <p>Generated by PesoPal - Personal Finance Management System</p>
            <p>This report contains confidential financial information</p>
        </div>
        
        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" style="background: #2563eb; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px;">Print Report</button>
            <button onclick="window.close()" style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Close</button>
        </div>
    </body>
    </html>
  `
  
  printWindow.document.write(reportHTML)
  printWindow.document.close()
  
  // Auto-focus the print window
  printWindow.focus()
}

async function exportToCSV() {
  console.log('💾 Generating comprehensive CSV Export...')
  
  // Load comprehensive data for the export
  const comprehensiveResult = await ApiService.getComprehensiveReportData(selectedRange.value)
  let comprehensiveData = null
  
  if (comprehensiveResult.success) {
    comprehensiveData = comprehensiveResult.data
    console.log('✅ Loaded comprehensive data for CSV export:', comprehensiveData)
  }
  
  let csvContent = `PesoPal Financial Report - ${new Date().toLocaleDateString()}\n`
  csvContent += `Period: Last ${selectedRange.value} days\n\n`
  
  // Summary section
  csvContent += "FINANCIAL SUMMARY\n"
  csvContent += "Metric,Amount\n"
  csvContent += `Total Income,₱${summary.value.income.toLocaleString()}\n`
  csvContent += `Total Expenses,₱${summary.value.expenses.toLocaleString()}\n`
  csvContent += `Net Savings,₱${Math.round(summary.value.savings).toLocaleString()}\n`
  csvContent += `Avg Daily Spend,₱${summary.value.avgDaily.toLocaleString()}\n\n`
  
  // Category breakdown
  csvContent += "SPENDING BY CATEGORY\n"
  csvContent += "Category,Amount,Percentage\n"
  breakdown.value.forEach(cat => {
    csvContent += `"${cat.title}",₱${cat.amount.toLocaleString()},${cat.percent}%\n`
  })
  csvContent += "\n"
  
  if (comprehensiveData) {
    // Recent transactions
    csvContent += "RECENT TRANSACTIONS\n"
    csvContent += "Description,Category,Type,Amount,Date\n"
    comprehensiveData.transactions.forEach(t => {
      csvContent += `"${t.description}","${t.category}","${t.type}","${t.formatted_amount}","${t.date}"\n`
    })
    csvContent += "\n"
    
    // Budget categories
    csvContent += "BUDGET CATEGORIES\n"
    csvContent += "Category,Budget,Spent,Remaining,Usage %,Status\n"
    comprehensiveData.budget_categories.forEach(b => {
      csvContent += `"${b.title}",₱${b.budget_amount.toLocaleString()},₱${b.spent_amount.toLocaleString()},₱${b.remaining.toLocaleString()},${b.percentage}%,"${b.status}"\n`
    })
    csvContent += "\n"
    
    // Savings goals
    csvContent += "SAVINGS GOALS\n"
    csvContent += "Goal,Target,Saved,Remaining,Progress %,Status\n"
    comprehensiveData.savings_goals.forEach(s => {
      csvContent += `"${s.title}",₱${s.target_amount.toLocaleString()},₱${s.saved_amount.toLocaleString()},₱${s.remaining.toLocaleString()},${s.percentage}%,"${s.status}"\n`
    })
    csvContent += "\n"
    
    // Invoices (if any)
    if (comprehensiveData.invoices.length > 0) {
      csvContent += "RECENT INVOICES\n"
      csvContent += "Invoice,Category,Amount,Status,Due Date\n"
      comprehensiveData.invoices.forEach(i => {
        csvContent += `"${i.title}","${i.category}",₱${i.amount.toLocaleString()},"${i.status}","${i.due_date}"\n`
      })
    }
  }
  
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `pesopal-comprehensive-report-${new Date().toISOString().split('T')[0]}.csv`
  a.click()
  window.URL.revokeObjectURL(url)
  
  console.log('💾 Comprehensive CSV Export completed')
}

async function exportToExcel() {
  console.log('📊 Generating comprehensive Excel Export...')
  
  // Load comprehensive data for the export
  const comprehensiveResult = await ApiService.getComprehensiveReportData(selectedRange.value)
  let comprehensiveData = null
  
  if (comprehensiveResult.success) {
    comprehensiveData = comprehensiveResult.data
    console.log('✅ Loaded comprehensive data for Excel export:', comprehensiveData)
  }
  
  // Create Excel-compatible HTML format
  let excelContent = `
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th, td { border: 1px solid #000; padding: 8px; text-align: left; }
            th { background-color: #f0f0f0; font-weight: bold; }
            .header { font-size: 18px; font-weight: bold; margin-bottom: 10px; }
            .section { margin-top: 30px; }
            .amount { text-align: right; }
        </style>
    </head>
    <body>
        <div class="header">PesoPal Financial Report</div>
        <p>Generated: ${new Date().toLocaleDateString()}</p>
        <p>Period: Last ${selectedRange.value} days</p>
        
        <div class="section">
            <h3>Financial Summary</h3>
            <table>
                <tr><th>Metric</th><th>Amount</th></tr>
                <tr><td>Total Income</td><td class="amount">₱${summary.value.income.toLocaleString()}</td></tr>
                <tr><td>Total Expenses</td><td class="amount">₱${summary.value.expenses.toLocaleString()}</td></tr>
                <tr><td>Net Savings</td><td class="amount">₱${Math.round(summary.value.savings).toLocaleString()}</td></tr>
                <tr><td>Avg Daily Spend</td><td class="amount">₱${summary.value.avgDaily.toLocaleString()}</td></tr>
            </table>
        </div>
        
        <div class="section">
            <h3>Spending by Category</h3>
            <table>
                <tr><th>Category</th><th>Amount</th><th>Percentage</th></tr>
                ${breakdown.value.map(cat => `
                    <tr>
                        <td>${cat.title}</td>
                        <td class="amount">₱${cat.amount.toLocaleString()}</td>
                        <td>${cat.percent}%</td>
                    </tr>
                `).join('')}
            </table>
        </div>
        
        ${comprehensiveData ? `
        <div class="section">
            <h3>Recent Transactions</h3>
            <table>
                <tr><th>Description</th><th>Category</th><th>Type</th><th>Amount</th><th>Date</th></tr>
                ${comprehensiveData.transactions.map(t => `
                    <tr>
                        <td>${t.description}</td>
                        <td>${t.category}</td>
                        <td>${t.type}</td>
                        <td class="amount">${t.formatted_amount}</td>
                        <td>${t.date}</td>
                    </tr>
                `).join('')}
            </table>
        </div>
        
        <div class="section">
            <h3>Budget Categories</h3>
            <table>
                <tr><th>Category</th><th>Budget</th><th>Spent</th><th>Remaining</th><th>Usage %</th><th>Status</th></tr>
                ${comprehensiveData.budget_categories.map(b => `
                    <tr>
                        <td>${b.title}</td>
                        <td class="amount">₱${b.budget_amount.toLocaleString()}</td>
                        <td class="amount">₱${b.spent_amount.toLocaleString()}</td>
                        <td class="amount">₱${b.remaining.toLocaleString()}</td>
                        <td>${b.percentage}%</td>
                        <td>${b.status}</td>
                    </tr>
                `).join('')}
            </table>
        </div>
        
        <div class="section">
            <h3>Savings Goals</h3>
            <table>
                <tr><th>Goal</th><th>Target</th><th>Saved</th><th>Remaining</th><th>Progress %</th><th>Status</th></tr>
                ${comprehensiveData.savings_goals.map(s => `
                    <tr>
                        <td>${s.title}</td>
                        <td class="amount">₱${s.target_amount.toLocaleString()}</td>
                        <td class="amount">₱${s.saved_amount.toLocaleString()}</td>
                        <td class="amount">₱${s.remaining.toLocaleString()}</td>
                        <td>${s.percentage}%</td>
                        <td>${s.status}</td>
                    </tr>
                `).join('')}
            </table>
        </div>
        
        ${comprehensiveData.invoices.length > 0 ? `
        <div class="section">
            <h3>Recent Invoices</h3>
            <table>
                <tr><th>Invoice</th><th>Category</th><th>Amount</th><th>Status</th><th>Due Date</th></tr>
                ${comprehensiveData.invoices.map(i => `
                    <tr>
                        <td>${i.title}</td>
                        <td>${i.category}</td>
                        <td class="amount">₱${i.amount.toLocaleString()}</td>
                        <td>${i.status}</td>
                        <td>${i.due_date}</td>
                    </tr>
                `).join('')}
            </table>
        </div>
        ` : ''}
        ` : ''}
    </body>
    </html>
  `
  
  const blob = new Blob([excelContent], { 
    type: 'application/vnd.ms-excel;charset=utf-8;' 
  })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `pesopal-comprehensive-report-${new Date().toISOString().split('T')[0]}.xls`
  a.click()
  window.URL.revokeObjectURL(url)
  
  console.log('📊 Comprehensive Excel Export completed')
}

// Load data on component mount
onMounted(() => {
  loadReportsData()
})



</script>

<style scoped>
</style>
