<?php
// File: src/Views/partner/programs/index.php
?>
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-semibold text-gray-900">Your Programs</h1>
                <p class="mt-2 text-sm text-gray-700">Join programs and get your tracking links to start earning commissions.</p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="rounded-md bg-green-50 p-4 mt-6">
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
        <?php unset($_SESSION['success']); endif; ?>

        <?php if (empty($programs)): ?>
        <div class="text-center mt-16">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No programs available</h3>
            <p class="mt-1 text-sm text-gray-500">Check back later for new program opportunities.</p>
        </div>
        <?php else: ?>
        <div class="mt-8 grid grid-cols-1 gap-6">
            <?php foreach ($programs as $program): ?>
            <div class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($program['name']) ?></h3>
                        <p class="mt-1 text-sm text-gray-500"><?= nl2br(htmlspecialchars($program['description'])) ?></p>
                    </div>
                    <?php if ($program['status'] === 'available'): ?>
                    <form action="/programs/join" method="POST" class="flex-shrink-0">
                        <input type="hidden" name="program_id" value="<?= $program['id'] ?>">
                        <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Join Program
                        </button>
                    </form>
                    <?php endif; ?>
                </div>

                <?php if ($program['status'] === 'joined'): ?>
                <div class="mt-4">
                    <div class="rounded-md bg-gray-50 p-4">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">Your Tracking Link</h4>
                            <div class="mt-1 flex items-center gap-2">
                                <code class="flex-1 text-sm font-mono bg-white px-2 py-1 rounded border border-gray-200">
                                    <?= htmlspecialchars($program['landing_page']) ?>?via=<?= htmlspecialchars($program['tracking_code']) ?>
                                </code>
                                <button onclick="copyToClipboard('<?= htmlspecialchars($program['landing_page']) ?>?via=<?= htmlspecialchars($program['tracking_code']) ?>')" 
                                        class="inline-flex items-center px-2.5 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Copy
                                </button>
                            </div>
                            <div class="mt-2 flex gap-4 text-sm text-gray-500">
                                <span>Code: <code class="font-mono bg-white px-1 rounded"><?= htmlspecialchars($program['tracking_code']) ?></code></span>
                                <span>Cookie: <?= $program['cookie_days'] ?> days</span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <dl class="mt-4 grid grid-cols-3 gap-4 border-t border-gray-200 pt-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Commission</dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            <?php if ($program['commission_type'] === 'percentage'): ?>
                                <?= number_format($program['commission_value'], 1) ?>% of sale
                            <?php else: ?>
                                $<?= number_format($program['commission_value'], 2) ?> per sale
                            <?php endif; ?>
                        </dd>
                    </div>
                    <?php if ($program['is_recurring']): ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Type</dt>
                        <dd class="mt-1 text-sm text-gray-900">Recurring commission</dd>
                    </div>
                    <?php endif; ?>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Sub IDs Available</dt>
                        <dd class="mt-1 text-sm font-mono text-gray-500">sid, sid2, sid3</dd>
                    </div>
                </dl>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
async function copyToClipboard(text) {
    try {
        await navigator.clipboard.writeText(text);
        // Show a temporary success message
        const button = event.target.closest('button');
        const originalText = button.textContent;
        button.textContent = 'Copied!';
        setTimeout(() => {
            button.textContent = originalText;
        }, 2000);
    } catch (err) {
        console.error('Failed to copy text: ', err);
    }
}
</script>