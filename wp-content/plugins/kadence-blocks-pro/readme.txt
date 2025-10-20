=== Kadence Blocks Pro – Gutenberg Page Builder Toolkit ===
Contributors: britner, oakesjosh, woodardmc
Tags: gutenberg, blocks, page builder, google fonts, dual buttons, svg icons, editor width,
Requires at least: 6.6
Tested up to: 6.8.2
Stable tag: 2.8.3
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom Blocks for Gutenberg to help extend the editing capabilities.

== Description ==

This plugin adds custom blocks to extend Gutenberg's editing capabilities. This is a premium add on to Kadence Blocks.

== Installation ==

Install the plugin into the `/wp-content/plugins/` folder, and activate it. This plugin is an extension of Kadence Blocks, you will be asked to install it if you do not have it installed already.

== Frequently Asked Questions ==

= Where do I report security bugs found in this plugin? =
Please report security bugs found in the source code of the
Kadence Blocks Pro plugin through the Patchstack
Vulnerability Disclosure Program https://patchstack.com/database/vdp/kadence-blocks-pro The
Patchstack team will assist you with verification, CVE assignment, and
notify the developers of this plugin.

== Security Policy ==

= Reporting Security Bugs =

Please report security bugs found in the
Kadence Blocks Pro plugin's source code through the
Patchstack Vulnerability Disclosure
Program https://patchstack.com/database/vdp/kadence-blocks-pro. The Patchstack team will
assist you with verification, CVE assignment, and notify the
developers of this plugin.

== Changelog ==

= 2.8.3 =
Release Date: 9th October 2025
* Add: Filter for query filter posts per page limit.
* Fix: Issue with "Exclude Current" option on post grid when multiple post grids are on the page.
* Fix: Post grid "read more" align bottom setting when "show filter" is enabled.
* Fix: Sliders aria labels are now translatable.

= 2.8.2 =
Release Date: 25th September 2025
* Fix: Slider fixed size minimum height.
* Fix: Query card border-radius units.
* Update: Default image overlay color for better contrast.
* Add: Tab index added to image overlay title and subtitle.
* Update: Image overlay subtitle now defaults as a paragraph.

= 2.8.1 =
Release Date: 18th September 2025
* Add: Multiple conditions for conditional display
* Add: URL based conditions for conditional display: full url, url path, and query parameter
* Add: Conditional display support for individual slides in Sider block.
* Add: Filter 'kadence_blocks_pro_query_block_split_character' to change the default split character for the Advanced Query Block.
* Fix: Post type filter in advanced query loop displaying the slug instead of the label.

= 2.8.0 =
Release Date: 11th September 2025
* Add: Ajax search functionality for the advanced query loop block.
* Fix: Improvements to advanced query loop filter counts logic.
* Fix: Improvements to filters when inheriting a query in advanced query loop.
* Fix: Product carousel template in the editor not matching the frontend.

= 2.7.19 =
Release Date: 28th August 2025
* Add: Contains and not contains options for conditional display.

= 2.7.18 =
Release Date: 19th August 2025
* Fix: Block defaults not applying to new blocks.

= 2.7.17 =
Release Date: 14th August 2025
* Add: Aria label support for the advanced slider block.
* Add: Pause button option for sliders with autoplay enabled.
* Add: Submission error summaries for the advanced form block to improve accessability.
* Update: Carousel arrows contrast ratios.
* Update: Improve keyboard focus when tabbing through sliders.
* Update: Slides in sliders are now semantic list items.
* Fix: Aria labels for some sliders not translatable.
* Fix: Advanced Header with sticky enabled would sometimes stick to wrong position.
* Fix: Adv Image alt text not showing in Repeater block.
* Fix: Conditional Display based on Role does not work when user has multiple role.
* Fix: Adv Gallery Mosaic type - Custom link issue.
* Fix: Wootemplate Gallery is showing blank when Mosaic layout is selected.

= 2.7.16 =
Release Date: 23rd July 2025
* Fix: Conditional display for user roles.
* Fix: Modal button fixes for WooCommerce shop page
* Fix: Save as default issue.

= 2.7.15 =
Release Date: 16th July 2025
* Fix: Error when editing Repeater Block.

= 2.7.14 =
Release Date: 15th July 2025
* Fix: Issue editing Kadence blocks in widget areas

= 2.7.13 =
Release Date: 15th July 2025
* Add: Custom arrow and dot styles for the advanced slide block.
* Add: Video posters for the Video Popup Block
* Add: Video aspect ratios for Video Popup Block
* Add: Mobile video option for Video Popup Block
* Fix: Error when adding site to multisite install.

= 2.7.12 = 
Release Date: 2nd July 2025
* Add: Post Carousel arrow and dot settings for custom color, size, border, position.
* Fix: Ability for randomly sorted query loops to be paginated.

= 2.7.11 =
Release Date: 19th June 2025
* Add: Year, Month, and Day options to dynamic content.
* Fix: Improve browser recognition when saving adv form submissions.

= 2.7.10 =
Release Date: 12th June 2025
* Fix: Issue with adv query post ordering on custom post types for inherited queries. 

= 2.7.9 =
Release Date: 5th June 2025
* Add: Custom styles for carousel arrows and dots.
* Fix: Issue selecting specific event posts in Query loop block.
* Fix: Advanced Query Loop filters when inheriting query.
* Fix: Dynamic HTML block not outputting oEembed inside Repeater block.
* Fix: Exporting form submissions when two fields have the same name.

= 2.7.8 =
Release Date: 29th May 2025
* Add: Conditional Display options for current user
* Add: Option to respect image aspect ratio in postgrid block when using full height option.
* Fix: Post Grid/Carousel border styles on save.
* Fix: PHP 8.4 deprecation warnings.
* Fix: Issue where swatches could render the incorrect value.
* Update: Updated device detection for form database entries.

= 2.7.7 =
Release Date: 22nd May 2025
* Fix: Indexing of MetaBox custom fields in Advanced Query Loop.

= 2.7.6 =
Release Date: 15th May 2025
* Fix: Post Grid editor responsive preview line height for title and excerpt.
* Fix: Modal block added via Hooked Element in the Advanced Query Loop not functioning properly.
* Add: Support for dynamic row video background urls.
* Add: Image border radius to Post Grid/Carousel block.

= 2.7.5 =
Release Date: 1st May 2025
* Add: Mosaic layout to Gallery Block
* Update: Connect post category colors with relevant blocks (Posts, Post Grid)
* Fix: Transparent Logo Reverts to Default Logo After Scrolling Back Up with Sticky Header Enabled
* Fix: Product Carousel > Do Not Loop setting not working.
* Fix: Custom Icon SVGs not showing on the Backend when using the Espanol Site Language
* Fix: Comma separated CC field not sending to multiple users in form block

= 2.7.4 =
Release Date: 23rd April 2025
* Fix: Issue with Adv Query Block Swatches Filter.

= 2.7.3 =
Release Date: 17th April 2025
* Fix: Page scripts missing from WooTemplates in editor.
* Fix: Dropdowns showing empty results after filtering in Query Loop. 
* Fix: Form entries filter resetting when paginating.

= 2.7.2 =
Release Date: 3rd April 2025
* Add: Order by custom field value in Advanced Query.
* Fix: Filtering when using an ACF checkbox or multiselect in advanced query.
* Fix: Keep focus on checkbox filter after selection in advanced query.
* Fix: Improvements when filtering by WPML translated value in advanced query.
* Fix: Add accessibility labels for post and products carousel

= 2.7.1 =
Release Date: 19th March 2025
* Fix: Increase timeout when searching for SVG icons.

= 2.7.0 =
Release Date: 18th March 2025
* Add: Ability to search for and add new svg icons in the icon block.
* Update: Post and product carousel loading shift improvements.
* Fix: Product Swatch not working correctly on some products in the Product Carousel block.
* Fix: Issue with Adv Query Loop Post Meta Filtering When Value Contains a Space.
* Fix Query Loop(Adv) margin/padding only applying in PX.

= 2.6.5 =
Release Date: 11th March 2025
* Fix: Query loop filtering issue with buttons and dropdown.
* Fix: Query loop date filter when using the default filter source.

= 2.6.4 =
Release Date: 25th February 2025
* Add: Option to the product carousel block to not loop the carousel. This has better support for swatches.
* Fix: Hidden products showing in Query Loop when top level taxonomy filter is set.
* Fix: Various text domain/translation fixes.
* Fix: Indexing custom fields in query loop.
* Fix: Stock status of items on backorder in query loop filters.

= 2.6.3 =
Release Date: 11th February 2025
* Fix: Advanced query result count for CPT
* Fix: Advanced query loop order by having to be set twice.
* Fix: Advanced query loop filter checkbox result counts.

= 2.6.2 =
Release Date: 30th January 2025
* Fix: Retain order of specifically selected posts in Adv Query Loop.
* Fix: Post grid translation of "Read More" text.
* Fix: Setting numerical padding & margin on query loop block.
* Fix: Issue selecting query loop checkbox filter when in hierarchical view.
* Fix: Query loop indexer performance on deletes and other bulk actions.
* Fix: Query loop range filter on rtl sites.
* Fix: Dynamic HTML block always having alignment-left

= 2.6.1 =
Release Date: 7th January 2025
* Update: Adjustments around Video Popup block registration.
* Update: Video Popup block animations.
* Fix: WPML translations for Query Cards
* Fix: Critical error related to Post Grid/Carousel block
* Fix: Query loop posts now order properly for RTL
* Fix: Query Loop(Adv) loses filters when accessed directly from URL of search with no results
* Fix: Advanced Slider gradient overlay
* Fix: Brevo API for advanced form
* Fix: Query Loop issue in the Editor when using Video Popup Blocks
* Fix: Modal block inside Advanced query loop with "Load Content After Footer"
* Fix: Aria-label translations for sliders
* Fix: Issue with Adv Button Dynamic Link in Repeater block FSE templates
* Fix: Navigation Link displaying dynamic control when not applicable


= 2.6.0 | 5th December 2024 =
* Add: Pro features for table block
* Fix: Modal block variable font sizes not applying.
* Fix: Query loop column layout was incorrect with product catalog customizer settings applied.
* Fix: Query loop pagination with user restricted content.
* Fix: Query loop issue with selecting cards when only one was available.
* Fix: Repeater context not working in FSE templates.
* Fix: Dynamic content "after content" when value starts with a space.
* Fix: Advanced Gallery thumbnail slider when used to replace archive loop.

= 2.5.5 | 19th November 2024 =
* Add: Background filter options to section block.
* Add: Background filter options to header block.
* Add: Ability to import and export query loops & cards.

== 2.5.4 | 12th November 2024 ==
* Add: Option to show advanced slider navigation arrows on hover.
* Fix: Query loop pagination color preview not accurate in editor.
* Fix: Query loop max width units not applying on front end.
* Fix: Custom anchor not applying to some query loop blocks.
* Fix: Improvements to query loop max value.
* Fix: Border radius units on some query blocks.
* Fix: Repeater blocks not working in FSE theme template.

== 2.5.3 | 29th October 2024 ==
* Enhancement: Allow advanced query filters inside modal block.
* Enhancement: Open Modal blocks with an anchor link to their open id on page load.
* Enhancement: Add a border controls to advanced slider slides.
* Enhancement: Option to set inner slide height to 100% in adv slider.
* Fix: Query loop filters working with pagination.
* Fix: Adv. slider text color too specific on headings.
* Fix: Conditional form fields not working in full site editing themes.
* Fix: Compatibility of product carousel with Shoptimizer theme.
* Fix: Support for image url field source in dynamic image content.

== 2.5.2 | 15th October 2024 ==
* Fix: Issue with inline dynamic content.

== 2.5.1 | 15th October 2024 ==
* Fix: Query loop filter for Post Meta -> Author.
* Fix: Query loop pagination to top of block.
* Fix: Filtering custom meta fields in some situation.
* Fix: Issue when display metabox custom user fields in dynamic content
* Fix: Issue with dynamic content before/after content with spaces.
* Enhancement: Added a selection of current user fields (including custom fields) to dynamic content for text blocks and other areas dynamic text is allowed.

== 2.5.0 | 3rd October 2024 ==
* New: Pro Header Adv features.
* New: Pro Navigation Mega menus.
* New: Pro Navigation icons.
* New: Pro Navigation highlight links.
* Fix: Query loop in stock / out of stock text not translating.

== 2.4.7 | 23rd September 2024 ==
* Add: Support for custom meta field filters in Advanced Query Loop.
* Fix: Prefix SVG sanitization library to avoid conflicts.
* Fix: Increase security of dynamically generated CSS.
* Fix: Setting numeric values for padding & margin in Query Loop and Card.
* Fix: Query loop result count defaulting to 0 on hierarchical results.

== 2.4.6 | 22nd August 2024 ==
* Fix: Search term not showing in query loop results.
* Fix: Issue with setting numeric and size based padding & margin.
* Fix: Issue with shortcode context in query loop.

== 2.4.5 | 8th August 2024 ==
* Add: Modal aria label option for the modal block.
* Fix: Issue with dynamic button links in Advanced Query loop.

== 2.4.4 | 8th August 2024 ==
* Enhancement: Improved performance of dynamic content in the Advanced Query Loop and Repeater blocks.
* Fix: Repeater blocks not correctly updating their content when source changed.
* Fix: Inability to edit a repeater block in query loop if first post had no repeater content.
* Fix: Brevo contacts not updating contact details when resubmitting form.
* Fix: Filter counts for Advanced Query loop when used on archive pages.
* Fix: Issue when uploading custom SVGs in some environments.
* Fix: Better handling when query loop index table is missing or deleted.
* Fix: Query loop filter button CSS specificity.
* Fix: Advanced slider CSS tweak and fix when used with image overlay.
* Fix: Allow users to set custom filter limit in Advanced Query Loop using a WordPress filter.
* Fix: Carousel column gap in editor when set to 0.

== 2.4.3 | 17th July 2024 ==
* Fix: Post grid title line height.

== 2.4.2 | 16th July 2024 ==
* Fix: Advanced Slider dotstyle not showing in editor.
* Fix: Custom anchor tags on modals not displaying on front end.
* Fix: Unitless line height on post grid title.
* Fix: Query card selection dropdown overlapping in editor.
* Fix: Query loop language incorrect after filtering when using polylang.
* Fix: Adv Form Mailchimp submissions were not being saved if fields were previously mapped but are no longer.

== 2.4.1 | 9th July 2024 ==
* Fix: Some Query Loops not showing on sites without WooCommerce.

== 2.4.0 | 8th July 2024 ==
* Add: Ability to upload custom SVG icons to Kadence icon picker.
* Add: Carousel Option to Advanced Slider
* Add: New slider arrow style options
* Add: Option to show slider overflow.
* Add: Portfolio Grid option to only show posts by author.
* Fix: Hidden Woo products being shown in advanced query loop results.
* Fix: Filtering by in-stock status in advanced query loop.
* Fix: Image overlay breaking when color pallet is deleted.
* Fix: Checkbox ACF field in repeater block.
* Fix: Box shadow on filter buttons in Adv query loop.
* Fix: Repeater block not detecting Meta Box groups.
* Fix: Query cards now save their preview post type.
* Fix: Shortcodes in query cards when filtered.
* Fix: split content block with image set to cover in loop items.

== 2.3.11 | 18th June 2024 ==
* Add: Post grid toggle to only show posts by author.
* Add: Post grid option to force image to height of container.
* Add: Post grid align options for content and read more text.
* Fix: Inheriting pagination in query loop on archive pages.
* Fix: Query loop input padding.
* Fix: Query loop filter buttons border.
* Fix: Query loop search filter padding in editor.
* Fix: Repeater block compatibility in image overlay, modal, post grid, split content, and video popup.
* Fix: Line height in dynamic html block.
* Fix: Post save issue on some very long posts with lots of dynamic content.
* Fix: Field border radius not saving in query loop fields.

== 2.3.10 | 3rd June 2024 ==
* Fix: Filtering by woo attributes in Adv query loop. Reindex your posts at Kadence -> Queries -> Force Reindex.
* Fix: Filtering by woo meta in Adv query loop.
* Fix: Filtering by post date in adv query loop.
* Fix: Button filter border radius unit.
* Fix: Custom CSS classes not applying to filters in query loop.

== 2.3.9 | 14th May 2024 ==
* Fix: Dynamic HTML block not rendering correctly if saved in 2.3.8.
* Fix: Display of gradient picker when using color palette.

== 2.3.8 | 8th May 2024 ==
* Add: Ability to use fields inside ACF group fields as dynamic content.
* Fix: Query loop filters based on post types displaying the slug instead of label.
* Fix: Issue when filtering query loop on Woocommerce attributes.
* Fix: Restrict some site settings from being used in dynamic content.
* Fix: Restrict users without manage_options access from creating dynamic content with options.

== 2.3.7 | 23rd April 2024 ==
* Add: Link to view full changelog on Kadence website.
* Fix: Dynamic HTML typography not applying if repeater field is used.
* Fix: Product carousel in mega menu.
* Fix: Portfolio Grid/Carousel not showing one less post when used in element.
* Fix: Repeater block styles applying to unexpected elements.
* Fix: Swatch filter when the term being used was the default and not specifically selected.
* Fix: Query Loop filter result counts when children are sorted hierarchically.

== 2.3.6 | 9th April 2024 ==
* Add: Dynamic content for The Events Calendar.
* Fix: Possible css issue in FSE templates.
* Fix: Issue with theme colors not working when changing opacity.

== 2.3.5 | 3rd April 2024 ==
* Fix: Editing the repeater block in WordPress 6.5.

== 2.3.4 | 2nd April 2024 ==
* Add: Option to show author website link in post grid.
* Fix: Filter results for advanced query loop range filter.
* Fix: Issue where "Show children" option would show in query loop editor when it shouldn't.
* Fix: Image path for ajax loading animation.

== 2.3.3 | 19th March 2024 ==
* Update: Block API version.
* Update: improved js and css styling.
* Fix: An issue when using custom meta key in Dynamic Content.
* Fix: An issue with counts in the advanced query block.
* Fix: An issue with templated field values in the auto response email in empty.
* Fix: An issue with labels with dropdown fields for the advanced query block.

== 2.3.2 | 6th March 2024 ==
* Fix: Title & subtitle margins on image overlay block.
* Fix: Selecting Kadence Elements as card in query loop.
* Fix: Prevent lazy load class not applying to dynamic advanced images when setting was enabled.
* Fix: Support page (pg) url parameter in query loop when using infinite scroll.
* Fix: Not all posts type showing in advanced query filters when inheriting query.
* Fix: Error when adding advanced query as a widget.
* Fix: Modal block when used in query loop cards.
* Fix: Issue where selecting a new field from the repeater sidebar would show no content.

== 2.3.1 | 12th February 2024 ==
* Fix: Error in editor when modifying conditional form settings.
* Fix: Error in editor when modifying styles on query loop button filter.
* Fix: Result count when using infinite scroll.
* Fix: Infinite scroll output when using filters.
* Fix: Button filter typography settings not applying
* Fix: Excluded/included terms in query loop showing on front end.

== 2.3.0 | 6th February 2024 ==

* Add: Ability to use Kadence Elements as card in query loop.
* Add: Ability to use Woo Templates as card in query loop.
* Add: Infinite scroll to query loop.
* Add: Range filter to query loop.
* Add: Swatch filter to query loop.
* Add: Rating filter to query loop.
* Add: Related posts option to query loop.
* Add: Ability to select individual posts in query loop.
* Add: Ability to adjust term order in query loop.
* Add: Ability to use icons in query loop filter labels.
* Add: Toggle for sticky posts in query loop.
* Add: Ability to limit text length in Dynamic HTML block.
* Add: Ability to strip HTML in Dynamic HTML block.
* Fix: query loop gap units not applying properly.
* Fix: Image overlay block inside of query loop on non-Kadence themes.
* Fix: Glightbox min.js file not being minified.
* Fix: query loop block inside of mega menus.
* Fix: Other various query loop bugs.

== 2.2.5 | 5th February 2024 ==
* Add: New Form Analytics option.
* Add: New option to make sections conditional in forms.
* Add: Get Response as an action option in the Advanced Form.
* Fix: Improve dynamic html & list support in repeater block.
* Fix: Incorrect column count in Query Loop block on product catalog page.
* Fix: Image overlay border-radius not applying to the image.
* Fix: Video popup gradient is not applied to overlay image.
* Fix: Issue with PHP 8.2 and dynamic property warning.
* Fix: Issue with sticky posts affecting editor post selection.
* Fix: License link conditions.

== 2.2.4 | 9th January 2024 ==
* Fix: Undefined issue on settings page.

== 2.2.3 | 8th January 2024 ==
* Fix: Fix hard coded table name prefix.
* Fix: Query loop API endpoint when site is in subfolder
* Fix: Query loop API endpoint when not using pretty permalinks
* Fix: Issue with RTL Carousels
* Fix: Potential conflict with plugins that modify default post content
* Fix: Advanced Query Loop labels not hiding when set to hidden
* Fix: Icon based pagination links not clickable
* Fix: Row & Column gap units not applying on Advanced Query Loop cards
* Fix: Fix PHP undefined notice issue.

== 2.2.2 | 3rd January 2024 ==
* Add: Image Size option to split content block.
* Update: Repeater Block to work in FSE template.
* Update: Alt text input in split content and image overlay block.
* Update: Obfuscate key input.
* Fix: Box shadow display on front end for some query loop inputs
* Fix: Inaccurate advanced query loop results when using more than 2 filters
* Fix: Styling issue with checkbox and button filters.
* Fix: Issue with pagination stylesheet.
* Fix: Issue with alt text in some dynamic images.
* Fix: Issue with modal styling in query loop.
* Fix: Issue with slider padding.
* Fix: Issue with portfolio block font size css.
* Fix: Issue with widgets page notice.

== 2.2.1 | 8th December 2023 ==
* Fix: Issue with repeater and metabox.
* Fix: RTL carousel css.
* Fix: Multisite licensing issue.
* Fix: Possible php notice with $group undefined.
* Fix: Issue with image overlay css specificity.
* Fix: Issue with border radius in portfolio grid.

== 2.2.0 | 4th December 2023 ==
* Add: New licensing.
* Add: Connections with AI.

== 2.1.5 | 30th November 2023 ==
* Fix: Possible issue with PHP 7.3

== 2.1.4 | 29th November 2023 ==
* Update: Logic around inheriting queries in query loop.
* Update: Logic around and/or with filters.
* Fix: Issue with sliders in RTL
* Fix: Arrow styles not showing on the front end.

== 2.1.3 | 17th November 2023 ==
* Add: Advanced query loop block
* Fix: Post grid container padding not applying in editor

== 2.1.2 BETA 3 | 11th October 2023 ==
* Update: Add in 2.0.16
* Update: Fix updater notice.

== 2.1.1 BETA 2 | 5th October 2023 ==
* Update: Text and Button blocks to use dynamic takeover instead of inline for repeater and query.

== 2.1.0 BETA 1 | 16th August 2023 ==
* Add: Repeater Field Block.

== 2.0.17 | 13th November 2023 ==
* Add: Setting to always show all custom fields in dynamic content.
* Fix: Issue with Advanced From webhook values.
* Fix: Post Grid custom except length when set greater than 55.
* Fix: Setting post grid box shadow color
* Fix: Post content & post excerpt both being selected in meta relationship dynamic content.
* Fix: Better sizing for modal preview in editor.

== 2.0.16 | 11th October 2023 ==
* Update: Entry save when using multiple file uploads.
* Update: ActiveCampaign connection.
* Fix: Possible php notices.

== 2.0.15 | 3rd October 2023 ==
* Fix: Responsive max width issue in advanced slider.
* Fix: Some Mailchimp API requests failing.
* Fix: Possible issue with older PHP version.
* Fix: Modal border radius not applying if no color is set on border.

== 2.0.14 | 19th September 2023 ==
* Add: Conditional fields for form block.
* Add: Option in advanced slider to disable drag.
* Fix: Portfolio fluid carousel issue.
* Fix: Issue with dynamic posts page title.
* Fix: Issue with slide render inline.

== 2.0.13 | 11th September 2023 ==
* Fix: Modal border not applying unless background color was set.
* Fix: Modal border radius unit not applying.
* Fix: Image overlay image ratios on front end and in editor.
* Fix: Selecting a block inside a modal, from list view, will preview the modal.
* Fix: Possible issue on widgets page.
* Fix: Issue with Portfolio carousel in element.
* Fix: Issue with mobile slider ratio setting.

== 2.0.12 | 28th August 2023 ==
* Fix: Advanced slider output on frontend.

== 2.0.11 | 24th August 2023 ==
* Update: Add attributes to portfolio filter query.
* Fix: Issue with image overlay and firefox browser.
* Fix: Issue with form, modal, video popup in advanced slider.
* Fix: Min height issue in split content
* Fix: Autoplay off in portfolio carousel.
* Fix: Portfolio grid gap settings.

== 2.0.10 | 16th August 2023 ==
* Fix: Saving product carousel individual post selection.
* Fix: Portfolio Grid single post selection ui.
* Fix: Portfolio Grid gap not working on frontend.

== 2.0.9 | 14th August 2023 ==
* Update: Add filter for in query content.
* Update: Blocks to API 3.

== 2.0.8 | 4th August 2023 ==
* Fix: Issue with portfolio grid filter.
* Fix: Issue with media library filter bar.
* Fix: Issue with excerpt length on Products in Post Grid.
* Fix: Translation issue.

== 2.0.7 | 2nd August 2023 ==
* Fix: Issue with read more text color.
* Fix: Manual post selection in Portfolio grid block.

== 2.0.6 | 2nd August 2023 ==
* Fix: Issue with missing css output when post grid block is present.
* Fix: Issue with carousel align in post carousel.

== 2.0.5 | 1st August 2023 ==
* Fix: Template locate issue.
* Fix: Splide Script name
* Fix: Post grid taxonomy filtering.
* Fix: Portfolio grid columns in editor when using column gaps.
* Fix: Some responsive editor previews not displaying correctly.
* Fix: Custom classes not applying to gallery when using Dynamic Content.
* Fix: Portfolio grid carousel not initiating on frontend.
* Fix: Toolbar appearing behind modal block preview in editor.
* Fix: Modal hidden when link disabled.
* Fix: Image overlay radio not working in editor.

== 2.0.4 BETA 5 | 12 July 2023 ==
* Fix: Improve PHP 8.2 support.
* Fix: Issue with advanced form submit defaults.
* Fix: "Select the post Taxonomy" not functioning.
* Fix: Increase specificity of Splide CSS to avoid conflicts.
* Fix: Product categories not showing while selecting individual products in Product Carousel.

== 2.0.3 BETA 4 | 16th June 2023 ==
* Fix: Class Conflict Issue.

== 2.0.2 BETA 3 | 15th June 2023 ==
* Update: Site wide Ids
* Update: Pro Advanced Form Block
* Update: Block Renaming

== 2.0.1 BETA 2 | 3rd May 2023 ==
* Update: Advanced slider to use splide.
* Fix: Issue with post carousel defaults.

== 2.0.0 BETA 1 | 28th April 2023 ==
* Update: Use tabbed settings layout in all blocks
* Update: Ability to copy/paste styles between blocks
* Update: Support for variable font sizes
* Update: Added block default settings
* Update: All blocks using block API version 2
* Update: Add more responsive setting to blocks and improve CSS output
* Update: Use Kadence components for font, border, and spacing controls
* Update: All sliders & carousels using Splide
* Update: New border/radius controls for Image Overlay
* Update: New border/radius controls for Portfolio grid
* Update: New border/radius controls for Video Popup Block
* Update: Improved clarity and organization for all block controls
* Update: New editing experience for Modal block content
* Fix: Various effects and style now properly applied for Video Popup Block
* Fix: Theme style now more naturally apply to Product Carousel items.
* Fix: Support for private, unlisted, and password protected Vimeo videos
* Fix: Image overlay title line height when inserting via hooked element
* Fix: Split content issue when min-height is removed
* Fix: Image overlay focus-within hover displaying improperly
* Fix: Modal z-index when inserted via hooked element
* Fix: Issue with image overlay margin causing layout to shift.
* Fix: Mobile preview of AOS.


== 1.7.29 | 21st April 2023 ==
* Update: Support for contextual media content in image block caption.
* Fix: Issue with saving form date when referer over 255 characters.
* Fix: Issue with image overlay focus bug.

== 1.7.28 | 10th April 2023 ==
* Fix: Issue with dynamic link showing HTML entities.
* Fix: Post Grid/Carousel image issue size when disabling the "Image is a link to post".
* Fix: Issue with system files.
* Fix: Issue with post select in 6.2.
* Fix: Issue with portfolio grid carousel in widgets screen.

== 1.7.27 | 28th March 2023 ==
* Update: Fix slider not showing correctly in tablet or mobile preview.
* Fix: Issue with lightbox caption when using a dynamic gallery.
* Fix: Spacing issue when returning a list inline dynamic content.
* Fix: Color issue in 6.2.
* Fix: Display products matching all setting.

== 1.7.26 | 24th March 2023 ==
* Update: Fix custom class in slider.

== 1.7.25 | 23rd March 2023 ==
* Update: Form database save of IP.
* Update: Advanced slider to be a slider in the editor.
* Fix: Animate on scroll settings not showing for pro blocks
* Fix: Conditional display settings not showing for pro blocks
* Fix: Date meta field not showing as option from PODS in dynamic content.
* Fix: Link icon not showing in block toolbar when Rank Math SEO is active.
* Fix: Dynamic gallery gutter settings.
* Fix: Possible issue with taxonomy select.
* Fix: Aria label in video popup.
* Fix: Issue with string based image meta save.

== 1.7.24 | 23rd February 2023 ==
* Update: Lightbox styles.
* Fix: Issue gap in post grid block when using filter.
* Fix: Video Popup Max width issue.

== 1.7.23 | 21st February 2023 ==
* Fix: Query CSS build for dynamic styles.

== 1.7.22 | 21st February 2023 ==
* Update: Term select to prevent the need for full list.
* Update: Splide to not render clones when carousel doesn't have enough items.
* Update: Video Popup to use Glight.
* Fix: Issue with icon selector.
* Fix: Issue with some types of dynamic images.
* Fix: Issue with row gap in post grid carousel.
* Fix: Issue with dynamic alt text.
* Fix: Some dynamic content not rendering in context.
* Fix: Issue with align not working in product carousel block.

== 1.7.21 | 17th January 2023 ==
* Fix: Possible php bug with 8.2
* Fix: Dynamic issues with Blocks 3.0

== 1.7.20 | 19th December 2022 ==
* Update: Small change to the way hooked components work.
* Update: Custom icon frontend support.

== 1.7.19 | 17th November 2022 ==
* Add: Woocommerce product gallery dynamic source.
* Update: Screen reader text for pagination.
* Update: Prevent issue with RankMath in sitemap XML sitemap.
* Fix: Issue with Slide transition in advanced slider.
* Fix: Issue with modal focus transfer when closed.
* Fix: Author images alt text.

== 1.7.18 | 21st October 2022 ==
* Add: Option to show all custom fields in dynamic data selection.
* Add: Option to search for active ActiveCampaign tags.
* Update: Video Popup & Video Overlay to work in horizontal sections when a max width is applied.

== 1.7.17 | 19th October 2022 ==
* Fix: Possible Issue with Dynamic Gallery.

== 1.7.16 | 18th October 2022 ==
* Add: ActiveCampaign subscribe to form block.
* Update: Custom icons to save sets as data and not files.
* Update: Taxonomy Select for conditionals.
* Update: Dynamic Content, custom field selection reload when source changes.
* Update: Custom field rest call to use relationship as source.
* Update: Modal button color selection.
* Fix: Issue with dynamic gallery styles.
* Fix: Issue with ACF archive custom meta.
* Fix: Slider transition issue.
* Fix: Extra min height control issue with slider.
* Fix: Issue with dynamic gallery block and conditional display.
* Fix: Issue with animate on scroll not working consistently after layout shift.
* Fix: Language strings, missing in translation.

== 1.7.15 | 29th August 2022 ==
* Fix: Issue with missing selection when using custom field for dynamic link.

== 1.7.14 | 24th August 2022 ==
* Fix: Issue with post grid carousel not rendering in admin.

== 1.7.13 | 23rd August 2022 ==
* Add: Pods Framework support to dynamic content.
* Update: Author custom fields in link settings.
* Update: include kadence_blocks_pro_product_carousel_atts filter.
* Update: Add option to show author image in meta for post carousel grid block.
* Update: Improve css when selecting dynamic fields.
* Fix: Issue with carousel when empty.
* Fix: Issue with show more and loop templates.
* Fix: Issue with dynamic content selector in split content.
* Fix: Issue with thumbnail gallery slider showing navigation dots.
* Fix: Issue with image overlay block and loop templates.
* Fix: Issue with dynamic html block and relationship content.
* Fix: Translations not working.
* Fix: Issue with fallback text in dynamic content.
* Fix: Issue with animate on scroll and some elements.

== 1.7.12 | 1st June 2022 ==
* Fix: Link style in dynamic list block.
* Fix: Product carousel Autoplay speed.

== 1.7.11 | 31st May 2022 ==
* Add: Add option for auto scroll carousel.
* Fix: Issues with dynamic content not pulling in context.
* Fix: Issue with dynamic galleries and thumbnails.
* Fix: Issue with slow scrolling carousel.

== 1.7.10 | 5th May 2022 ==
* Update: By default force sticky posts to not increase the posts per page when using post grid without pagination.
* Update: Option to disable link on title in post grid block.
* Fix: Issue with form entries search.
* Fix: Issue with animate on scroll when below a carousel.
* Fix: Issue with inline dynamic content not showing meta field options.
* Fix: Issue with relationship ACF field where return is an object.
* Fix: Issue with dynamic galleries in columns.
* Fix: Issue with form entries meta not being removed correctly when entry is deleted.

== 1.7.9 | 28th April 2022 ==
* Fix: Issue with carousel not loading with async.

== 1.7.8 | 21st April 2022 ==
* Add: Dynamic HTML Block.
* Add: Relationship Dynamic Content Options.
* Add: Filter to optionally enable dynamic content for password protected posts.
* Update: Splide js.
* Fix: Issue with Typography component not saving.
* Fix: Issue with dynamic gallery using slider type.

== 1.7.7 | 17th April 2022 ==
* Fix: Issue with post grid padding when only one direction set in mobile and tablet.
* Fix: Issue with autoplaying carousels in tabs.
* Fix: Issue with portfolio grid block pagination.

== 1.7.6 | 14th April 2022 ==
* Add: BoxShadow controls to modal block link.
* Update: Modal block with option to hide popup content when block is not selected.
* Fix: ACF checkboxes not showing highlighted in custom post meta for dynamic list.
* Fix: Issue with Advanced Slider Inner Max Width.
* Fix: Issue with carousel in tabs.
* Fix: Issue with modal link icon when using em units.

== 1.7.5 | 11th April 2022 ==
* Fix: Carousel Order being one off once rendered.
* Fix: Autoplay always one in post grid carousel.

== 1.7.4 | 9th April 2022 ==
* Update: Improve responsive settings selection when in widgets block editor.
* Fix: Issue with ACF and Dynamic Gallery.
* Fix: Issue were portfolio carousel block and product carousel block on the same page created a conflict
* Fix: Issue with post grid carousel, autoplay, gap settings and missing styles.

== 1.7.3 | 9th April 2022 ==
* Fix: Issue with meta font size in post grid block.
* Fix: Issue with content padding and background in post grid block.

== 1.7.2 | 9th April 2022 ==
* Fix: Issue causing error with post grid block when using a custom excerpt Length.

== 1.7.1 | 8th April 2022 ==
* Fix: ACF Data issue.

== 1.7.0 | 8th April 2022 ==
* Add: Dynamic list block.
* Add: MetaBox dynamic source support
* Update: Gallery can be dynamically sourced.
* Update: Media as a dynamic source.
* Update: Add unboxed styling for product carousel.
* Update: Use Splide js for product carousel and post grid (drop jQuery requirement).
* Update: Padding controls for Post Grid Carousel.
* Update: AOS and Conditional settings have user visibility control.
* Fix: Dynamic images missing alt text.
* Fix: Issue with modal scrolling on iphone.
* Fix: Issue with images missing srcset.
* Fix: issue with post grid if initial style not selected.
* Fix: Issue with filters in dynamic content not running properly.

== 1.6.1 | 10th February 2022 ==
* Add: Option to clear animation settings.
* Tweak: Possible issue with taxonomies.
* Fix: Issue with dynamic fallback image in image block.

== 1.6.0 | 14th January 2022 ==
* Add: taxonomy as a conditional display option.
* Update: Dynamic content for image block.
* Update: Add more Image ratio options for post and portfolio grid.
* Fix: Conditionally display controls.

== 1.5.11 | 23rd December 2021 ==
* Update: Prevent split content block from showing broken if advanced heading disabled.
* Fix: issue with animate on scroll loading in sidebar.
* Fix: Issue with modal and section not showing dynamic content correctly archive.

== 1.5.10 | 20th December 2021 ==
* Fix: issue with dynamic content src archive description adding extra paragraph tag.

== 1.5.9 | 14th December 2021 ==
* Fix: Issue with selecting multiple blocks.

== 1.5.8 | 13th December 2021 ==
* Add: Conditionally Show Block via dynamic content.
* Fix: Issue with links not rendering correctly in query block.

== 1.5.7 | 7th December 2021 ==
* Update: Tweak some javascript around animation on scroll.
* Update: API Class.
* Update: Dynamic Content filters, better context control.
* Update: Image overlay link field with clear link option.
* Update: Split Content block cover image when text is larger then min-height setting.
* Update: CSS specificity with post grid title color.
* Update: MailChimp API requests item limit.

== 1.5.6 | 5th November 2021 ==
* Add: Aria Label option to video popup.
* Add: Mapping for fields for Webhook.
* Add: Post Grid title hover color option.
* Fix: Thumbnail gallery load layout shift.
* Fix: Issue with Image overlay.
* Fix: Issue with post grid selecting by custom taxonomy.
* Fix: Issue with product carousel individual select.

== 1.5.5 | 12th October 2021 ==
* Update: Dynamic content to work better in query block.
* Fix: Modal z index.
* Fix: Issue with color picker in some blocks.
* Fix: IP address issue in entries.

== 1.5.4 | 8th September 2021 ==
* Add: Mailchimp tag support.
* Fix: Hex color input.
* Fix: ACF Site Options.

== 1.5.3 | 3rd September 2021 ==
* Update: Video Pop dynamic image.
* Fix: Individual select for post grid.

== 1.5.2 | 2nd September 2021 ==
* Add: More ratio options to slider.
* Add: Dynamic content options to video popup.
* Fix: Issue with image overlay and square ratio.
* Fix: Custom Icons in widget blocks.
* Fix: Order when individually selecting products.

== 1.5.1 | 24th August 2021 ==
* Add: Text transform control to image overlay.
* Add: Max width to image overlay.
* Add: Basic Responsive support for split content in editor.
* Update: Automatically exclude current post in post blocks.
* Update: in Element preview with latest post using dynamic content.
* Fix: issue with split content overlap.
* Fix: Overlay color opacity select.
* Fix: Post Block issue with google fonts when added to a sidebar.
* Fix: Issue with widget screen.
* Fix: Issue with post grid carousel in tabs.

== 1.5.0.2 - BETA | 23rd July 2021 ==
* Fix: License issue.

== 1.5.0.1 - BETA | 20th July 2021 ==
* Fix: issue with portfolio block.

== 1.5.0 - BETA | 16th July 2021 ==
* Add: Page specific scripts.
* Add: Beta Dynamic Content
* Fix: Issue with swipe animation.
* Fix: Issue with portfolio grid center mode.
* Fix: Issue with portfolio grid column gap.
* Fix: issue with thumbnails and 16:9 ratio.

== 1.4.33 | 24th May 2021 ==
* Update: Split content image link settings.
* Fix: Modal button border-radius setting.
* Fix: Activation issue.

== 1.4.32 | 7th May 2021 ==
* Update: Image overlay editor css.
* Update: Slider JS for possible issue with accordion.

== 1.4.31 | 3rd May 2021 ==
* Update: Portfolio Grid Carousel Block editor rest end points.
* Fix: Issue with portfolio filter not wrapping if really large.
* Fix: Issue with custom modal links not being tabbable.
* Fix: Issue with image overlay focus state.
* Fix: Issue with date meta output.
* Fix: Issue with above taxonomy symbol.

== 1.4.30 ==
* Fix: Issue with Modal Block Button not being tabbable.
* Fix: User info block date localization.
* Fix: Issue with Deactivating.
* Fix: Issue with Masonry Portfolio Grid.
* Fix: Issue with Border Width for Portfolio Grid.

== 1.4.29 ==
* Update: Support ithemes Toolkit/Agency license.

== 1.4.28 ==
* Update: Post Grid Carousel Block editor rest end points.
* Update: Typography filter hooks.
* Update: Toolset filters.
* Fix: Slider Background image select.

== 1.4.27 ==
* Add: New Controls for Countdown Block.
* Fix: Form Entires export as CSV with WC not installed.

== 1.4.26 =
* Add: Form Entires export as CSV.
* Update: Post Block filter for non latin language support.
* Update: JSON upload allowance.

== 1.4.25 ==
* Update: Styling issue with modal and toolset view.
* Add: Option to turn off image link in post grid.
* Update: Allow for auto responder message to have fields e.g. {field_1}.
* Fix: Issue with animate on scroll.
* Fix: Issue with image overlay letter spacing.

== 1.4.24 ==
* Add: Shortcode rendering to modal when load content after footer.
* Fix: Preview of order by "menu_order"
* Fix: Issue with post grid css filter css.

== 1.4.23 ==
* Fix: Issue with post grid added through element.
* Fix: Issue with youtube embed not respecting start time.
* Update: Post Grid with Menu Order option for query.
* Update: Post Grid with option to open links in new tab.
* Update: Post Grid no post action.

== 1.4.22 ==
* Fix: Issue with auto response dynamic subject.
* Fix: Issue with post grid needing excerpt.
* Fix: Possible issue with modal close styling when placed outside.

== 1.4.21 ==
* Add: Webhook Option to Kadence Blocks Form.

== 1.4.20 ==
* Fix: Styling of modal when added in the footer.

== 1.4.19 ==
* Add: Option in Modal to load content in footer (better support for modal placement outside of content area).
* Fix: issue with video popup fullscreen option not showing.

== 1.4.18 ==
* Add: New User Info Block.
* Add: Excerpt Length Options to Post Block.
* Add: Preset for Image Align Left to Post Block.
* Fix: Mobile Font Size on Excerpt is not working on Post Grid/Carousel
* Add: Time to form database entries.
* Fix: youtube video popup auto play.
* Fix: Issue with multiple post grids and filter items.
* Fix: Issue with reveal animation.

== 1.4.17 ==
* Add: New Image overlay image ratio options - 2:3 and 3:2
* Update: pot translation file.
* Update: Mulish font.
* Update: WPML post grid support.
* Update: Send in blue api calls.
* Fix: Issue with modal and slider inside.
* Fix: post grid not showing 0 comments.
* Fix: issue with masonry resize.
* Fix: Issue with post grid and some themes.
* Fix: Image overlay letter spacing issue.

== 1.4.16 ==
* Fix: Possible issue with sendinblue in certain instances.

== 1.4.15 ==
* Fix: issue with animation scripts not loading in elements.

== 1.4.14 ==
* Add: Option for random order in post grid.
* Update: Post grid max items to 300.
* Fix: Issue with slider not loading.

== 1.4.13 ==
* Add: Image size option to portfolio and post grid blocks.
* Add: Option to turn off hover pause for slider.
* Add: Option to allow sticky post in post grid block.
* Add: Option for filter in Post Grid Block.
* Add: Hook Custom icons into mega menu icons.
* Update: Better support for global palette.
* Fix: Issue with product carousel not showing enough posts when using individual.
* Fix: Issue with range control.

== 1.4.12 ==
* Remove: Modal class to prevent possible issues.
* Fix: issue with post grid, left algin image width wasn't being respected.

== 1.4.11 ==
* Fix: Possible Issue with excerpt color.

== 1.4.10 ==
* Update: post grid block to load css in head.
* Update: Video popup to use no cookie embed link.
* Update: Post Grid with footer meta align bottom.
* Update: Form, Mailchimp now with double opt in option.
* Update: Post Grid uses global colors.
* Fix: Issue with slider pause on hover.

== 1.4.9 ==
* Fix: Product Carousel with storefront theme.
* Add: Background overlay to slider.

== 1.4.8 ==
* Fix: Modal block to allow more dynamic content.

== 1.4.7 ==
* Fix: Modified date to post block.
* Add: Border Radius to Post Block Container.
* Add: Box shadow to Post Block Container.
* Fix: Issue with portfolio grid in carousel fluid.

== 1.4.6 ==
* Update: Video Popup Block to support different youtube urls (youtu.be and youtube-nocookie)
* Fix: Portfolio Carousel height not working.
* Add: AOS defaults.
* Add: Better color controls to advnaced slider block.
* Add: Modified date to post block.

== 1.4.5 ==
* Fix: Issue with storefront theme and product block.
* Fix: Issue with Send in blue subscribe.
* Update: Allow html in title of admin post grid.

== 1.4.4 ==
* Add: Focus highlght for portfolio grid block.
* Add: Three new prebuilt layouts.
* Fix: Post limit from pulling from settings > reading.

== 1.4.3 ==
* Fix: Ratio slider for Tablet and Mobile.

== 1.4.2 ==
* Add: Mailchimp subscribe to Email Form.
* Add: Image size option for image overlay.
* Add: Make Translatable "no posts".
* Add: Exclude switch to posts block term selection.
* Add: Show Unique to post block, excludes posts from showing in other blocks further down the page.
* Add: Image size option for poster image in video popup.
* Add: Video popup background, close color max width and on pop animation options.
* Tweak: Pangiation to try and prevent a conflict.

== 1.4.1 ==
* Fix: Update Issue.

== 1.4.0 ==
* Add: Custom Icon Importer.
* Fix: Isotope Conflict.

== 1.3.9 ==
* Add: Send in Blue subscribe to Email Form.
* Fix: Column Gutter when in grid layout and using filter.
* Fix: Video Popup inherit image size bug.

== 1.3.8 ==
* Fix: Best Selling in Product Carousel.

== 1.3.7 ==
* Fix: Product Carousel Block.

== 1.3.6 ==
* Add: Portfolio Grid/Carousel Block
* Fix: Post Grid/Carousel Block Heading setting.
* Add: Post Grid/Carousel Block option to choose a custom taxonomy for posts.

== 1.3.5 ==
* Update: Remove icon front from slider js.
* Update: Add Pro Prebuilt Template Layouts.

== 1.3.4 ==
* Fix: Slider css bug.

== 1.3.3 ==
* Update: Tweak how Blocks CSS is loaded, now only loads for blocks used on page.

== 1.3.2 ==
* Update: Change how Blocks CSS is loaded, now only loads for blocks used on page.
* Fix: Bug where modal was contained by row max width.

== 1.3.1 ==
* Add: Support for custom css class in post grid blocks.

== 1.3.0 ==
* Add: Pro Form Options

== 1.2.10 ==
* Fix: Animation attributes not saving.

== 1.2.9 ==
* Fix: PHP issue in some versions.

== 1.2.8 ==
* Add: Allow Image Overlay to not have a link.
* Add: Offset for Post Grid Block.
* Add: Text Align to Post Grid Block.
* Add: Options to have 60 posts in Post Grid Block.
* Update: Translations issue.
* Fix: Issue with split content block throwing error.
* Prep: New Block Entries
* Prep: New Form Block

== 1.2.7 ==
* Add: New Advanced Slider Block.
* Fix: Css Conflict
* Fix: Css Bug with image overlay.

== 1.2.6 ==
* Fix: Post Grid bug.

== 1.2.5 ==
* Fix: Pagination if on the front page.

== 1.2.4 ==
* Fix: Post Grid Carousel error when CPT doesn't support author or image.

== 1.2.3 ==
* Fix: Split Content issue.
* Add: Split Content animation options.

== 1.2.2 ==
* Update: modal javascript to check child elements of modal trigger class.
* Fix: Title, subtitle mobile font size for overlay block.

== 1.2.1 ==
* Update: 5.9 support.

== 1.2.0 ==
* Add: Video Popup Block.
* Update: Image Overlay Block, full width title background.

== 1.1.10 ==
* Fix: modal loading on shop page.

== 1.1.9 ==
* Fix: CSS Padding issue SplitContent.

== 1.1.8 ==
* Fix: Post Grid read more wrap class name.

== 1.1.7 ==
* Fix: Mobile Modal issue.

== 1.1.6 ==
* Fix: WC 3.6 Rest API Notice.

== 1.1.5 ==
* Update: Add Custom Post type support for Post Grid/Carousel.

== 1.1.4 ==
* Update: Modal, if nested fix z-index.

== 1.1.3 ==
* Update: Modal, prevent scroll to top on open.
* Update: Image Overlay, Add options to turn off subtitle, or title.
* Update: Image Overlay, Add options for ratio size.
* Update: Image Overlay, Add options for border side control.
* Update: Add 1st Prebuilt Content.

= 1.1.2 ==
* Update: Icons.
* Update: Add Animation options to image overlay.
* Add: New Product Carousel Block

= 1.1.1 ==
* Fix: Issue slick slider.
* Fix: Issue with Modal Background.
* Update: Animation css.

= 1.1.0 ==
* Fix: Issue image overlay color.
* Fix: Issue with Modal overlay Color.
* Add: Animate Cover Swipe support.

== 1.0.9 ==
* Fix: Issue image overlay.
* Add: Inital Animate on Scroll.

== 1.0.8 ==
* Fix: Issue modal spacing,
* Fix: Modal Close settings not showing.
* Fix: Empty Span issue.
* Update: Enable Activation through Theme API.

== 1.0.7 ==
* Fix: Issue with Images in Post Grid
* Add: New Modal Block
* Update: CSS Loading.

== 1.0.6 ==
* Fix: Font size Post grid.
* Update: Change Carousel Arrow icons.
* Update: Update footer text.
* Fix: Script update for nested.

== 1.0.5 ==
* Update: Remove attribute data from post grid.
* Update: Fix Classname in html.

== 1.0.4 ==
* Update: Typography Filter.
* Update: Multisite license activation.
* Update: Move css inline.
* Add: Post Loop Grid or Carousel Block.

== 1.0.3 ==
* Fix: Overlay subtitle size.
* Update: SplitContent to allow offset and box shadow.

== 1.0.2 ==
* Update: Add Split Content Block.

== 1.0.1 ==
* Update: Gutenberg 4.1 updates.

== 1.0.0 ==
* Initial Beta Release.
