<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">Partners</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all your affiliate partners and their performance.</p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <a href="/admin/partners/create" class="block rounded-md bg-indigo-600 px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Create Partner
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

        <?php if (empty($partners)): ?>
        <div class="text-center mt-16">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No partners</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating your first partner.</p>
            <div class="mt-6">
                <a href="/admin/partners/create" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Create Partner
                </a>
            </div>
        </div>
        <?php else: ?>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Partner</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Programs</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Conversions</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Revenue</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Commission</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($partners as $partner): ?>
                            <tr>
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-900 sm:pl-6">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-500">
                                                <span class="text-sm font-medium leading-none text-white">
                                                    <?= strtoupper(substr($partner['contact_name'] ?? $partner['company_name'], 0, 1)) ?>
                                                </span>
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="font-medium text-gray-900"><?= htmlspecialchars($partner['company_name']) ?></div>
                                            <div class="text-gray-500">
                                                <?= htmlspecialchars($partner['contact_name']) ?> · <?= htmlspecialchars($partner['email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= number_format($partner['total_programs']) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <?= number_format($partner['total_conversions']) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    $<?= number_format($partner['total_revenue'], 2) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    $<?= number_format($partner['total_commission'], 2) ?>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium <?php
                                        switch($partner['status']):
                                            case 'active':
                                                echo 'bg-green-50 text-green-700';
                                                break;
                                            case 'pending':
                                                echo 'bg-yellow-50 text-yellow-700';
                                                break;
                                            case 'suspended':
                                                echo 'bg-red-50 text-red-700';
                                                break;
                                            default:
                                                echo 'bg-gray-50 text-gray-700';
                                        endswitch; ?>">
                                        <?= ucfirst(htmlspecialchars($partner['status'])) ?>
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button @click="open = !open" type="button" class="p-1 rounded-full hover:bg-gray-50">
                                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        
                                        <div x-show="open" 
                                             @click.away="open = false"
                                             class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
                                             role="menu" 
                                             aria-orientation="vertical" 
                                             aria-labelledby="menu-button" 
                                             tabindex="-1"
                                             style="display: none;">
                                            <div class="py-1" role="none">
                                                <a href="/admin/partners/<?= $partner['id'] ?>/edit" 
                                                   class="text-gray-700 group flex items-center px-4 py-2 text-sm hover:bg-gray-100 hover:text-gray-900" 
                                                   role="menuitem">
                                                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                        <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                    </svg>
                                                    Edit
                                                </a>

                                                <?php if ($partner['status'] === 'pending'): ?>
                                                <form action="/admin/partners/<?= $partner['id'] ?>/update" 
                                                      method="POST" 
                                                      class="block w-full" 
                                                      role="none">
                                                    <input type="hidden" name="status" value="active">
                                                    <button type="submit" 
                                                            class="text-green-700 group flex items-center px-4 py-2 text-sm hover:bg-gray-100 hover:text-green-900 w-full" 
                                                            role="menuitem">
                                                        <svg class="mr-3 h-5 w-5 text-green-400 group-hover:text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                        </svg>
                                                        Approve
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <?php if ($partner['status'] === 'active'): ?>
                                                <form action="/admin/partners/<?= $partner['id'] ?>/update" 
                                                      method="POST" 
                                                      class="block w-full" 
                                                      role="none">
                                                    <input type="hidden" name="status" value="suspended">
                                                    <button type="submit" 
                                                            class="text-yellow-700 group flex items-center px-4 py-2 text-sm hover:bg-gray-100 hover:text-yellow-900 w-full" 
                                                            role="menuitem"
                                                            onclick="return confirm('Are you sure you want to suspend this partner?')">
                                                        <svg class="mr-3 h-5 w-5 text-yellow-400 group-hover:text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        Suspend
                                                    </button>
                                                </form>
                                                <?php endif; ?>

                                                <form action="/admin/partners/<?= $partner['id'] ?>/delete" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this partner?')" 
                                                      class="block w-full border-t border-gray-100" 
                                                      role="none">
                                                    <button type="submit" 
                                                            class="text-red-700 group flex items-center px-4 py-2 text-sm hover:bg-gray-100 hover:text-red-900 w-full" 
                                                            role="menuitem">
                                                        <svg class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>