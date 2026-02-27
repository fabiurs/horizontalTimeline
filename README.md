# Horizontal Timeline WordPress Plugin

## Overview
This plugin allows you to display a horizontal timeline on your WordPress site. It is designed to be simple to use, customizable, and visually appealing. The timeline can be added to any post or page using a shortcode.

## Features
- Responsive horizontal timeline display
- Easy-to-use shortcode
- Customizable styles via CSS
- Admin interface for managing timeline events

## Installation
1. Upload the plugin files to the `/wp-content/plugins/horizontal-timeline/` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the admin interface to add and manage timeline events.

## Usage
Add the following shortcode to any post or page where you want the timeline to appear:

```
[horizontal_timeline]
```

You can customize the timeline by editing the CSS in `assets/style.css` or by overriding styles in your theme.

## File Structure
- `horizontal-timeline.php`: Main plugin file
- `assets/`: Contains JavaScript and CSS files
  - `admin-script.js`, `admin-style.css`: Admin area scripts and styles
  - `script.js`, `style.css`: Frontend scripts and styles
- `includes/`: PHP classes and views
  - `class-admin.php`: Admin functionality
  - `class-shortcode.php`: Shortcode logic
  - `views/admin-page.php`: Admin page template

## Customization
- Edit `assets/style.css` for frontend timeline styles
- Edit `assets/admin-style.css` for admin interface styles
- Modify PHP files in `includes/` to change functionality

## Support
For support or feature requests, please contact the plugin author or open an issue on the plugin's repository.

---
**Author:** Fabian Ursache  
**Version:** 1.4
