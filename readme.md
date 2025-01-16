# numok: Open Source Affiliate Program Platform

An open source affiliate program that connects to Stripe to track payments.

## Technical Architecture

We'll use plain PHP for the backend, MySQL for the database. We should aim for this to be as compatible as possible.

For the presentation layer, we'll use plain HTML, JS and CSS. We can use TailwindUI.

Regarding style, I'd like to follow a style like Github.com.

### Tracking Flow

1. Affiliate Link Structure
   - Base URL + `?via=CODE&sid=value1&sid1=value2...`
   - Sub IDs (sid through sid5) are optional parameters

2. Cookie Storage
   - JS tracker captures all URL parameters
   - Stores in cookie with configured expiration
   - No database storage of sub IDs

3. Stripe Integration
   - All URL parameters sent as metadata during checkout
   - Webhook endpoint receives payment notification
   - Attribution matches via `via` parameter only
   - Sub IDs retrieved from Stripe metadata for reporting
   - Store our tracking code with a numok_ prefix to avoid data collission

4. Postback System
   - Sends conversion data to affiliate's postback URL
   - Includes all original sub IDs from Stripe metadata
   - No database queries needed for sub ID data

### Entities

Keep it as minimal as possible to begin with: 

- Users: Admins or regular users running the affiliate program. Admins will be able to modify Stripe settings and create new users. Users will just be able to operate the partners, programs, conversions and payments.
- Partners (Affiliates): Users who sign up to apply to run the programs
- Programs: The definition of the reward for bringing paying customers, landing page, cookie duration for attribution, percentage or fixed amount, is it recurring or one time, number of days to reward since first payment
- Conversions: When a payment is registered in Stripe, it appears here with a status (pending, payable, rejected, paid), we should also store the payment information and the comision reward
- Settings: All the system wide settings, like Stripe Secret Key, payment terms, domain (if needed), etc
- Clicks: Optional, if the system is enabled to track clicks, we will store them here, and send the click ID to Stripe
- Logs: If logging is enabled, it will be usefull to have a detailed log of all the actions that happened without user intervention (conversion tracking, webhooks, postbacks triggered, etc)

Partners Programs is a many to many relation, a partner can create many tracking codes for each program. We should store the postback URL in this relation.

### Installation Process

#### Self-hosted Version
1. Download Release Package
2. Upload to Web Server
3. Create MySQL Database
4. Run Web-based Installer
   - Database configuration
   - Admin account creation
   - Stripe API setup
5. Complete Configuration
   - Set up first program
   - Configure commission rules
   - Generate tracking code

#### Hosted Service (numok.com)
- Individual instance per customer
- Automated instance provisioning
- Custom domain support
- Managed updates and maintenance

### Integration Flows

#### 1. Stripe Integration
- Enter Stripe's Secret key
- Webhook endpoint for real-time payment notifications
- Commission calculation service
- Payment status tracking
- Automated affiliate payments
- Metadata storage for tracking parameters

```

### Security Considerations

1. **Data Protection**
   - Encrypted storage for sensitive data
   - Regular security audits
   - GDPR compliance measures

2. **Authentication**
   - JWT-based API authentication
   - CSRF protection
   - Rate limiting

3. **Affiliate Verification**
   - Manual review process
   - Fraud detection
   - Payment verification

## Development Guidelines

### Code Style
- PSR-12 for PHP
- ESLint with recommended rules for JavaScript
- Prettier for formatting
- Git commit conventions

### Testing
- PHPUnit for backend testing
- Jest for JavaScript testing
- E2E testing with Cypress
- CI/CD pipeline with GitHub Actions

### Documentation
- OpenAPI/Swagger for API documentation
- PHPDoc for PHP documentation
- JSDoc for JavaScript documentation
- Markdown for user documentation

## Roadmap

### Phase 1: Core Features
- [x] Admin dashboard
- [x] Affiliate portal
- [x] Stripe integration
- [ ] JavaScript tracker
- [ ] Basic installation flow

### Phase 2: Advanced Features
- [ ] Postback system
- [ ] Advanced analytics

### Phase 3: Hosted Service
- [ ] Instance provisioning system
- [ ] Custom domain management
- [ ] Billing system
- [ ] LEV3R syndication (To be defined)