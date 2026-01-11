<?php
// Ensure $program is available
if (!isset($program)) {
    header('Location: /programs');
    exit;
}
?>
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:items-center lg:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    <?= htmlspecialchars($program['name']) ?>
                </h2>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0 space-x-3">
                <a href="/admin/programs" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                    Back to Programs
                </a>
                <?php if ($program['status'] === 'active'): ?>
                    <a href="/admin/programs/<?= $program['id'] ?>/integration"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M14.5 10a4.5 4.5 0 004.284-5.882c-.105-.324-.51-.391-.752-.15L15.34 6.66a.454.454 0 01-.493.11 3.01 3.01 0 01-1.618-1.616.455.455 0 01.11-.494l2.694-2.692c.24-.241.174-.647-.15-.752a4.5 4.5 0 00-5.873 4.575c.055.873-.128 1.808-.8 2.368l-7.23 6.024a2.724 2.724 0 103.837 3.837l6.024-7.23c.56-.672 1.495-.855 2.368-.8.096.007.193.01.291.01zM5 16a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                        </svg>
                        Integration Guide
                    </a>
                <?php endif; ?>
            </div>
        </div>

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
        <?php unset($_SESSION['error']);
        endif; ?>

        <form action="/admin/programs/<?= $program['id'] ?>/update" method="POST" class="mt-8">
            <div class="space-y-12">
                <!-- Program Details Section -->
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Program Details</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">
                            Basic information about your affiliate program.
                        </p>
                    </div>

                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        <div class="sm:col-span-4">
                            <div class="flex justify-between items-center">
                                <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Program Name</label>
                                <span class="text-gray-500 text-xs">Required</span>
                            </div>
                            <div class="mt-2">
                                <input type="text" name="name" id="name" required
                                    value="<?= htmlspecialchars($program['name']) ?>"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                        </div>

                        <div class="col-span-full">
                            <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                            <div class="mt-2">
                                <textarea id="description" name="description" rows="4"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"><?= htmlspecialchars($program['description']) ?></textarea>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">Write a few sentences about your program for partners.</p>
                        </div>

                        <div class="col-span-full">
                            <label for="terms" class="block text-sm font-medium leading-6 text-gray-900">Program Terms</label>
                            <div class="mt-2">
                                <textarea id="terms" name="terms" rows="6"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"><?= htmlspecialchars($program['terms'] ?? '') ?></textarea>
                            </div>
                            <p class="mt-3 text-sm leading-6 text-gray-600">Terms that partners must accept before joining the program.</p>
                        </div>

<div class="sm:col-span-4">
                                            <label for="landing_page" class="block text-sm font-medium leading-6 text-gray-900">Landing Page URL</label>
                                            <div class="mt-2">
                                                <input type="url" name="landing_page" id="landing_page"
                                                    value="<?= htmlspecialchars($program['landing_page']) ?>"
                                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"
                                                    placeholder="https://example.com/landing">
                                            </div>
                                            <p class="mt-2 text-sm text-gray-600">Where partners should send their traffic.</p>
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

                <!-- Commission Settings Section -->
                <div class="grid grid-cols-1 gap-x-8 gap-y-10 border-b border-gray-900/10 pb-12 md:grid-cols-3">
                    <div>
                        <h2 class="text-base font-semibold leading-7 text-gray-900">Commission Settings</h2>
                        <p class="mt-1 text-sm leading-6 text-gray-600">
                            Configure how partners will be rewarded.
                        </p>
                    </div>

                    <div class="grid max-w-2xl grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 md:col-span-2">
                        <div class="sm:col-span-3">
                            <label for="commission_type" class="block text-sm font-medium leading-6 text-gray-900">Commission Type</label>
                            <div class="mt-2">
                                <select id="commission_type" name="commission_type" required
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="percentage" <?= $program['commission_type'] === 'percentage' ? 'selected' : '' ?>>Percentage of Sale</option>
                                    <option value="fixed" <?= $program['commission_type'] === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="commission_value" class="block text-sm font-medium leading-6 text-gray-900">Commission Value</label>
                            <div class="mt-2">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm" x-text="$el.closest('form').querySelector('#commission_type').value === 'fixed' ? '$' : ''"></span>
                                    </div>
                                    <input type="number" name="commission_value" id="commission_value" required
                                        min="0" step="0.01"
                                        value="<?= htmlspecialchars($program['commission_value']) ?>"
                                        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 pl-6">
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 sm:text-sm" x-text="$el.closest('form').querySelector('#commission_type').value === 'percentage' ? '%' : ''"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="cookie_days" class="block text-sm font-medium leading-6 text-gray-900">Cookie Duration (days)</label>
                            <div class="mt-2">
                                <input type="number" name="cookie_days" id="cookie_days" required
                                    min="1" value="<?= htmlspecialchars($program['cookie_days']) ?>"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                            <p class="mt-2 text-sm text-gray-600">How long the attribution will last.</p>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="reward_days" class="block text-sm font-medium leading-6 text-gray-900">Reward Delay (days)</label>
                            <div class="mt-2">
                                <input type="number" name="reward_days" id="reward_days" required
                                    min="0" value="<?= htmlspecialchars($program['reward_days']) ?>"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                            </div>
                            <p class="mt-2 text-sm text-gray-600">Days to wait before marking commission as payable.</p>
                        </div>

                        <div class="col-span-full">
                            <div class="relative flex gap-x-3">
                                <div class="flex h-6 items-center">
                                    <input type="checkbox" name="is_recurring" id="is_recurring"
                                        <?= $program['is_recurring'] ? 'checked' : '' ?>
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                </div>
                                <div class="text-sm leading-6">
                                    <label for="is_recurring" class="font-medium text-gray-900">Recurring Commissions</label>
                                    <p class="text-gray-500">Partners will earn commission on all future payments from referred customers.</p>
                                </div>
                            </div>
                        </div>

                        <div class="sm:col-span-3">
                            <label for="status" class="block text-sm font-medium leading-6 text-gray-900">Status</label>
                            <div class="mt-2">
                                <select id="status" name="status"
                                    class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                    <option value="active" <?= $program['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $program['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                                <p class="mt-2 text-sm text-gray-600">Inactive programs won't accept new conversions.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-x-6 border-t border-gray-900/10 pt-6">
                <button type="button"
                    onclick="window.location.href='/programs'"
                    class="text-sm font-semibold leading-6 text-gray-900">
                    Cancel
                </button>
                <?php if ($program['status'] === 'active'): ?>
                    <button type="submit"
                        name="status"
                        value="inactive"
                        class="rounded-md bg-yellow-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-yellow-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-yellow-600">
                        Deactivate Program
                    </button>
                <?php endif; ?>
                <button type="submit"
                    class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Update commission value input prefix/suffix based on type
    document.getElementById('commission_type').addEventListener('change', function(e) {
                const valueInput = document.getElementById('commission_value');
                const prefix = e.target.value === 'fixed' ? '<?php
                                                                // Ensure $program is available
                                                                if (!isset($program)) {
                                                                    header('Location: /programs');
                                                                    exit;
                                                                }
                                                                ?>