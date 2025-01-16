<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    </div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
        <!-- Stats -->
        <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Total Partners -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Total Partners</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900"><?= $stats['total_partners'] ?? 0 ?></dd>
            </div>
            <!-- Total Programs -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">Active Programs</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900"><?= $stats['active_programs'] ?? 0 ?></dd>
            </div>
            <!-- Monthly Revenue -->
            <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                <dt class="truncate text-sm font-medium text-gray-500">This Month's Revenue</dt>
                <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">$<?= number_format($stats['monthly_revenue'] ?? 0, 2) ?></dd>
            </div>
        </div>

        <!-- Recent Conversions -->
        <div class="mt-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h2 class="text-base font-semibold leading-6 text-gray-900">Recent Conversions</h2>
                    <p class="mt-2 text-sm text-gray-700">A list of recent conversions and their status.</p>
                </div>
                <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                    <a href="/admin/conversions" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white hover:bg-indigo-500">View all</a>
                </div>
            </div>
            <?php if (!empty($recent_conversions)): ?>
            <div class="mt-8 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Partner</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Commission</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <?php foreach ($recent_conversions as $conversion): ?>
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                            <?= htmlspecialchars($conversion['partner_name']) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            $<?= number_format($conversion['amount'], 2) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            $<?= number_format($conversion['commission_amount'], 2) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium 
                                                <?php switch($conversion['status']):
                                                    case 'pending': echo 'bg-yellow-50 text-yellow-800'; break;
                                                    case 'payable': echo 'bg-green-50 text-green-800'; break;
                                                    case 'paid': echo 'bg-blue-50 text-blue-800'; break;
                                                    case 'rejected': echo 'bg-red-50 text-red-800'; break;
                                                endswitch; ?>">
                                                <?= ucfirst(htmlspecialchars($conversion['status'])) ?>
                                            </span>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <?= date('M j, Y', strtotime($conversion['created_at'])) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center mt-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No conversions</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by adding your first program.</p>
                <div class="mt-6">
                    <a href="/admin/programs/new" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Create Program
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>