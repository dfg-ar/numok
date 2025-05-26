<?php
// File: src/Views/partner/programs/index.php
?>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex-auto">
                <h1 class="text-3xl font-bold text-gray-900">Available Programs</h1>
                <p class="mt-2 text-lg text-gray-600">Discover and join affiliate programs to start earning commissions</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <?= count(array_filter($programs ?? [], fn($p) => $p['status'] === 'joined')) ?> Programs Joined
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="rounded-lg bg-green-50 p-4 mt-6 border border-green-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($_SESSION['success']) ?></p>
                    </div>
                </div>
            </div>
        <?php unset($_SESSION['success']);
        endif; ?>

        <!-- Empty State -->
        <?php if (empty($programs)): ?>
            <div class="text-center mt-16 py-12">
                <div class="mx-auto h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">No programs available</h3>
                <p class="mt-2 text-gray-500 max-w-md mx-auto">We're working on bringing you exciting affiliate opportunities. Check back soon for new programs to join.</p>
                <div class="mt-6">
                    <button onclick="window.location.reload()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                </div>
            </div>
        <?php else: ?>
            <!-- Programs Grid -->
            <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-2">
                <?php foreach ($programs as $program): ?>
                    <div class="relative group">
                        <!-- Program Card -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 overflow-hidden">
                            <!-- Status Badge -->
                            <div class="absolute top-4 right-4 z-10">
                                <?php if ($program['status'] === 'joined'): ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                        </svg>
                                        Joined
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                        </svg>
                                        Available
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Card Header -->
                            <div class="p-6 pb-4">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 pr-4">
                                        <h3 class="text-xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($program['name']) ?></h3>
                                        <p class="text-gray-600 text-sm leading-relaxed"><?= nl2br(htmlspecialchars($program['description'])) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Commission Highlight -->
                            <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-purple-50 border-y border-gray-100">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-medium text-gray-500">Commission Rate</p>
                                            <p class="text-lg font-bold text-gray-900">
                                                <?php if ($program['commission_type'] === 'percentage'): ?>
                                                    <?= number_format($program['commission_value'], 1) ?>% <span class="text-sm font-normal text-gray-500">of sale</span>
                                                <?php else: ?>
                                                    $<?= number_format($program['commission_value'], 2) ?> <span class="text-sm font-normal text-gray-500">per sale</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <?php if ($program['is_recurring']): ?>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                                                </svg>
                                                Recurring
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Program Details -->
                            <div class="p-6">
                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Cookie Duration</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1"><?= $program['cookie_days'] ?> days</p>
                                    </div>
                                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Sub IDs</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1">sid, sid2, sid3</p>
                                    </div>
                                </div>

                                <!-- Tracking Link Section (for joined programs) -->
                                <?php if ($program['status'] === 'joined'): ?>
                                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="text-sm font-semibold text-gray-900 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd" />
                                                </svg>
                                                Your Tracking Link
                                            </h4>
                                            <span class="text-xs text-gray-500 font-mono bg-white px-2 py-1 rounded border">
                                                <?= htmlspecialchars($program['tracking_code']) ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <code class="flex-1 text-sm font-mono bg-white px-3 py-2 rounded border border-gray-200 text-gray-700 break-all">
                                                <?= htmlspecialchars($program['landing_page']) ?>?via=<?= htmlspecialchars($program['tracking_code']) ?>
                                            </code>
                                            <button onclick="copyToClipboard('<?= htmlspecialchars($program['landing_page']) ?>?via=<?= htmlspecialchars($program['tracking_code']) ?>')"
                                                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                                </svg>
                                                Copy
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <?php if ($program['status'] === 'available'): ?>
                                    <div class="space-y-4">
                                        <?php if (!empty($program['terms'])): ?>
                                            <!-- Terms Preview -->
                                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-lg">
                                                <div class="flex items-start">
                                                    <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                    </svg>
                                                    <div class="flex-1">
                                                        <h5 class="text-sm font-medium text-amber-800">Program Terms Required</h5>
                                                        <p class="text-sm text-amber-700 mt-1">Please review and accept the program terms before joining.</p>
                                                        <button type="button"
                                                            onclick="showTermsModal('<?= $program['id'] ?>')"
                                                            class="mt-3 inline-flex items-center px-3 py-1.5 border border-amber-300 text-xs font-medium rounded-md text-amber-800 bg-amber-100 hover:bg-amber-200 hover:border-amber-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-colors">
                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            View Terms & Conditions
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Join Form with Terms -->
                                            <form action="/programs/join" method="POST" class="space-y-4">
                                                <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                                                <div class="flex items-start">
                                                    <div class="flex h-6 items-center">
                                                        <input type="checkbox" required
                                                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                                    </div>
                                                    <div class="ml-3">
                                                        <label class="text-sm text-gray-700 font-medium">
                                                            I have read and accept the program terms and conditions
                                                        </label>
                                                    </div>
                                                </div>
                                                <button type="submit"
                                                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                                    </svg>
                                                    Join Program
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <!-- Direct Join Button -->
                                            <form action="/programs/join" method="POST">
                                                <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                                                <button type="submit" 
                                                    class="w-full inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                                    </svg>
                                                    Join Program
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Already Joined State -->
                                    <div class="space-y-4">
                                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-green-800">You're part of this program!</p>
                                                    <p class="text-sm text-green-700">Start promoting and earning commissions with your tracking link above.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if (!empty($program['terms'])): ?>
                                            <div class="text-center">
                                                <button type="button"
                                                    onclick="showTermsModal('<?= $program['id'] ?>')"
                                                    class="inline-flex items-center px-3 py-1.5 border border-indigo-300 text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100 hover:border-indigo-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    View Program Terms
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Terms Modal -->
<div id="termsModal" class="relative z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
            <div class="relative transform overflow-hidden rounded-xl bg-white shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                <!-- Modal Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-100">
                                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                    Program Terms & Conditions
                                </h3>
                                <p class="text-sm text-gray-500">Please review the following terms carefully</p>
                            </div>
                        </div>
                        <button type="button" onclick="hideTermsModal()" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="px-6 py-6">
                    <div class="max-h-96 overflow-y-auto">
                        <div id="termsContent" class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap bg-gray-50 p-4 rounded-lg border border-gray-200"></div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3 border-t border-gray-200">
                    <button type="button" onclick="hideTermsModal()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        Close
                    </button>
                    <button type="button" onclick="printTerms()" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Terms
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Copy to clipboard functionality
    async function copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = `
                <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Copied!
            `;
            button.classList.add('text-green-600', 'border-green-300');
            setTimeout(() => {
                button.innerHTML = originalContent;
                button.classList.remove('text-green-600', 'border-green-300');
            }, 2000);
        } catch (err) {
            console.error('Failed to copy text: ', err);
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        }
    }

    // Terms modal functionality
    const programTerms = {
        <?php foreach ($programs as $program): ?>
            <?php if (!empty($program['terms'])): ?>
                '<?= $program['id'] ?>': <?= json_encode(nl2br(htmlspecialchars($program['terms']))) ?>,
            <?php endif; ?>
        <?php endforeach; ?>
    };

    function showTermsModal(programId) {
        const modal = document.getElementById('termsModal');
        const content = document.getElementById('termsContent');
        content.innerHTML = programTerms[programId] || 'No terms available.';
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideTermsModal() {
        const modal = document.getElementById('termsModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function printTerms() {
        const content = document.getElementById('termsContent').innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Program Terms & Conditions</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 40px; line-height: 1.6; }
                        h1 { color: #4f46e5; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
                        .content { margin-top: 20px; }
                        @media print { body { margin: 20px; } }
                    </style>
                </head>
                <body>
                    <h1>Program Terms & Conditions</h1>
                    <div class="content">${content}</div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideTermsModal();
        }
    });

    // Close modal on backdrop click
    document.getElementById('termsModal').addEventListener('click', function(event) {
        if (event.target === this) {
            hideTermsModal();
        }
    });
</script>