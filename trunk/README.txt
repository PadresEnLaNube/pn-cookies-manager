=== PN Cookies Manager ===
Contributors: felixmartinez, hamlet237
Donate link: https://padresenlanube.com/
Tags: cookie consent, gdpr, ccpa, privacy, cookie banner
Requires at least: 3.5
Tested up to: 7.0
Stable tag: 1.0.55
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Manage cookies on your website. Configure cookie consent banners, categorize cookies, and ensure compliance with privacy regulations.

== Description ==

PN Cookies Manager is a lightweight, privacy-focused WordPress plugin that helps your website comply with GDPR, CCPA, LGPD and ePrivacy Directive regulations.

Display a fully customizable cookie consent banner, register the cookies your site uses, and let visitors manage their preferences with granular category-based controls.

= Cookie Consent Banner =

* Fully customizable banner with three layout options: full-width bar, compact box, and floating card.
* Position the banner at the top, bottom, or center of the screen.
* Horizontal alignment control (left, center, right) for box and floating layouts.
* Three action buttons always visible: Accept All, Reject All, and Cookie Settings.
* Background overlay option to focus user attention on the banner.
* Configurable border radius for buttons and banner corners.
* Responsive design that adapts to mobile and tablet screens.
* Smooth open/close animations.
* Re-open floating button so visitors can change their preferences at any time.

= Cookie Preferences Panel =

* Slide-in settings panel with cookie categories: Necessary, Functional, Analytics, Performance, and Advertising.
* Toggle switches per category (Necessary is always on and cannot be disabled).
* Expandable sections showing the registered cookies for each category with name, duration, and description.
* Accept All and Save Preferences buttons within the panel.

= Cookie Registry =

* Register the cookies your site uses organized by category.
* Define cookie name, duration, and description for each entry.
* Quick-add preset buttons for common cookies: WordPress defaults, Google Analytics (GA4), Google Ads, Google Merchant, Facebook Pixel, and more.
* "Add all" button to insert all presets for a category at once.
* Case-insensitive duplicate detection prevents accidental cookie duplication.

= Cookie Scanner =

* Automated cookie discovery tool that scans your website pages.
* Select multiple pages to scan simultaneously (pages, posts, custom post types).
* Batch processing with progress indicator showing current page being scanned.
* Automatic categorization of discovered cookies (Necessary, Functional, Analytics, Performance, Advertising).
* One-click import of scanned cookies directly into the Cookie Registry.
* Scan history with ability to view, export (CSV), and delete previous scans.
* Smart import prevents duplicate entries when adding scanned cookies to registry.

= Interactive Tutorial =

* Step-by-step guided tutorial for first-time users.
* Contextual help highlighting key features and settings.
* Shows on first activation to help users get started quickly.
* Can be dismissed and won't show again once completed.

= Customizable Design =

* Full color control: background, text, accept button, reject button, and settings button colors.
* All colors applied via CSS custom properties for consistent theming.
* Admin preview button to see how the banner will look before publishing.

= Preventive Script & Iframe Blocking =

* Block third-party scripts and iframes from loading before the user gives consent.
* Configurable URL patterns per category (analytics, advertising, performance, functional) with sensible defaults.
* Re-enable scripts and restore iframes automatically after consent is granted.
* MutationObserver intercepts dynamically injected scripts that match blocking patterns.
* Automatic deletion of known tracking cookies (GA, Facebook, HotJar, Clarity, etc.) when a user revokes consent.

= Google Consent Mode v2 =

* Built-in Google Consent Mode v2 integration with Basic and Advanced mode selector.
* Advanced mode: tags load with denied state and update on consent (default). Basic mode: tags are completely blocked until consent.
* Regional consent defaults based on user geolocation (Cloudflare, Vercel, GeoIP headers) with EU/EEA country list.
* Three regional modes: Strict (all denied), Mixed (EU denied, rest granted), Lenient (all granted).
* Outputs default consent state in the page head before any Google tag scripts load.
* Automatically updates consent signals (ad_storage, ad_user_data, ad_personalization, analytics_storage, functionality_storage, personalization_storage) when visitors change their preferences.
* Works with Google Analytics 4, Google Ads, and Google Tag Manager.

= Global Privacy Control (GPC) =

* Detect the browser GPC signal (navigator.globalPrivacyControl) and automatically apply opt-out consent.
* When GPC is active on a new visitor, analytics and advertising are denied without showing the banner.
* Respects explicit user choices: if a consent cookie already exists, GPC does not override it.
* Configurable toggle in Privacy & Compliance settings.

= WP Consent API Integration =

* Automatically syncs consent state with the WP Consent API plugin when active.
* Maps plugin categories to WP Consent API types (functional→preferences, analytics→statistics, performance→statistics-anonymous, advertising→marketing).

= Individual Consent Proof Logging =

* Privacy-safe individual consent records for compliance proof with hashed IP and user agent.
* Each record includes consent hash, policy version, categories, consent type, region, and GPC signal.
* Policy version hash based on banner message and total registered cookie count.
* Export consent log to CSV from the Analytics admin page.
* Configurable retention period with purge function (default 730 days).

= Privacy & Compliance =

* Configurable consent cookie duration (1-395 days) following GDPR/CNIL/EDPB guidelines (max 13 months recommended).
* Secure flag on consent cookie when site is served over HTTPS.
* Privacy Policy link option in the banner.
* Consent stored in a browser cookie (no server-side tracking).
* SSRF protection in the cookie scanner: URL validation, private IP rejection, SSL verification, and redirect blocking.
* All banner texts are translatable by default; custom text overrides are also supported.

= Accessibility =

* Full ARIA support: aria-labelledby, aria-describedby, aria-expanded, aria-controls, and role attributes on all interactive elements.
* Focus management: saves and restores focus when opening/closing banner and settings panel.
* Focus trap in the settings panel to prevent tabbing outside the dialog.
* Keyboard navigation: Enter/Space to toggle category sections.
* :focus-visible outline styles on all interactive elements.
* Respects prefers-reduced-motion to disable animations.
* Respects prefers-contrast for enhanced high contrast mode.

= Translations =

* Fully translation-ready with complete .pot file.
* All default texts (button labels, category names, descriptions) are translatable via standard WordPress i18n.
* Spanish (es_ES) translation included.
* Compatible with translation plugins like Loco Translate and Polylang.

= Quick Setup & Configuration Overview =

* One-click auto-configure button applies recommended defaults: cookie presets, script blocking patterns, GCM advanced mode, GPC, strict regional defaults, and consent analytics.
* Configuration summary blocks at the top of the settings page show live status of Cookie Registry, Script Blocking, and Privacy & Compliance with expandable details and green/red status indicators.

= Cookie Policy Generator =

* Generate a cookie policy page automatically from the cookies registered in the plugin.
* Outputs both Gutenberg block markup and raw HTML ready to paste into any WordPress page.
* Available as a submenu page under Cookies Manager.

= Lightweight & Developer Friendly =

* No external dependencies for the front-end banner (jQuery only).
* Deferred script loading for minimal performance impact.
* Clean CSS class naming convention (pn-cookies-manager-*) for easy custom styling.
* Hooks and filters for developer extensibility.


== Installation ==

1. Upload the `pn-cookies-manager` folder to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Cookies Manager > Settings to configure your cookie banner, register your cookies, and customize the design.

== Frequently Asked Questions ==

= How do I configure the cookie banner? =

After activating the plugin, go to Cookies Manager > Settings. You will find sections for Banner Design (layout, position, alignment), Texts (title, message, button labels), and Colors. Use the "Preview Banner" button to see a live preview of your configuration.

= How do I register the cookies my site uses? =

In the Settings page, scroll down to the Cookie Registry section. Each category (Necessary, Functional, Analytics, Performance, Advertising) has its own expandable subsection where you can add cookie entries with name, duration, and description. Use the preset buttons to quickly add common cookies from services like Google Analytics or Facebook. Alternatively, use the Cookie Scanner to automatically detect cookies on your site and import them with one click.

= How does the Cookie Scanner work? =

The Cookie Scanner automatically detects cookies used on your website. Go to Cookies Manager > Settings > Cookie Scanner, select the pages you want to scan, and click "Start Scan". The scanner will analyze each page and categorize any cookies it finds. Once the scan is complete, you can review the results and click "Import to Registry" to add them to your cookie registry. The smart merge system ensures previously registered cookies won't be deleted or duplicated.

= What is Google Consent Mode v2? =

Google Consent Mode v2 is a framework that allows your Google tags to adjust their behavior based on the consent status of your visitors. When enabled in the plugin settings, the banner will automatically communicate consent choices to Google services, ensuring compliant data collection.

= Can I customize the banner texts? =

Yes. In the Texts subsection of the Cookie Banner settings, you can override any default text. If you leave a field empty, the plugin will use the default translatable text. If you enter a custom value, it will be displayed as-is and will not be translated.

= How long is the consent cookie stored? =

By default, the consent cookie is stored for 182 days (6 months). You can configure this in the Design subsection (1-395 days). The GDPR via CNIL/EDPB guidelines recommends a maximum of 13 months (~395 days).

= Is the plugin compatible with any WordPress theme? =

Yes, the plugin is designed to work with any WordPress theme. The banner uses fixed positioning and its own isolated styles, so it will not conflict with your theme's design.

= Is the plugin translation-ready? =

Yes. All default texts are fully translatable using standard WordPress i18n functions. You can use translation plugins like Loco Translate to translate the plugin into your language. Spanish (es_ES) translation is included.

= How do I uninstall the plugin? =

Go to the Plugins screen, deactivate the plugin, then click Delete. If you enabled the "Remove data on deactivation" option in Settings > System, all plugin options will be cleaned up automatically on deactivation.

= How do I get support? =

For support, visit the plugin's support forum on WordPress.org or contact us at info@padresenlanube.com.


== Changelog ==

= 1.0.55 =

* Add configuration summary blocks (Cookie Registry, Script Blocking, Privacy & Compliance) at the top of the settings page with expandable details, status dots, and live option values
* Add one-click auto-configure button that applies recommended defaults: populates all cookie presets, enables script blocking with default patterns, sets GCM to advanced mode, enables GPC, strict regional defaults, and consent analytics
* Add auto-configure AJAX handler with capability check, preset population, privacy policy auto-detection, and auto-configured flag
* Add Cookie Policy Generator submenu page that builds cookie policy content from registered cookies with Gutenberg block markup and raw HTML output
* Add Run Tests card with icon, description, and transparent button matching the auto-configure card style
* Replace cookie registry merge-on-save with direct replacement so that deleted cookies are actually removed instead of being re-added from the database
* Add cleanup for categories where all cookies were removed to explicitly clear the stored arrays
* Add is_string() guards in the cookie registry save handler to prevent non-string values from being stored in cookie arrays
* Add is_string() guards in the frontend banner template to skip non-string cookie values instead of rendering "Array" text
* Fix cookie scanner progress message showing literal "%actual%" instead of the current page number in Spanish, Catalan, and Galician translations
* Reduce spacing between the form sections and the Run Tests card

= 1.0.50 =

* Add Secure flag to the consent cookie when site is served over HTTPS
* Add SSRF protection to the cookie scanner: validate URLs against site host, reject private/reserved IP ranges, disable redirect following, and enable SSL verification
* Propagate scanner errors to the admin UI with differentiated error messages per URL
* Add preventive script and iframe blocking before user consent via new Script Blocker class
* Add admin settings section to configure blocking patterns per category with sensible defaults
* Add toggle to enable/disable script blocking globally
* Re-enable blocked scripts and restore iframe sources client-side when the user grants consent
* Add MutationObserver to intercept and block dynamically injected scripts that match blocking patterns
* Delete known tracking cookies client-side when a user revokes consent for a category
* Add Google Consent Mode v2 Basic/Advanced mode selector
* Add regional consent defaults based on user geolocation with EU/EEA country list
* Add regional defaults mode selector: Strict, Mixed, and Lenient
* Add geolocation fallback setting for when user region cannot be detected
* Add Global Privacy Control (GPC) support with automatic opt-out consent
* Add GPC toggle in Privacy & Compliance settings section
* Add WP Consent API integration with category mapping and consent sync
* Add individual consent proof logging table with hashed IP, user agent, consent hash, policy version, categories, consent type, region, and GPC signal
* Add policy version hash based on banner message content and registered cookie count
* Add consent log CSV export from the Analytics admin page
* Add consent log purge with configurable retention period from the Analytics admin page
* Add ARIA attributes to the cookie banner for screen reader support
* Add focus management and focus trap in the settings panel
* Add keyboard accessibility for category headers
* Add :focus-visible, prefers-reduced-motion, and prefers-contrast CSS support
* Add hideable settings banner image with close button and AJAX persistence
* Add Script Blocking and Privacy & Compliance settings sections
* Add Tests submenu page under Cookies Manager for administrators
* Add standalone test runner with tests for scanner security, consent cookies, GCM signals, and consent logging
* Add file upload input type, section labels, and colored section headers in the forms builder
* Fix default value logic for options to only apply defaults when get_option returns false
* Fix unsaved changes warning appearing on page load due to browser normalizing color input values
* Fix tutorial completion handler to pass boolean directly

= 1.0.46 =

* New: Added Cookie Scanner section to detect cookies used on your website
* New: Scan multiple pages to identify cookies from videos, forms, and external tools
* New: Detailed cookie information with name, provider/domain, duration, category, and purpose
* New: Export scan results to CSV format
* New: Scan history with date, time, and cookies found per scan
* Improved: Reorganized settings structure - Design and User Roles are now subsections within System
* Enhanced: Cookie detection for YouTube, Vimeo, Google Maps, HubSpot, Mailchimp, and more
* Enhanced: Automatic categorization of detected cookies based on known patterns
* Enhanced: Database table for storing scan history and results

= 1.0.0 =

Hello world!
