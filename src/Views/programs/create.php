<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:items-center lg:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Create Program
                </h2>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <span class="hidden sm:block">
                    <a href="/admin/programs" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                        </svg>
                        Back
                    </a>
                </span>
            </div>
        </div>

        <form action="/admin/programs<?= isset($program) ? "/{$program['id']}/update" : '/store' ?>" method="POST" class="mt-8">
            <div class="space-y-12">
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Program Details</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">
                            Configure your affiliate program settings including commission structure and attribution rules.
                        </p>
                    </div>

                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        <div class="sm:col-span-4">
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Program Name</label>
                            <div class="mt-2">
                                <input type="text" name="name" id="name" required
                                       value="<?= htmlspecialchars($program['name'] ?? '') ?>"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                        </div>

                        <div class="col-span-full">
                            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                            <div class="mt-2">
                                <textarea id="description" name="description" rows="3" 
                                          class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"><?= htmlspecialchars($program['description'] ?? '') ?></textarea>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">Write a few sentences describing your program to partners.</p>
                        </div>

                        <div class="col-span-full">
                            <label for="terms" class="block text-sm font-medium leading-6 text-gray-900">Program Terms</label>
                            <div class="mt-2">
                                <textarea id="terms" name="terms" rows="6" 
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"><?= htmlspecialchars($program['terms'] ?? '') ?>Affiliate Program Terms & Conditions
By joining the Affiliate Program ("Program"), you agree to comply with the following terms and conditions.

1. Eligibility
You must be at least 18 years old to participate.
Participation is subject to approval by the Program Admin, who may accept or reject any affiliate at their sole discretion.

2. Prohibited Activities
You agree NOT to:

Engage in fraudulent, deceptive, or misleading practices.
Use spam, unsolicited emails, or aggressive marketing tactics.
Promote content that is illegal, violent, hateful, or contains adult material.
Bid on Program trademarks or brand terms in paid search campaigns without explicit permission.
Falsify transactions or artificially inflate commissions.

3. Commissions & Payments
Commissions are only earned on valid, fully paid, and non-refunded transactions.
The Program Admin reserves the right to adjust, reject, or delay payments in case of suspected fraud or violations of these terms.
Payouts are made on a [e.g., monthly] basis via [payment method], subject to a minimum payout threshold of [$X].

4. Termination
Either party may terminate participation at any time, with or without cause.
If terminated due to a violation of these terms, any pending commissions may be forfeited.

5. Program Modifications
The Program Admin reserves the right to update or modify these terms at any time. Continued participation constitutes acceptance of any changes.

6. Final Discretion
The Program Admin has the final authority regarding affiliate eligibility, commission payments, and program enforcement.
By participating in the Program, you acknowledge and agree to these terms.</textarea>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">Terms that partners must accept before joining the program.</p>
                        </div>

<div class="sm:col-span-4">
                                            <label for="landing_page" class="block text-sm font-medium leading-6 text-gray-900">Landing Page URL</label>
                                            <div class="mt-2">
                                                <input type="url" name="landing_page" id="landing_page" required
                                                       value="<?= htmlspecialchars($program['landing_page'] ?? '') ?>"
                                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                            </div>
                                            <p class="mt-3 text-sm leading-6 text-gray-600">The page where partners should send their traffic.</p>
                                        </div>

                                        <div class="col-span-full">
                                            <div class="relative flex gap-x-3">
                                                <div class="flex h-6 items-center">
                                                    <input type="checkbox" name="is_private" id="is_private" 
                                                           <?= ($program['is_private'] ?? false) ? 'checked' : '' ?>
                                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                                </div>
                                                <div class="text-sm leading-6">
                                                    <label for="is_private" class="font-medium text-gray-900">Private Program</label>
                                                    <p class="text-gray-500">Private programs are not visible to partners. Only partners manually assigned by an admin can access this program.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Commission Settings</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">
                            Define how partners will be rewarded for bringing customers.
                        </p>
                    </div>

                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        <div class="sm:col-span-3">
                            <label for="commission_type" class="block text-sm font-medium leading-6 text-gray-900">Commission Type</label>
                            <div class="mt-2">
                                <select id="commission_type" name="commission_type" 
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="percentage" <?= ($program['commission_type'] ?? '') === 'percentage' ? 'selected' : '' ?>>Percentage</option>
                                    <option value="fixed" <?= ($program['commission_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="commission_value" class="block text-sm font-medium leading-6 text-gray-900">Commission Value</label>
                            <div class="mt-2">
                                <input type="tel" name="commission_value" id="commission_value" step="0.01" required
                                       value="<?= htmlspecialchars($program['commission_value'] ?? '0') ?>"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="cookie_days" class="block text-sm font-medium leading-6 text-gray-900">Cookie Duration (days)</label>
                            <div class="mt-2">
                                <input type="number" name="cookie_days" id="cookie_days" min="1" required
                                       value="<?= htmlspecialchars($program['cookie_days'] ?? '30') ?>"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">How long the attribution will last.</p>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="reward_days" class="block text-sm font-medium leading-6 text-gray-900">Reward Delay (days)</label>
                            <div class="mt-2">
                                <input type="number" name="reward_days" id="reward_days" min="0"
                                       value="<?= htmlspecialchars($program['reward_days'] ?? '0') ?>"
                                       class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">Days to wait before marking commission as payable.</p>
                        </div>

                        <div class="col-span-full">
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input type="checkbox" name="is_recurring" id="is_recurring" 
                                           <?= ($program['is_recurring'] ?? false) ? 'checked' : '' ?>
                                           class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="is_recurring" class="font-medium text-gray-900">Recurring Commissions</label>
                                    <p class="text-gray-500">Partners will earn commission on all future payments from the customer.</p>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($program)): ?>
                        <div class="sm:col-span-3">
                            <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                            <div class="mt-2">
                                <select id="status" name="status" 
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="active" <?= $program['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $program['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6">
                <a href="/admin/programs" class="text-sm font-semibold leading-6 text-gray-900">Cancel</a>
                <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    <?= isset($program) ? 'Update Program' : 'Create Program' ?>
                </button>
            </div>
        </form>
    </div>
</div>