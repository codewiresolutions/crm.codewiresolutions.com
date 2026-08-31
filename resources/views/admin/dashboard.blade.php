@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="bg-gradient-to-r from-slate-900 to-blue-900 rounded-xl p-5">
    <div class="md:flex justify-between items-start">
        <div>
            <i class="ri-chat-3-line text-blue-600 px-2 py-2 rounded-lg bg-blue-950/70 text-xl"></i>
            <h1 class="text-2xl font-bold text-white mt-4">Welcome back, Admin!</h1>
            <p class="text-gray-300 mt-2">
              Manage WhatsApp communication and customer data from one place.
            </p>
        </div>
        <span class="w-fit flex items-center gap-2 bg-gray-700/40 text-white px-3 py-2 rounded-full text-xs font-medium mt-6 md:mt-10">
            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
            All systems operational
        </span>
    </div>
</div>

    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-4 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300">
            <i class="ri-user-3-line text-blue-800 bg-blue-100/50 px-1.5 py-1.5 rounded-lg text-xl"></i>
            <div class="text-sm text-gray-500 mt-3">Customers</div>
            <h3 class="my-1 text-2xl font-semibold">{{ $totalContacts }}</h3>
            <p class="text-sm text-gray-500">Total customers in the customer table.</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300">
            <i class="ri-send-plane-fill text-red-700 text-xl bg-red-100/50 px-1.5 py-1.5 rounded-lg"></i>
            <div class="text-sm text-gray-500 mt-3">Sent Messages</div>
            <h3 class="my-1 text-2xl font-semibold">{{ $sentMessagesCount }}</h3>
            <p class="text-sm text-gray-500">Customers with a message sent using message_sent_at.</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300">
            <i class="ri-group-line text-green-800 bg-green-100/50 rounded-lg px-1.5 py-1.5 text-xl"></i>
            <div class="text-sm text-gray-500 mt-3">Users</div>
            <h3 class="my-1 text-2xl font-semibold">{{ $totalUsers }}</h3>
            <p class="text-sm text-gray-500">Total registered user accounts.</p>
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm hover:shadow-md transition-all hover:-translate-y-1 duration-300">
            <i class="ri-chat-4-line text-xl text-red-900 bg-red-50 px-1.5 py-1.5 rounded-lg"></i>
            <div class="text-sm text-gray-500 mt-3">Messages</div>
            <h3 class="my-1 text-2xl font-semibold">{{ $totalMessages }}</h3>
            <p class="text-sm text-gray-500">Total WhatsApp message templates created.</p>
        </div>
    </div>


<div class="space-y-6 font-sans">
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white border border-white rounded-xl p-5 flex flex-col justify-between mt-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold">Revenue</h3>
        <div class="flex bg-[#171326] p-1 rounded-lg gap-1">
          <button type="button" class="bg-[#381f66] text-white text-xs px-2.5 py-1 rounded-md font-medium">6M</button>
          <button type="button" class="text-[#8b85a1] hover:text-white text-xs px-2.5 py-1 rounded-md font-medium transition-colors">1Y</button>
          <button type="button" class="text-[#8b85a1] hover:text-white text-xs px-2.5 py-1 rounded-md font-medium transition-colors">All</button>
        </div>
      </div>

      <div class="relative h-48 w-full">
        <canvas id="revenueChart"></canvas>
      </div>
    </div>
    <div class="bg-white border border-white rounded-xl p-5 relative mt-6">
      <h3 class="text-base font-semibold mb-2">Pipeline</h3>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="md:col-span-2 relative h-48 w-full">
          <canvas id="pipelineChart"></canvas>
        </div>

       
        <div class="flex flex-col justify-center items-start md:items-end pr-2">
          <span class="text-[#8b85a1] text-xs font-medium mb-1">Total Pipeline</span>
          <span class="text-2xl font-bold mb-1">$854k</span>
          <span class="text-emerald-400 text-xs font-medium flex items-center gap-1 mb-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
            11.4%
          </span>
          <span class="text-[#8b85a1] text-[11px]">vs last 6 months</span>
        </div>
      </div>
    </div>

  </div>


  
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white border border-white rounded-xl p-5 overflow-x-auto">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold">Recent Deals</h3>
        <a href="#" class="text-purple-800 hover:text-purple-500 text-xs font-medium">View all</a>
      </div>

      <div class="space-y-3">
      
        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-[#1f1535] border border-[#352358] flex items-center justify-center text-purple-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
            </div>
            <div>
              <p class="text-sm font-medium">Q1 Bulk Order</p>
              <p class="text-[#8b85a1] text-xs">Martin's Hardware</p>
            </div>
          </div>
          <div class="flex items-center gap-6">
            <span class="text-sm font-semibold">$125k</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium text-amber-300 bg-amber-50">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
              Negotiation
            </span>
            <span class="text-[#8b85a1] text-xs flex items-center gap-1">Feb 2 <svg class="w-3 h-3 text-[#8b85a1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </div>

        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-[#1f1535] border border-[#352358] flex items-center justify-center text-purple-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div>
              <p class="text-sm font-medium">Spring Inventory</p>
              <p class="text-[#8b85a1] text-xs">City Builders Supply</p>
            </div>
          </div>
          <div class="flex items-center gap-6">
            <span class="text-sm font-semibold">$89k</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium text-purple-300 bg-purple-50">
              <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
              Proposal
            </span>
            <span class="text-[#8b85a1] text-xs flex items-center gap-1">Jan 31 <svg class="w-3 h-3 text-[#8b85a1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </div>

        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-[#1f1535] border border-[#352358] flex items-center justify-center text-purple-400">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </div>
            <div>
              <p class="text-sm font-medium">Annual Contract</p>
              <p class="text-[#8b85a1] text-xs">Midwest Retail Group</p>
            </div>
          </div>
          <div class="flex items-center gap-6">
            <span class="text-sm font-semibold">$340k</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium text-emerald-300 bg-emerald-50">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
              Closed
            </span>
            <span class="text-[#8b85a1] text-xs flex items-center gap-1">Jan 29 <svg class="w-3 h-3 text-[#8b85a1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></span>
          </div>
        </div>
      </div>
    </div>
    <div class="bg-white border border-white rounded-xl p-5">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-base font-semibold">Top Contacts</h3>
        <a href="#" class="text-purple-800 hover:text-purple-500 text-xs font-medium">View all</a>
      </div>

      <div class="space-y-3">
      
        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#271d43] border border-[#3e2e6b] text-purple-200 text-xs font-semibold flex items-center justify-center">
              JS
            </div>
            <div>
              <p class="text-sm font-medium">Jane Smith</p>
              <p class="text-[#8b85a1] text-xs">City Builders Supply</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="p-2 text-purple-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </button>
            <button class="p-2 text-purple-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#271d43] border border-[#3e2e6b] text-purple-200 text-xs font-semibold flex items-center justify-center">
              MW
            </div>
            <div>
              <p class="text-sm font-medium">Mike Wilson</p>
              <p class="text-[#8b85a1] text-xs">Martin's Hardware</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="p-2 text-purple-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </button>
            <button class="p-2 text-purple-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </button>
          </div>
        </div>

        <div class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-50 transition-colors">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-[#271d43] border border-[#3e2e6b] text-purple-200 text-xs font-semibold flex items-center justify-center">
              DR
            </div>
            <div>
              <p class="text-sm font-medium">David Rodriguez</p>
              <p class="text-[#8b85a1] text-xs">Midwest Retail Group</p>
            </div>
          </div>
          <div class="flex items-center gap-2">
            <button class="p-2 text-purple-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            </button>
            <button class="p-2 text-purple-400 rounded-lg transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            </button>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  function initializeDashboardCharts() {
    if (typeof Chart === 'undefined') return;

    const revCanvas = document.getElementById('revenueChart');
    if (revCanvas) {
      const revCtx = revCanvas.getContext('2d');
      const gradient = revCtx.createLinearGradient(0, 0, 0, 180);
      gradient.addColorStop(0, 'rgba(168, 85, 247, 0.45)');
      gradient.addColorStop(1, 'rgba(168, 85, 247, 0.0)');

      new Chart(revCtx, {
        type: 'line',
        data: {
          labels: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb'],
          datasets: [{
            data: [35000, 48000, 70000, 105000, 138000, 168000],
            borderColor: '#a855f7',
            borderWidth: 2.5,
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
            pointRadius: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { display: false }, ticks: { color: '#8b85a1', font: { size: 11 } } },
            y: {
              min: 0,
              max: 180000,
              ticks: {
                color: '#8b85a1',
                font: { size: 11 },
                stepSize: 45000,
                callback: (val) => `$${val / 1000}k`
              },
              grid: { color: 'rgba(255, 255, 255, 0.03)', drawBorder: false }
            }
          }
        }
      });
    }
    const pipeCanvas = document.getElementById('pipelineChart');
    if (pipeCanvas) {
      const pipeCtx = pipeCanvas.getContext('2d');

      new Chart(pipeCtx, {
        type: 'bar',
        data: {
          labels: ['Discovery', 'Proposal', 'Negotiation', 'Closed'],
          datasets: [{
            data: [340000, 250000, 200000, 140000],
            backgroundColor: [
              '#a855f7', // Bright purple
              '#8b5cf6', // Medium purple
              '#7c3aed', // Darker purple
              '#4c1d95'  // Deep purple
            ],
            borderRadius: 6,
            barThickness: 16
          }]
        },
        options: {
          indexAxis: 'y', // Horizontal bars
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: {
              min: 0,
              max: 380000,
              ticks: {
                color: '#8b85a1',
                font: { size: 11 },
                stepSize: 95000,
                callback: (val) => `$${val / 1000}k`
              },
              grid: { color: 'rgba(255, 255, 255, 0.03)', drawBorder: false }
            },
            y: {
              grid: { display: false },
              ticks: { color: '#8b85a1', font: { size: 11 } }
            }
          }
        }
      });
    }
  }

  // Safe Execution trigger
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeDashboardCharts);
  } else {
    initializeDashboardCharts();
  }
</script>
@endsection