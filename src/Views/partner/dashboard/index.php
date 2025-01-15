<?php
// File: src/Views/partner/dashboard/index.php
?>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>

        <!-- Stats Overview -->
        <dl class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Monthly Earnings -->
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Monthly Earnings</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    $<?= number_format($stats['monthly_commission'], 2) ?>
                </dd>
                <dd class="mt-1 text-sm text-gray-500">
                    From <?= number_format($stats['monthly_conversions']) ?> conversions
                </dd>
            </div>

            <!-- Total Earnings -->
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Total Earnings</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    $<?= number_format($stats['total_commission'], 2) ?>
                </dd>
                <dd class="mt-1 text-sm text-gray-500">
                    From <?= number_format($stats['total_conversions']) ?> conversions
                </dd>
            </div>

            <!-- Active Programs -->
            <div class="px-4 py-5 bg-white shadow rounded-lg overflow-hidden sm:p-6">
                <dt class="text-sm font-medium text-gray-500 truncate">Active Programs</dt>
                <dd class="mt-1 text-3xl font-semibold text-gray-900">
                    <?= number_format($stats['active_programs']) ?>
                </dd>
            </div>
        </dl>

        <!-- Recent Conversions -->
        <div class="mt-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Conversions</h2>
                    <p class="mt-2 text-sm text-gray-700">A list of your most recent conversions across all programs.</p>
                </div>
            </div>
            
            <?php if (empty($conversions)): ?>
            <div class="text-center mt-6">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No conversions yet</h3>
                <p class="mt-1 text-sm text-gray-500">Start promoting your programs to earn commissions.</p>
            </div>
            <?php else: ?>
            <div class="mt-8 flex flex-col">
                <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Date</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Program</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Amount</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Commission</th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    <?php foreach ($conversions as $conversion): ?>
                                    <tr>
                                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 sm:pl-6">
                                            <?= date('M j, Y', strtotime($conversion['created_at'])) ?>
                                            <div class="text-gray-500"><?= date('g:i A', strtotime($conversion['created_at'])) ?></div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($conversion['program_name']) ?></div>
                                            <div class="text-gray-500 font-mono text-xs"><?= htmlspecialchars($conversion['tracking_code']) ?></div>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            $<?= number_format($conversion['amount'], 2) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                            $<?= number_format($conversion['commission_amount'], 2) ?>
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 <?php
                                                switch($conversion['status']) {
                                                    case 'pending':
                                                        echo 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    case 'payable':
                                                        echo 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'paid':
                                                        echo 'bg-blue-100 text-blue-800';
                                                        break;
                                                    case 'rejected':
                                                        echo 'bg-red-100 text-red-800';
                                                        break;
                                                    default:
                                                        echo 'bg-gray-100 text-gray-800';
                                                }
                                            ?>">
                                                <?= ucfirst(htmlspecialchars($conversion['status'])) ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
