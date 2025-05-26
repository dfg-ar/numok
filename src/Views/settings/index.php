<?php
// src/Views/settings/index.php
?>
<div class="py-6">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-semibold text-gray-900">Settings</h1>
    </div>
    
    <div class="mx-auto max-w-7xl px-4 sm:px-6 md:px-8">
        <?php if (isset($success)): ?>
        <div class="rounded-md bg-green-50 p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?= htmlspecialchars($success) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="rounded-md bg-red-50 p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($error) ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="mt-6 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
            <div class="md:col-span-2 space-y-6">
                <!-- Admin Profile Section -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Admin Profile</h3>
                        <div class="mt-4 max-w-xl">
                            <form action="/admin/settings/update-profile" method="POST">
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                                    <input type="email" name="email" id="email" 
                                           value="<?= htmlspecialchars($_SESSION['user_email']) ?>"
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                </div>

                                <!-- Current Password -->
                                <div class="mb-4">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" required
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                </div>

                                <!-- New Password -->
                                <div class="mb-4">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="new_password" id="new_password" minlength="8"
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                    <p class="mt-1 text-sm text-gray-500">Leave blank to keep current password</p>
                                </div>

                                <!-- Confirm New Password -->
                                <div class="mb-4">
                                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="confirm_password" id="confirm_password"
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                </div>

                                <div class="mt-6">
                                    <button type="submit" 
                                            class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Update Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Custom Branding -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Custom Branding</h3>
                        <div class="mt-4 max-w-xl">
                            <form action="/admin/settings/update-branding" method="POST" enctype="multipart/form-data">
                                <!-- Custom App Name -->
                                <div class="mb-4">
                                    <label for="custom_app_name" class="block text-sm font-medium text-gray-700">Custom App Name</label>
                                    <input type="text" name="custom_app_name" id="custom_app_name" 
                                           value="<?= htmlspecialchars($settings['custom_app_name'] ?? '') ?>"
                                           placeholder="Enter your app name"
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                    <p class="mt-2 text-sm text-gray-500">This will replace "Numok" throughout the application.</p>
                                </div>

                                <!-- Custom Logo Upload -->
                                <div class="mb-4">
                                    <label for="custom_logo" class="block text-sm font-medium text-gray-700">Custom Logo</label>
                                    <?php if (!empty($settings['custom_logo'])): ?>
                                    <div class="mt-2 mb-3">
                                        <img src="/assets/uploads/<?= htmlspecialchars($settings['custom_logo']) ?>" alt="Current Logo" class="h-12 max-w-48 object-contain">
                                        <p class="text-sm text-gray-500 mt-1">Current logo</p>
                                    </div>
                                    <?php endif; ?>
                                    <input type="file" name="custom_logo" id="custom_logo" accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-2 text-sm text-gray-500">Upload a PNG, JPG, or SVG file. Recommended size: 200x50px or similar aspect ratio.</p>
                                </div>

                                <div class="mt-6 flex space-x-3">
                                    <button type="submit" 
                                            class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Update Branding
                                    </button>
                                    <?php if (!empty($settings['custom_app_name']) || !empty($settings['custom_logo'])): ?>
                                    <button type="button" 
                                            onclick="resetBranding()"
                                            class="inline-flex justify-center rounded-md bg-gray-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                        Reset to Numok Branding
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>



                <!-- Stripe Integration -->
                <div class="bg-white shadow sm:rounded-lg" x-data="{ 
                    testResults: null, 
                    testing: false,
                    async testConnection() {
                        this.testing = true;
                        try {
                            const response = await fetch('/admin/settings/test-connection');
                            this.testResults = await response.json();
                        } catch (error) {
                            this.testResults = {
                                success: false,
                                messages: [{
                                    type: 'error',
                                    text: 'Failed to test connection. Please try again.'
                                }]
                            };
                        } finally {
                            this.testing = false;
                        }
                    }
                }">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">Stripe Integration</h3>
                            <?php if (!empty($settings['stripe_secret_key']) || !empty($settings['stripe_webhook_secret'])): ?>
                            <button type="button"
                                    @click="testConnection()"
                                    :disabled="testing"
                                    class="rounded bg-white px-2 py-1 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 disabled:opacity-50">
                                <span x-show="!testing">Test Connection</span>
                                <span x-show="testing">Testing...</span>
                            </button>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mt-4 space-y-4 max-w-xl">
                            <form action="/admin/settings/update" method="POST">
                                <div class="mb-4">
                                    <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700">Secret Key</label>
                                    <input type="password" name="stripe_secret_key" id="stripe_secret_key" 
                                           value="<?= htmlspecialchars($settings['stripe_secret_key'] ?? '') ?>"
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                </div>
                                
                                <div class="mb-4">
                                    <label for="stripe_webhook_secret" class="block text-sm font-medium text-gray-700">Webhook Secret</label>
                                    <input type="password" name="stripe_webhook_secret" id="stripe_webhook_secret" 
                                           value="<?= htmlspecialchars($settings['stripe_webhook_secret'] ?? '') ?>"
                                           class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3">
                                </div>

                                <template x-if="testResults?.messages">
                                    <div class="space-y-3 mb-4">
                                        <template x-for="message in testResults.messages" :key="message.text">
                                            <div :class="{
                                                'rounded-md p-3 text-sm': true,
                                                'bg-green-50 text-green-700': message.type === 'success',
                                                'bg-red-50 text-red-700': message.type === 'error',
                                                'bg-yellow-50 text-yellow-700': message.type === 'warning'
                                            }">
                                                <div class="flex">
                                                    <div class="flex-shrink-0">
                                                        <template x-if="message.type === 'success'">
                                                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" />
                                                            </svg>
                                                        </template>
                                                        <template x-if="message.type === 'error'">
                                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" />
                                                            </svg>
                                                        </template>
                                                        <template x-if="message.type === 'warning'">
                                                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" />
                                                            </svg>
                                                        </template>
                                                    </div>
                                                    <span class="ml-2" x-text="message.text"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <div class="mt-6">
                                    <button type="submit" 
                                            class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Save Stripe Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Partner Settings -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Partner Settings</h3>
                        <div class="mt-4 max-w-xl">
                            <form action="/admin/settings/update" method="POST">
                                <div class="mb-4">
                                    <label for="partner_welcome_message" class="block text-sm font-medium text-gray-700">Welcome Message</label>
                                    <textarea name="partner_welcome_message" id="partner_welcome_message" rows="4"
                                            class="mt-1 block w-full rounded-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 px-3"><?= htmlspecialchars($settings['partner_welcome_message'] ?? '') ?></textarea>
                                    <p class="mt-2 text-sm text-gray-500">This message will be displayed to partners on their dashboard.</p>
                                </div>

                                <div class="mt-6">
                                    <button type="submit" 
                                            class="inline-flex justify-center rounded-md bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                        Save Partner Settings
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documentation Sidebar -->
            <div class="md:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <!-- Quick Help -->
                    <div class="rounded-lg bg-white shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-900">Quick Help</h4>
                            <div class="mt-4 text-sm text-gray-500 space-y-3">
                                <p>Configure your main settings here including:</p>
                                <ul class="list-disc pl-5 space-y-2">
                                    <li>Admin profile and security</li>
                                    <li>Custom branding and logo</li>
                                    <li>Application general settings</li>
                                    <li>Stripe payment integration</li>
                                    <li>Partner portal configuration</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Stripe Integration Guide -->
                    <div class="rounded-lg bg-white shadow">
                        <div class="px-4 py-5 sm:p-6">
                            <h4 class="text-sm font-medium text-gray-900">Stripe Integration Guide</h4>
                            <div class="mt-2 text-sm text-gray-500 space-y-4">
                                <p>To set up Stripe integration:</p>
                                <ol class="list-decimal pl-4 space-y-2">
                                    <li>Go to your <a href="https://dashboard.stripe.com/apikeys" class="text-indigo-600 hover:text-indigo-500" target="_blank">Stripe Dashboard</a></li>
                                    <li>Copy your Secret Key</li>
                                    <li>Create a new webhook endpoint pointing to:
                                        <code class="block mt-1 p-2 bg-gray-50 rounded text-xs"><?= htmlspecialchars(rtrim($settings['app_url'] ?? 'https://'.$_SERVER['HTTP_HOST'], '/') . '/webhook/stripe') ?></code>
                                    </li>
                                    <li>Copy the webhook signing secret</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function resetBranding() {
    if (confirm('Are you sure you want to reset to Numok branding? This will remove your custom app name and logo.')) {
        // Create a form to submit the reset request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/settings/reset-branding';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>