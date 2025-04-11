<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:items-center lg:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Edit Partner: <?= htmlspecialchars($partner['company_name']) ?>
                </h2>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <a href="/admin/partners" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                    Back
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="rounded-md bg-green-50 p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($_SESSION['success']) ?></p>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="rounded-md bg-red-50 p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($_SESSION['error']) ?></p>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Partner Information -->
            <div>
                <form action="/admin/partners/<?= $partner['id'] ?>/update" method="POST">
                    <div class="space-y-12">
                        <div class="bg-white shadow sm:rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Partner Information</h3>
                                
                                <div class="mt-6 grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                    <div class="sm:col-span-6">
                                        <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                                        <div class="mt-1">
                                            <input type="text" name="company_name" id="company_name" required
                                                value="<?= htmlspecialchars($partner['company_name']) ?>"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                        </div>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="contact_name" class="block text-sm font-medium text-gray-700">Contact Name</label>
                                        <div class="mt-1">
                                            <input type="text" name="contact_name" id="contact_name" required
                                                value="<?= htmlspecialchars($partner['contact_name']) ?>"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                        </div>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="payment_email" class="block text-sm font-medium text-gray-700">Payment Email</label>
                                        <div class="mt-1">
                                            <input type="email" name="payment_email" id="payment_email"
                                                value="<?= htmlspecialchars($partner['payment_email']) ?>"
                                                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500">Where commission payments will be sent.</p>
                                    </div>

                                    <div class="sm:col-span-6">
                                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                        <select id="status" name="status" 
                                                class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                            <option value="pending" <?= $partner['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="active" <?= $partner['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                            <option value="suspended" <?= $partner['status'] === 'suspended' ? 'selected' : '' ?>>Suspended</option>
                                        </select>
                                        <p class="mt-2 text-sm text-gray-500">Partner's current status in the platform.</p>
                                    </div>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="ml-3 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                        Update Partner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Programs -->
            <div>
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Program Assignments</h3>
                        
                        <?php if ($partner['status'] === 'active'): ?>
                        <form action="/admin/partners/<?= $partner['id'] ?>/assign-program" method="POST" class="mt-4">
                            <div class="flex gap-3">
                                <div class="flex-1">
                                    <select name="program_id" required
                                            class="mt-1 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                        <option value="">Select a program...</option>
                                        <?php foreach ($availablePrograms as $program): ?>
                                        <option value="<?= $program['id'] ?>"><?= htmlspecialchars($program['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                                    Assign Program
                                </button>
                            </div>
                        </form>
                        <?php endif; ?>

                        <!-- Program List -->
                        <div class="mt-6">
                            <?php if (empty($programs)): ?>
                            <div class="text-center rounded-lg border-2 border-dashed border-gray-300 p-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No programs</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by assigning a program to this partner.</p>
                            </div>
                            <?php else: ?>
                            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900">Program</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tracking Code</th>
                                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        <?php foreach ($programs as $program): ?>
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900">
                                                <div>
                                                    <div class="font-medium"><?= htmlspecialchars($program['program_name']) ?></div>
                                                    <div class="text-gray-500">
                                                        <?php if ($program['commission_type'] === 'percentage'): ?>
                                                            <?= number_format($program['commission_value'], 1) ?>%
                                                        <?php else: ?>
                                                            $<?= number_format($program['commission_value'], 2) ?>
                                                        <?php endif; ?>
                                                        commission
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-mono">
                                                <?= htmlspecialchars($program['tracking_code']) ?>
                                            </td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium <?= $program['status'] === 'active' ? 'bg-green-50 text-green-700' : 'bg-yellow-50 text-yellow-700' ?>">
                                                    <?= ucfirst(htmlspecialchars($program['status'])) ?>
                                                </span>
                                            </td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                                                <div x-data="{ copySuccess: false }" class="flex justify-end gap-2">
                                                    <button @click="navigator.clipboard.writeText('<?= htmlspecialchars($program['tracking_code']) ?>'); copySuccess = true; setTimeout(() => copySuccess = false, 2000)" 
                                                            type="button"
                                                            class="text-indigo-600 hover:text-indigo-900">
                                                        <span x-show="!copySuccess">Copy Code</span>
                                                        <span x-show="copySuccess" x-cloak>Copied!</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }
</style>