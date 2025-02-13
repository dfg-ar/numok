<!-- Add Prism.js core -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/components/prism-core.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/autoloader/prism-autoloader.min.js"></script>

<!-- Optional: Add line numbers plugin -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js"></script>

<style>
    /* Custom code block styling */
    pre[class*="language-"] {
        padding: 1em;
        margin: 0.5em 0;
        border-radius: 0.5rem;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        font-size:80%;
    }

    .code-annotation {
        color: #60a5fa;  /* Blue 400 */
        font-weight: 500;
        margin-top: 0.5rem;
        margin-bottom: 0.25rem;
    }

    .integration-highlight {
        background-color: rgba(96, 165, 250, 0.1);  /* Blue 400 with opacity */
        border-left: 3px solid #60a5fa;
        margin-left: -1em;
        margin-right: -1em;
        padding-left: 0.85em;
        padding-right: 0.85em;
    }
</style>

<div class="min-h-full">
    <main>
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            <!-- Breadcrumbs -->
            <nav class="flex mb-8" aria-label="Breadcrumb">
                <ol role="list" class="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="/admin/programs" class="text-gray-500 hover:text-gray-700">
                                Programs
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <a href="/admin/programs/<?= $program['id'] ?>/edit" class="ml-4 text-gray-500 hover:text-gray-700">
                                <?= htmlspecialchars($program['name']) ?>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                            </svg>
                            <span class="ml-4 text-gray-700 font-medium">Integration</span>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Program Context -->
            <div class="mb-8 bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                <?= htmlspecialchars($program['name']) ?> Integration Guide
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                <?= $program['commission_type'] === 'percentage' 
                                    ? number_format($program['commission_value'], 1) . '% commission' 
                                    : '$' . number_format($program['commission_value'], 2) . ' per sale' ?>
                                · <?= $program['cookie_days'] ?> day cookie
                                <?= $program['is_recurring'] ? '· Recurring commissions' : '' ?>
                            </p>
                        </div>
                        <div class="mt-4 sm:ml-16 sm:mt-0">
                            <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
                                Active Program
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-0">
                <h1 class="text-2xl font-semibold text-gray-900">Integration Guide</h1>
                <p class="mt-1 text-sm text-gray-600">Follow these steps to integrate affiliate tracking with your website.</p>
            </div>

            <div class="mt-8 space-y-8">
                <!-- Step 1: Add Tracking Script -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">1. Add Tracking Script</h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            <p>Add this script to every page of your website to track affiliate referrals. Place it in the <code>&lt;head&gt;</code> section:</p>
                        </div>
                        <div class="mt-3">
                            <div class="rounded-md bg-gray-50 p-4">
                                <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code class="language-html">&lt;script src="https://<?= $_SERVER['HTTP_HOST'] ?>/tracking/program-<?= $program['id'] ?>.js"&gt;&lt;/script&gt;</code></pre>
                            </div>
                        </div>
                        <div class="mt-3 text-sm">
                            <p class="font-medium text-gray-900">What this script does:</p>
                            <ul class="mt-2 list-disc pl-5 text-gray-500 space-y-1">
                                <li>Detects affiliate tracking codes in URLs (<code>?via=TRACKING_CODE</code>)</li>
                                <li>Stores tracking data in cookies (30 day duration)</li>
                                <li>Tracks clicks and page views</li>
                                <li>Provides Stripe integration helpers</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Add Tracking to Stripe -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">2. Add Tracking to Stripe</h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            <p>Now we need to send the tracking code captured in the previous step, to Stripe Checkout.</p>
                            <p>Search for our comments 👆👇 to find the changes you need to apply in your code.</p>
                        </div>

                        <!-- Language Tabs -->
                        <div class="mt-4" x-data="{ tab: 'html' }">
                            <div class="sm:hidden">
                                <label for="lang-tabs" class="sr-only">Select a language</label>
                                <select id="lang-tabs" name="tabs" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                        x-model="tab">
                                    <option value="html">HTML + JavaScript</option>
                                    <option value="php">PHP</option>
                                    <option value="python">Python</option>
                                    <option value="ruby">Ruby</option>
                                </select>
                            </div>
                            <div class="hidden sm:block">
                                <nav class="flex space-x-4" aria-label="Tabs">
                                    <button @click="tab = 'html'" :class="{ 'bg-gray-100 text-gray-700': tab === 'html', 'text-gray-500 hover:text-gray-700': tab !== 'html' }" class="px-3 py-2 text-sm font-medium rounded-md">HTML + JavaScript</button>
                                    <button @click="tab = 'php'" :class="{ 'bg-gray-100 text-gray-700': tab === 'php', 'text-gray-500 hover:text-gray-700': tab !== 'php' }" class="px-3 py-2 text-sm font-medium rounded-md">PHP</button>
                                    <button @click="tab = 'python'" :class="{ 'bg-gray-100 text-gray-700': tab === 'python', 'text-gray-500 hover:text-gray-700': tab !== 'python' }" class="px-3 py-2 text-sm font-medium rounded-md">Python</button>
                                    <button @click="tab = 'ruby'" :class="{ 'bg-gray-100 text-gray-700': tab === 'ruby', 'text-gray-500 hover:text-gray-700': tab !== 'ruby' }" class="px-3 py-2 text-sm font-medium rounded-md">Ruby</button>
                                </nav>
                            </div>

                            <div class="mt-4">
                                <!-- HTML + JS -->
                                <div x-show="tab === 'html'" class="rounded-md bg-gray-50 p-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code class="language-javascript">&lt;!-- Stripe Checkout Button --&gt;
&lt;button id="checkout-button"&gt;Checkout&lt;/button&gt;

&lt;script src="https://js.stripe.com/v3/"&gt;&lt;/script&gt;
&lt;script&gt;
const stripe = Stripe('your_publishable_key');

document.getElementById('checkout-button').addEventListener('click', async () => {
// Create checkout session
const response = await fetch('/create-checkout-session', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        // Regular Stripe checkout data goes here...
        price: 'price_123',
        quantity: 1,
        
        // 👇 NUMOK: Add affiliate tracking metadata
        <span class="integration-highlight">metadata: window.numok.getStripeMetadata(),</span>
        // ☝️ NUMOK: This adds tracking_code, sid, sid2, sid3

    })
});

const session = await response.json();

// Redirect to checkout
stripe.redirectToCheckout({
    sessionId: session.id
});
});
&lt;/script&gt;</code></pre>
                                </div>

                                <!-- PHP -->
                                <div x-show="tab === 'php'" class="rounded-md bg-gray-50 p-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code class="language-php">// composer require stripe/stripe-php
require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('your_secret_key');

// Get tracking data from cookie
$trackingData = isset($_COOKIE['numok_tracking']) 
? json_decode($_COOKIE['numok_tracking'], true) 
: [];

// 👇 NUMOK: Load cookie values
$trackingData = isset($_COOKIE['numok_tracking']) 
    ? json_decode($_COOKIE['numok_tracking'], true) 
    : [];
// ☝️ NUMOK

// Create checkout session
$session = \Stripe\Checkout\Session::create([
    // Regular Stripe configuration
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price' => 'price_H5ggYwtDq4fbrJ',
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => 'https://example.com/success',
    'cancel_url' => 'https://example.com/cancel',

    // 👇 NUMOK: Add affiliate tracking metadata
    'metadata' => [
        <span class="integration-highlight">'numok_tracking_code' => $trackingData['tracking_code'] ?? null,
        'numok_sid' => $trackingData['sid'] ?? null,
        'numok_sid2' => $trackingData['sid2'] ?? null,
        'numok_sid3' => $trackingData['sid3'] ?? null</span>
    ]
    // ☝️ NUMOK: This metadata will be used to track affiliate conversions
    
]);</code></pre>
                                </div>

                                <!-- Python -->
                                <div x-show="tab === 'python'" class="rounded-md bg-gray-50 p-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code class="language-python"># pip install stripe
import stripe
import json
from flask import request

stripe.api_key = 'your_secret_key'

# 👇 NUMOK: Get tracking data from cookie
tracking_data = json.loads(request.cookies.get('numok_tracking', '{}'))
# ☝️ NUMOK

# Create checkout session
session = stripe.checkout.Session.create(
payment_method_types=['card'],
line_items=[{
    'price': 'price_H5ggYwtDq4fbrJ',
    'quantity': 1,
}],
mode='payment',
success_url='https://example.com/success',
cancel_url='https://example.com/cancel',

# 👇 NUMOK: Add affiliate tracking metadata
metadata={
    'numok_tracking_code': tracking_data.get('tracking_code'),
    'numok_sid': tracking_data.get('sid'),
    'numok_sid2': tracking_data.get('sid2'),
    'numok_sid3': tracking_data.get('sid3')
}
# ☝️ NUMOK: This adds tracking_code, sid, sid2, sid3

)</code></pre>
                                </div>

                                <!-- Ruby -->
                                <div x-show="tab === 'ruby'" class="rounded-md bg-gray-50 p-4">
                                    <pre class="text-sm text-gray-800 whitespace-pre-wrap"><code class="language-ruby"># gem install stripe
require 'stripe'
require 'json'

Stripe.api_key = 'your_secret_key'

# 👇 NUMOK: Get tracking data from cookie
tracking_data = JSON.parse(cookies[:numok_tracking] || '{}')
# ☝️ NUMOK

# Create checkout session
session = Stripe::Checkout::Session.create({
payment_method_types: ['card'],
line_items: [{
price: 'price_H5ggYwtDq4fbrJ',
quantity: 1,
}],
mode: 'payment',
success_url: 'https://example.com/success',
cancel_url: 'https://example.com/cancel',

# 👇 NUMOK: Add affiliate tracking metadata
metadata: {
numok_tracking_code: tracking_data['tracking_code'],
numok_sid: tracking_data['sid'],
numok_sid2: tracking_data['sid2'],
numok_sid3: tracking_data['sid3']
}
# ☝️ This adds: tracking_code, sid, sid2, sid3

})</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Configure Webhook -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">3. Configure Webhook</h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            <p>Configure your Stripe webhook endpoint to receive payment notifications.</p>
                        </div>
                        <div class="mt-3">
                            <div class="rounded-md bg-gray-50 p-4">
                                <h4 class="text-sm font-medium text-gray-900">Webhook URL</h4>
                                <pre class="mt-2 text-sm text-gray-800"><?= rtrim('https://'.$_SERVER['HTTP_HOST'], '/') ?>/webhook/stripe</pre>

                                <h4 class="mt-4 text-sm font-medium text-gray-900">Required Events</h4>
                                <ul class="mt-2 list-disc pl-5 text-sm text-gray-600 space-y-1">
                                    <li><code>checkout.session.completed</code></li>
                                    <li><code>payment_intent.succeeded</code></li>
                                    <li><code>invoice.paid</code> (for recurring payments)</li>
                                </ul>

                                <div class="mt-4">
                                    <a href="https://dashboard.stripe.com/webhooks" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-500">
                                        Configure webhooks in Stripe Dashboard →
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Testing Your Integration</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Follow these steps to test your integration:</p>
                    </div>
                    <div class="mt-3">
                        <div class="rounded-md bg-gray-50 p-4">
                            <ol class="list-decimal list-inside space-y-3 text-sm text-gray-600">
                                <li>Visit your landing page with the tracking parameter:
                                    <div class="mt-1">
                                        <a href="<?= htmlspecialchars($program['landing_page']) ?>?via=TEST123" target="_blank" class="text-indigo-600 hover:text-indigo-500 break-all">
                                            <?= htmlspecialchars($program['landing_page']) ?>?via=TEST123
                                        </a>
                                        👈 You need to use a real tracking code from your partners.
                                    </div>
                                </li>
                                <li>Make a test purchase using a Stripe test card:
                                    <div class="mt-2 bg-white rounded border border-gray-200 p-3">
                                        <table class="min-w-full text-sm">
                                            <tr>
                                                <td class="font-medium pr-4">Card Number</td>
                                                <td><code>4242 4242 4242 4242</code></td>
                                            </tr>
                                            <tr>
                                                <td class="font-medium pr-4">Expiry</td>
                                                <td>Any future date</td>
                                            </tr>
                                            <tr>
                                                <td class="font-medium pr-4">CVC</td>
                                                <td>Any 3 digits</td>
                                            </tr>
                                        </table>
                                        <div class="mt-2">
                                            <a href="https://stripe.com/docs/testing#cards" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-500">
                                                View more test cards →
                                            </a>
                                        </div>
                                    </div>
                                </li>
                                <li>Verify the tracking data appears in your Stripe Dashboard under Payment details</li>
                                <li>Check your Numok dashboard for the recorded conversion</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Tools -->
            <div class="bg-white shadow sm:rounded-lg mt-8">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Verification Tools</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Use these tools to verify your integration:</p>
                    </div>
                    <div class="mt-3">
                        <div class="rounded-md bg-gray-50 p-4">
                            <ul class="list-disc list-inside space-y-3 text-sm text-gray-600">
                                <li>Open browser developer tools (F12) and check:
                                    <ul class="list-disc list-inside ml-4 mt-2 space-y-1">
                                        <li>Network tab: Verify the tracking script loads</li>
                                        <li>Application tab: Check for <code>numok_tracking</code> cookie</li>
                                        <li>Console tab: Look for any tracking-related errors</li>
                                    </ul>
                                </li>
                                <li>Use Stripe's test webhook tool to simulate payments:
                                    <div class="mt-2">
                                        <a href="https://dashboard.stripe.com/test/webhooks" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                                            Test webhooks in Stripe Dashboard →
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support Resources -->
            <div class="bg-white shadow sm:rounded-lg mt-8">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900">Support Resources</h3>
                    <div class="mt-2">
                        <ul class="divide-y divide-gray-200">
                            <li class="py-4">
                                <a href="https://github.com/dfg-ar/numok/issues" class="group block">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">GitHub Issues</p>
                                            <p class="text-sm text-gray-500">Report bugs or request features</p>
                                        </div>
                                        <div class="ml-auto">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="py-4">
                                <a href="https://stripe.com/docs/webhooks/test" class="group block">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 group-hover:text-indigo-600">Stripe Webhook Guide</p>
                                            <p class="text-sm text-gray-500">Learn more about testing webhooks</p>
                                        </div>
                                        <div class="ml-auto">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-indigo-500" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
    // Highlight Numok integration parts
    document.addEventListener('DOMContentLoaded', () => {
        const codeBlocks = document.querySelectorAll('pre code');
        codeBlocks.forEach(block => {
            const lines = block.innerHTML.split('\n');
            const highlightedLines = lines.map(line => {
                if (line.includes('NUMOK:') || line.includes('numok_')) {
                    return `<span class="integration-highlight">${line}</span>`;
                }
                return line;
            });
            block.innerHTML = highlightedLines.join('\n');
            Prism.highlightElement(block);
        });
    });
</script>