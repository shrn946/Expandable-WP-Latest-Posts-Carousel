# Expandable Animated Post Slider

## Description
Expandable Animated Post Slider is a WordPress plugin that allows you to display a carousel of latest posts with animated expandable cards.

## Installation
1. Download the plugin zip file from the WordPress plugin repository or GitHub.
2. Upload the plugin zip file via the WordPress admin dashboard or manually extract the contents to the `/wp-content/plugins/` directory.
3. Activate the plugin through the 'Plugins' menu in WordPress.

## Usage
To use the plugin, you can insert the shortcode `[latest_posts_carousel]` into any WordPress post, page, or widget where you want to display the carousel of latest posts.

You can customize the shortcode using the following attributes:

- `exclude_category`: Specify the category slug(s) to exclude posts from. Example: `exclude_category="electronics"`
- `count`: Control the number of posts displayed in the carousel. Example: `count="3"`

Here are some examples of how to use the shortcode:

```shortcode
[latest_posts_carousel]


Displays the latest posts with the default settings.

[latest_posts_carousel exclude_category="electronics" count="3"]


Displays 8 latest posts with no specific category exclusion.


[latest_posts_carousel count="8"]


Support
For any issues or questions regarding the plugin, please visit the plugin page on WordPress.org or submit an issue on GitHub.
