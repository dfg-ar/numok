<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! Here's what's happening with your affiliate program.
                </p>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <a href="/admin/conversions/export" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                    </svg>
                    Export Data
                </a>
                <a href="/admin/programs/create" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    New Program
                </a>
            </div>
        </div>

        <!-- Enhanced Stats Grid -->
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Monthly Revenue -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.467-.22-2.121-.659C8.737 10.46 8.737 9.04 9.879 8.182c1.171-.879 3.07-.879 4.242 0L15 9" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Monthly Revenue</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">$<?= number_format($stats['monthly_revenue'], 2) ?></p>
                    <?php if ($stats['revenue_change'] != 0): ?>
                        <p class="ml-2 flex items-baseline text-sm font-semibold <?= $stats['revenue_change'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?php if ($stats['revenue_change'] >= 0): ?>
                                <svg class="h-5 w-5 flex-shrink-0 self-center text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                </svg>
                            <?php else: ?>
                                <svg class="h-5 w-5 flex-shrink-0 self-center text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                </svg>
                            <?php endif; ?>
                            <?= abs($stats['revenue_change']) ?>%
                        </p>
                    <?php endif; ?>
                </dd>
                <dd class="ml-16 text-sm text-gray-500">vs last month</dd>
            </div>

            <!-- Monthly Conversions -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-green-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Monthly Conversions</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900"><?= number_format($stats['monthly_conversions']) ?></p>
                    <?php if ($stats['conversions_change'] != 0): ?>
                        <p class="ml-2 flex items-baseline text-sm font-semibold <?= $stats['conversions_change'] >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                            <?php if ($stats['conversions_change'] >= 0): ?>
                                <svg class="h-5 w-5 flex-shrink-0 self-center text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 17a.75.75 0 01-.75-.75V5.612L5.29 9.77a.75.75 0 01-1.08-1.04l5.25-5.5a.75.75 0 011.08 0l5.25 5.5a.75.75 0 11-1.08 1.04L10.75 5.612V16.25A.75.75 0 0110 17z" clip-rule="evenodd" />
                                </svg>
                            <?php else: ?>
                                <svg class="h-5 w-5 flex-shrink-0 self-center text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                </svg>
                            <?php endif; ?>
                            <?= abs($stats['conversions_change']) ?>%
                        </p>
                    <?php endif; ?>
                </dd>
                <dd class="ml-16 text-sm text-gray-500">vs last month</dd>
            </div>

            <!-- Total Partners -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-yellow-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Active Partners</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900"><?= number_format($stats['total_partners']) ?></p>
                </dd>
                <dd class="ml-16 text-sm text-gray-500">registered partners</dd>
            </div>

            <!-- Pending Payouts -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-red-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Pending Payouts</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">$<?= number_format($stats['pending_payouts'], 2) ?></p>
                </dd>
                <dd class="ml-16 text-sm text-gray-500">awaiting payment</dd>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column - Charts and Recent Conversions -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Revenue Trends Chart -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Revenue Trends</h3>
                        <p class="mt-2 text-sm text-gray-700">Monthly revenue over the last 6 months</p>
                        
                        <?php if (!empty($revenue_trends)): ?>
                        <div class="mt-6">
                            <div class="relative h-64">
                                <canvas id="revenueChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('revenueChart').getContext('2d');
                            const data = <?= json_encode($revenue_trends) ?>;
                            
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.map(item => {
                                        const date = new Date(item.month + '-01');
                                        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                                    }),
                                    datasets: [{
                                        label: 'Revenue',
                                        data: data.map(item => parseFloat(item.revenue)),
                                        borderColor: 'rgb(79, 70, 229)',
                                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                callback: function(value) {
                                                    return '$' + value.toLocaleString();
                                                }
                                            }
                                        }
                                    }
                                }
                            });
                        });
                        </script>
                        <?php else: ?>
                        <div class="mt-6 text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No revenue data</h3>
                            <p class="mt-1 text-sm text-gray-500">Start generating conversions to see trends.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Conversions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="sm:flex sm:items-center">
                            <div class="sm:flex-auto">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Conversions</h3>
                                <p class="mt-2 text-sm text-gray-700">Latest conversions from your affiliate partners</p>
                            </div>
                            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                                <a href="/admin/conversions" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-500">View all</a>
                            </div>
                        </div>
                        
                        <?php if (!empty($recent_conversions)): ?>
                        <div class="mt-6 flow-root">
                            <ul role="list" class="-my-5 divide-y divide-gray-200">
                                <?php foreach ($recent_conversions as $conversion): ?>
                                <li class="py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-600"><?= strtoupper(substr($conversion['partner_name'], 0, 1)) ?></span>
                                            </div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($conversion['partner_name']) ?>
                                            </p>
                                            <p class="truncate text-sm text-gray-500">
                                                <?= htmlspecialchars($conversion['program_name']) ?>
                                            </p>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <p class="text-sm font-medium text-gray-900">$<?= number_format($conversion['amount'], 2) ?></p>
                                            <p class="text-sm text-gray-500">$<?= number_format($conversion['commission_amount'], 2) ?> commission</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                                                <?php switch($conversion['status']):
                                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'payable': echo 'bg-green-100 text-green-800'; break;
                                                    case 'paid': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                                endswitch; ?>">
                                                <?= ucfirst(htmlspecialchars($conversion['status'])) ?>
                                            </span>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="text-center mt-6 py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No conversions yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating your first program.</p>
                            <div class="mt-6">
                                <a href="/admin/programs/create" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                    Create Program
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Quick Actions, Top Performers, Recent Activity -->
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Quick Actions</h3>
                        <div class="mt-6 grid grid-cols-1 gap-4">
                            <a href="/admin/partners/create" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.674-1.334c.343.4.561.872.673 1.372h.756a4.5 4.5 0 00-1.84-3.18 6.375 6.375 0 00-11.263 0c-.537.813-.846 1.75-.846 2.757l.001.055z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900">Add Partner</p>
                                    <p class="text-sm text-gray-500">Invite new affiliate partners</p>
                                </div>
                            </a>

                            <a href="/admin/programs/create" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900">Create Program</p>
                                    <p class="text-sm text-gray-500">Set up new affiliate program</p>
                                </div>
                            </a>

                            <a href="/admin/conversions" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900">Review Conversions</p>
                                    <p class="text-sm text-gray-500">Manage pending payouts</p>
                                </div>
                            </a>

                            <a href="/admin/settings" class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm flex items-center space-x-3 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                <div class="flex-shrink-0">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="absolute inset-0" aria-hidden="true"></span>
                                    <p class="text-sm font-medium text-gray-900">Settings</p>
                                    <p class="text-sm text-gray-500">Configure your platform</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Top Performing Partners -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Top Partners</h3>
                        <p class="mt-2 text-sm text-gray-700">Highest revenue generating partners</p>
                        
                        <?php if (!empty($top_partners)): ?>
                        <div class="mt-6">
                            <ul role="list" class="space-y-3">
                                <?php foreach (array_slice($top_partners, 0, 5) as $index => $partner): ?>
                                <li class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-indigo-100 text-indigo-800 text-xs font-medium">
                                                <?= $index + 1 ?>
                                            </span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($partner['company_name']) ?></p>
                                            <p class="text-sm text-gray-500"><?= number_format($partner['total_conversions']) ?> conversions</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">$<?= number_format($partner['total_revenue'], 2) ?></p>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="mt-6">
                            <a href="/admin/partners" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                                View all partners
                                <span aria-hidden="true"> &rarr;</span>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="text-center mt-6 py-8">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No partners yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Recent Activity</h3>
                        <p class="mt-2 text-sm text-gray-700">Latest updates from your platform</p>
                        
                        <?php if (!empty($recent_activities)): ?>
                        <div class="mt-6 flow-root">
                            <ul role="list" class="-mb-8">
                                <?php foreach ($recent_activities as $index => $activity): ?>
                                <li>
                                    <div class="relative pb-8">
                                        <?php if ($index < count($recent_activities) - 1): ?>
                                        <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <?php endif; ?>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <?php switch($activity['type']):
                                                    case 'partner_signup': ?>
                                                        <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M10 8a3 3 0 100-6 3 3 0 000 6zM3.465 14.493a1.23 1.23 0 00.41 1.412A9.957 9.957 0 0010 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 00-13.074.003z" />
                                                            </svg>
                                                        </span>
                                                        <?php break;
                                                    case 'program_created': ?>
                                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 3a.75.75 0 01.75.75v10.638l3.96-4.158a.75.75 0 111.08 1.04l-5.25 5.5a.75.75 0 01-1.08 0l-5.25-5.5a.75.75 0 111.08-1.04l3.96 4.158V3.75A.75.75 0 0110 3z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                        <?php break;
                                                    case 'big_conversion': ?>
                                                        <span class="h-8 w-8 rounded-full bg-yellow-500 flex items-center justify-center ring-8 ring-white">
                                                            <svg class="h-5 w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M10.75 10.818v2.614A3.13 3.13 0 0011.888 13c.482-.315.612-.648.612-.875 0-.227-.13-.56-.612-.875a3.13 3.13 0 00-1.138-.432zM8.33 8.62c.053.055.115.11.184.164.208.16.46.284.736.363V6.603a2.45 2.45 0 00-.35.13c-.14.065-.27.143-.386.233-.377.292-.514.627-.514.909 0 .184.058.39.202.592.037.051.08.102.128.152z" />
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-6a.75.75 0 01.75.75v.316a3.78 3.78 0 011.653.713c.426.33.744.74.925 1.2a.75.75 0 01-1.395.55 1.35 1.35 0 00-.447-.563 2.187 2.187 0 00-.736-.363V9.3c.698.093 1.383.32 1.959.696.787.514 1.29 1.27 1.29 2.13 0 .86-.504 1.616-1.29 2.13-.576.377-1.261.603-1.959.696v.299a.75.75 0 11-1.5 0v-.3c-.697-.092-1.382-.318-1.958-.695C5.978 13.786 5.474 13.03 5.474 12.17c0-.86.504-1.616 1.29-2.13.576-.377 1.261-.603 1.958-.696V6.75A.75.75 0 0110 4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                        <?php break;
                                                endswitch; ?>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        <?php switch($activity['type']):
                                                            case 'partner_signup':
                                                                echo 'New partner <span class="font-medium text-gray-900">' . htmlspecialchars($activity['company_name']) . '</span> signed up';
                                                                break;
                                                            case 'program_created':
                                                                echo 'New program <span class="font-medium text-gray-900">' . htmlspecialchars($activity['name']) . '</span> created';
                                                                break;
                                                            case 'big_conversion':
                                                                echo '<span class="font-medium text-gray-900">' . htmlspecialchars($activity['company_name']) . '</span> generated $' . number_format($activity['amount'], 2) . ' conversion';
                                                                break;
                                                        endswitch; ?>
                                                    </p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <?= date('M j', strtotime($activity['created_at'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="text-center mt-6 py-8">
                            <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No recent activity</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>