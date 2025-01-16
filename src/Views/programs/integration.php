<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:items-center lg:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                    Integration Guide: <?= htmlspecialchars($program['name']) ?>
                </h2>
            </div>
            <div class="mt-5 flex lg:ml-4 lg:mt-0">
                <a href="/admin/programs/<?= $program['id'] ?>/edit" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                    Back to Program
                </a>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Stripe Integration -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Stripe Integration</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>To track affiliate conversions, add the partner's tracking code to your Stripe Checkout Session or Payment Intent metadata.</p>
                    </div>
                    
                    <div class="mt-5">
                        <div class="rounded-md bg-gray-50 p-4">
                            <h4 class="text-sm font-medium text-gray-900">1. Add the Tracking Script</h4>
                            <p class="mt-2 text-sm text-gray-600">Add this script to your website's header:</p>
                            <pre class="mt-2 p-2 bg-gray-100 rounded text-sm overflow-x-auto"><code>&lt;script src="https://<?= $_SERVER['HTTP_HOST'] ?>/tracking/program-<?= $program['id'] ?>.js"&gt;&lt;/script&gt;</code></pre>

                            <h4 class="mt-4 text-sm font-medium text-gray-900">2. Add Tracking to Stripe Checkout</h4>
                            <p class="mt-2 text-sm text-gray-600">When creating a Stripe Checkout Session, add the tracking metadata:</p>
                            <pre class="mt-2 p-2 bg-gray-100 rounded text-sm overflow-x-auto"><code>const session = await stripe.checkout.sessions.create({
  line_items: [...],
  mode: 'payment',
  metadata: window.numok.getStripeMetadata() // Automatically adds tracking data
});</code></pre>
                            
                            <div class="mt-4 rounded-md bg-blue-50 p-3">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-blue-700">
                                            The script automatically handles:
                                        </p>
                                        <ul class="mt-2 list-disc list-inside text-sm text-blue-700">
                                            <li>Cookie management and duration</li>
                                            <li>Tracking code detection from URL</li>
                                            <li>Sub-ID tracking (sid, sid2, sid3)</li>
                                            <li>Click and impression tracking</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Webhook Setup -->
            <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Webhook Configuration</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Configure your Stripe webhook endpoint to receive payment notifications.</p>
                    </div>
                    
                    <div class="mt-5">
                        <div class="rounded-md bg-gray-50 p-4">
                            <h4 class="text-sm font-medium text-gray-900">1. Add Webhook Endpoint</h4>
                            <p class="mt-2 text-sm text-gray-600">Add this URL to your Stripe webhook settings:</p>
                            <pre class="mt-2 p-2 bg-gray-100 rounded text-sm"><?= rtrim(htmlspecialchars($settings['app_url'] ?? 'https://'.$_SERVER['HTTP_HOST']), '/') ?>/webhook/stripe</pre>
                            
                            <h4 class="mt-4 text-sm font-medium text-gray-900">2. Select Events</h4>
                            <p class="mt-2 text-sm text-gray-600">Subscribe to these webhook events:</p>
                            <ul class="mt-2 list-disc list-inside text-sm text-gray-600">
                                <li><code>checkout.session.completed</code></li>
                                <li><code>payment_intent.succeeded</code></li>
                                <li><code>invoice.paid</code></li>
                            </ul>
                        </div>

                        <div class="mt-4 flex items-center">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" />
                            </svg>
                            <p class="ml-2 text-sm text-gray-600">Make sure to add metadata before creating the checkout session.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testing Section -->
        <div class="mt-6 bg-white shadow sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Testing Your Integration</h3>
                <div class="mt-2 max-w-xl text-sm text-gray-500">
                    <p>Follow these steps to test your integration:</p>
                </div>
                
                <div class="mt-5">
                    <div class="rounded-md bg-gray-50 p-4">
                        <ol class="list-decimal list-inside space-y-3 text-sm text-gray-600">
                            <li>Switch to Stripe test mode</li>
                            <li>Create a test partner with a tracking code</li>
                            <li>Add the tracking code to your checkout metadata</li>
                            <li>Complete a test purchase using a <a href="https://stripe.com/docs/testing#cards" target="_blank" class="text-indigo-600 hover:text-indigo-500">Stripe test card</a></li>
                            <li>Verify the conversion appears in your dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>