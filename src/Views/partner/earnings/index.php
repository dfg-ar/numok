<?php
// File: src/Views/partner/earnings/index.php
?>
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="md:flex md:items-center md:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Earnings Report
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Track your commission earnings, conversions, and performance across all programs.
                </p>
            </div>
            <div class="mt-4 flex md:ml-4 md:mt-0">
                <a href="/dashboard" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 mb-4">Filters</h3>
                <form method="GET" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" <?= $filters['status'] === 'all' ? 'selected' : '' ?>>All Statuses</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="payable" <?= $filters['status'] === 'payable' ? 'selected' : '' ?>>Payable</option>
                            <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                            <option value="rejected" <?= $filters['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <!-- Program Filter -->
                    <div>
                        <label for="program" class="block text-sm font-medium text-gray-700">Program</label>
                        <select id="program" name="program" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all" <?= $filters['program'] === 'all' ? 'selected' : '' ?>>All Programs</option>
                            <?php foreach ($programs as $program): ?>
                            <option value="<?= $program['id'] ?>" <?= $filters['program'] == $program['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($program['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Period Filter -->
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700">Period</label>
                        <select id="period" name="period" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="7" <?= $filters['period'] === '7' ? 'selected' : '' ?>>Last 7 days</option>
                            <option value="30" <?= $filters['period'] === '30' ? 'selected' : '' ?>>Last 30 days</option>
                            <option value="90" <?= $filters['period'] === '90' ? 'selected' : '' ?>>Last 90 days</option>
                            <option value="365" <?= $filters['period'] === '365' ? 'selected' : '' ?>>Last year</option>
                            <option value="all" <?= $filters['period'] === 'all' ? 'selected' : '' ?>>All time</option>
                        </select>
                    </div>

                    <!-- Apply Button -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full inline-flex justify-center items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                            </svg>
                            Apply Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Commission -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-green-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.467-.22-2.121-.659C8.737 10.46 8.737 9.04 9.879 8.182c1.171-.879 3.07-.879 4.242 0L15 9" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Commission</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">$<?= number_format($summary['total_commission'], 2) ?></p>
                </dd>
                <dd class="ml-16 text-sm text-gray-500">from <?= number_format($summary['total_conversions']) ?> conversions</dd>
            </div>

            <!-- Average Commission -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-blue-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Average Commission</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">$<?= number_format($summary['avg_commission'], 2) ?></p>
                </dd>
                <dd class="ml-16 text-sm text-gray-500">per conversion</dd>
            </div>

            <!-- Pending Commission -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-yellow-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Pending Commission</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">$<?= number_format($summary['pending_amount'], 2) ?></p>
                </dd>
                <dd class="ml-16 text-sm text-gray-500"><?= number_format($summary['pending_count']) ?> conversions</dd>
            </div>

            <!-- Paid Commission -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6">
                <dt>
                    <div class="absolute rounded-md bg-indigo-500 p-3">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="ml-16 truncate text-sm font-medium text-gray-500">Paid Commission</p>
                </dt>
                <dd class="ml-16 flex items-baseline">
                    <p class="text-2xl font-semibold text-gray-900">$<?= number_format($summary['paid_amount'], 2) ?></p>
                </dd>
                <dd class="ml-16 text-sm text-gray-500"><?= number_format($summary['paid_count']) ?> conversions</dd>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
            <!-- Left Column - Chart -->
            <div class="lg:col-span-2">
                <!-- Monthly Earnings Chart -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Monthly Earnings Trend</h3>
                        <p class="mt-2 text-sm text-gray-700">Your commission earnings over time</p>
                        
                        <?php if (!empty($monthly_earnings)): ?>
                        <div class="mt-6">
                            <div class="relative h-64">
                                <canvas id="earningsChart" class="w-full h-full"></canvas>
                            </div>
                        </div>
                        
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const ctx = document.getElementById('earningsChart').getContext('2d');
                            const data = <?= json_encode($monthly_earnings) ?>;
                            
                            new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: data.map(item => {
                                        const date = new Date(item.month + '-01');
                                        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
                                    }),
                                    datasets: [{
                                        label: 'Earnings',
                                        data: data.map(item => parseFloat(item.earnings)),
                                        borderColor: 'rgb(34, 197, 94)',
                                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
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
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No earnings data</h3>
                            <p class="mt-1 text-sm text-gray-500">Start promoting programs to see your earnings trends.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Column - Status Breakdown -->
            <div>
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Commission Status</h3>
                        <p class="mt-2 text-sm text-gray-700">Breakdown by payment status</p>
                        
                        <div class="mt-6 space-y-4">
                            <!-- Pending -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-3 w-3 rounded-full bg-yellow-400"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-900">Pending</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">$<?= number_format($summary['pending_amount'], 2) ?></div>
                                    <div class="text-xs text-gray-500"><?= number_format($summary['pending_count']) ?> conversions</div>
                                </div>
                            </div>

                            <!-- Payable -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-3 w-3 rounded-full bg-green-400"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-900">Payable</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">$<?= number_format($summary['payable_amount'], 2) ?></div>
                                    <div class="text-xs text-gray-500"><?= number_format($summary['payable_count']) ?> conversions</div>
                                </div>
                            </div>

                            <!-- Paid -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-3 w-3 rounded-full bg-blue-400"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-900">Paid</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">$<?= number_format($summary['paid_amount'], 2) ?></div>
                                    <div class="text-xs text-gray-500"><?= number_format($summary['paid_count']) ?> conversions</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Conversions Table -->
        <div class="mt-8 bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-center">
                    <div class="sm:flex-auto">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Detailed Conversions</h3>
                        <p class="mt-2 text-sm text-gray-700">
                            Showing <?= number_format(count($conversions)) ?> of <?= number_format($pagination['total_count']) ?> conversions
                        </p>
                    </div>
                </div>
                
                <?php if (!empty($conversions)): ?>
                <div class="mt-6 flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Date</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Program</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Sale Amount</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Commission</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tracking Code</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($conversions as $conversion): ?>
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 sm:pl-0">
                                            <?= date('M j, Y', strtotime($conversion['created_at'])) ?>
                                            <div class="text-xs text-gray-500"><?= date('g:i A', strtotime($conversion['created_at'])) ?></div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            <?= htmlspecialchars($conversion['program_name']) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                                            $<?= number_format($conversion['amount'], 2) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm font-medium text-green-600">
                                            $<?= number_format($conversion['commission_amount'], 2) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                                                <?php switch($conversion['status']):
                                                    case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'payable': echo 'bg-green-100 text-green-800'; break;
                                                    case 'paid': echo 'bg-blue-100 text-blue-800'; break;
                                                    case 'rejected': echo 'bg-red-100 text-red-800'; break;
                                                endswitch; ?>">
                                                <?= ucfirst(htmlspecialchars($conversion['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-mono">
                                            <?= htmlspecialchars($conversion['tracking_code']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($pagination['total_pages'] > 1): ?>
                <div class="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])) ?>" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Previous</a>
                        <?php endif; ?>
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])) ?>" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Next</a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium"><?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?></span>
                                to <span class="font-medium"><?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_count']) ?></span>
                                of <span class="font-medium"><?= number_format($pagination['total_count']) ?></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <?php if ($pagination['current_page'] > 1): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] - 1])) ?>" class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                                   class="relative inline-flex items-center px-4 py-2 text-sm font-semibold <?= $i === $pagination['current_page'] ? 'z-10 bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0' ?>">
                                    <?= $i ?>
                                </a>
                                <?php endfor; ?>
                                
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $pagination['current_page'] + 1])) ?>" class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                    </svg>
                                </a>
                                <?php endif; ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center mt-6 py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No conversions found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your filters or start promoting programs to earn commissions.</p>
                    <div class="mt-6">
                        <a href="/programs" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Browse Programs
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script> 