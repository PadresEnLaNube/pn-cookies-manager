=== PN Cookies Manager ===
Contributors: felixmartinez, hamlet237
Donate link: https://padresenlanube.com/
Tags: cookie consent, gdpr, ccpa, privacy, cookie banner
Requires at least: 3.5
Tested up to: 6.9
Stable tag: 1.0.0
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

= Customizable Design =

* Full color control: background, text, accept button, reject button, and settings button colors.
* All colors applied via CSS custom properties for consistent theming.
* Admin preview button to see how the banner will look before publishing.

= Google Consent Mode v2 =

* Built-in Google Consent Mode v2 integration.
* Outputs default consent state in the page head before any Google tag scripts load.
* Automatically updates consent signals (ad_storage, ad_user_data, ad_personalization, analytics_storage, functionality_storage, personalization_storage) when visitors change their preferences.
* Works with Google Analytics 4, Google Ads, and Google Tag Manager.

= Privacy & Compliance =

* Configurable consent cookie duration (1-395 days) following GDPR/CNIL/EDPB guidelines (max 13 months recommended).
* Privacy Policy link option in the banner.
* Consent stored in a browser cookie (no server-side tracking).
* All banner texts are translatable by default; custom text overrides are also supported.

= Translations =

* Fully translation-ready with complete .pot file.
* All default texts (button labels, category names, descriptions) are translatable via standard WordPress i18n.
* Spanish (es_ES) translation included.
* Compatible with translation plugins like Loco Translate and Polylang.

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

In the Settings page, scroll down to the Cookie Registry section. Each category (Necessary, Functional, Analytics, Performance, Advertising) has its own expandable subsection where you can add cookie entries with name, duration, and description. Use the preset buttons to quickly add common cookies from services like Google Analytics or Facebook.

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

= 1.0.0 =

Hello world!
