(function() {
    const COOKIE_NAME = 'numok_tracking';
    const PROGRAM_ID = '<?= $program['id'] ?>';
    let config = null;

    class NumokTracker {
        constructor() {
            // Initialize tracker
            this.init();
        }

        async init() {
            try {
                // Get configuration from server
                config = await this.loadConfig();

                // Parse URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                
                // Check for tracking code
                if (urlParams.has('via')) {
                    // Store tracking data in cookie
                    const trackingData = {
                        tracking_code: urlParams.get('via'),
                        sid: urlParams.get('sid') || null,
                        sid2: urlParams.get('sid2') || null,
                        sid3: urlParams.get('sid3') || null,
                        referrer: document.referrer || null,
                        timestamp: new Date().toISOString()
                    };

                    // Save tracking data
                    await this.saveTrackingData(trackingData);

                    // Track click if enabled
                    if (config.track_clicks) {
                        await this.trackClick(trackingData);
                    }
                }
            } catch (error) {
                console.error('Numok tracker initialization error:', error);
            }
        }

        /**
         * Load configuration from server
         */
        async loadConfig() {
            const response = await fetch('/tracking/config/' + PROGRAM_ID);
            if (!response.ok) {
                throw new Error('Failed to load tracker configuration');
            }
            return await response.json();
        }

        /**
         * Save tracking data
         * @param {Object} data Tracking data to save
         */
        async saveTrackingData(data) {
            if (!config) return;

            // Prepare cookie data
            const cookieData = {
                program_id: PROGRAM_ID,
                tracking_code: data.code,
                ...data.sid && { sid: data.sid },
                ...data.sid2 && { sid2: data.sid2 },
                ...data.sid3 && { sid3: data.sid3 },
                referrer: data.referrer,
                landing_page: data.landing,
                timestamp: new Date().toISOString()
            };

            // Set cookie with configured expiration
            const expires = new Date();
            expires.setDate(expires.getDate() + config.cookie_days);
            document.cookie = `${COOKIE_NAME}=${JSON.stringify(cookieData)};expires=${expires.toUTCString()};path=/`;

            // Track click if enabled
            if (config.track_clicks) {
                await this.trackClick(cookieData);
            }
        }

        /**
         * Track click
         * @param {Object} data Click data
         */
        async trackClick(data) {
            try {
                const response = await fetch('/tracking/click', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                if (!response.ok) {
                    throw new Error('Failed to track click');
                }
            } catch (error) {
                console.error('Error tracking click:', error);
            }
        }

        /**
         * Track impression
         */
        async trackImpression() {
            try {
                const cookieData = this.getTrackingData();
                if (!cookieData?.tracking_code) return;

                const response = await fetch('/tracking/impression', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        program_id: PROGRAM_ID,
                        tracking_code: cookieData.tracking_code,
                        url: window.location.href
                    })
                });
                
                if (!response.ok) {
                    throw new Error('Failed to track impression');
                }

                // Set impression cookie to prevent duplicate tracking
                const expires = new Date();
                expires.setDate(expires.getDate() + 1); // 24 hour impression cookie
                document.cookie = `numok_impression_${cookieData.tracking_code}=1;expires=${expires.toUTCString()};path=/`;
            } catch (error) {
                console.error('Error tracking impression:', error);
            }
        }

        /**
         * Get Stripe metadata
         * @returns {Object} Metadata for Stripe
         */
        getStripeMetadata() {
            const data = this.getTrackingData();
            if (!data) return {};

            return {
                numok_tracking_code: data.tracking_code,
                ...data.sid && { numok_sid: data.sid },
                ...data.sid2 && { numok_sid2: data.sid2 },
                ...data.sid3 && { numok_sid3: data.sid3 }
            };
        }

        /**
         * Get tracking data from cookie
         * @returns {Object|null} Tracking data or null
         */
        getTrackingData() {
            const cookie = this.getCookie(COOKIE_NAME);
            if (!cookie) return null;

            try {
                return JSON.parse(cookie);
            } catch (error) {
                console.error('Error parsing tracking cookie:', error);
                return null;
            }
        }

        /**
         * Get cookie by name
         * @param {string} name Cookie name
         * @returns {string|null} Cookie value or null
         */
        getCookie(name) {
            const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            return match ? match[2] : null;
        }

        /**
         * Check if there's active tracking
         * @returns {boolean}
         */
        hasTracking() {
            return !!this.getTrackingData();
        }
    }

    // Initialize and expose to window
    window.numok = new NumokTracker();
})();