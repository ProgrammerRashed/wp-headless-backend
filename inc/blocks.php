<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;
use WpGraphQLCrb\Container as WpGraphQLCrbContainer;

add_action('carbon_fields_register_fields', 'headless_register_components');

function headless_register_components()
{
    WpGraphQLCrbContainer::register(
        //********************************************************** */
        //************************ Hero Section Block ******************/ 
        //********************************************************** */
        Block::make(__('Hero Slider', 'nh'))
            ->add_fields([
                Field::make('html', 'crb_information_text')
                    ->set_html('<h2>Hero Slider</h2>'),

                Field::make('complex', 'hero_slides', __('Hero Slides', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('image', 'slide_image', __('Slide Image', 'nh'))
                        ->set_value_type('url'),
                        Field::make('text', 'slide_title', __('Slide Title', 'nh')),
                        Field::make('textarea', 'slide_description', __('Slide Description', 'nh')),

                        Field::make('complex', 'banner_cta', __('CTA Buttons', 'nh'))
                        ->set_layout('tabbed-horizontal')
                            ->add_fields([
                                Field::make('text', 'cta_label', __('CTA Label', 'nh')),
                                Field::make('text', 'cta_link', __('CTA Link', 'nh')),
                            ])
                    ])
            ])
            ->set_icon('dashicons-star-filled')
            ->set_keywords([__('Hero Slider', 'nh')])
            ->set_description(__('A hero section with sliding banners and CTAs', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ TOP CLIENT SECTION ******************/ 
        //********************************************************** */
        Block::make(__('Top Clients Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Top Clients Section</h2>'),
                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('select', 'variant', __('Variant', 'nh'))
                    ->add_options([
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                    ])
                    ->set_default_value('primary'),
                Field::make('association', 'clients', __('Select Clients', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'clients',
                        ]
                    ])
                    ->set_max(10)
                    ->set_help_text(__('Select clients to feature in this section.')),
            ])
            ->set_icon('groups')
            ->set_keywords([__('Clients', 'nh'), __('Top Clients', 'nh')])
            ->set_description(__('Displays top clients with logos and links', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Our Services Section ******************/ 
        //********************************************************** */
        Block::make(__('Our Services Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Our Services Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('textarea', 'description', __('Section Description', 'nh')),

                Field::make('text', 'button_label', __('Button Label', 'nh')),
                Field::make('text', 'button_link', __('Button Link', 'nh')),

                Field::make('association', 'services', __('Select Services', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'services',
                        ]
                    ])
                    ->set_max(6)
                    ->set_help_text(__('Select up to 6 services to display in this section.')),
            ])
            ->set_icon('admin-tools')
            ->set_keywords([__('Services', 'nh'), __('Our Services', 'nh')])
            ->set_description(__('Displays up to 6 associated services with a section heading and CTA', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ Our Legacy Section ******************/ 
        //********************************************************** */
        Block::make(__('Our Legacy Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Our Legacy Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('text', 'subtitle', __('Subtitle', 'nh')),
                Field::make('textarea', 'description', __('Section Description', 'nh')),

                Field::make('text', 'button_label', __('Button Label', 'nh')),
                Field::make('text', 'button_link', __('Button Link', 'nh')),

                Field::make('image', 'image', __('Section Image', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text(__('Upload an image for this section.'), 'nh'),
            ])
            ->set_icon('shield-alt')  // Choose an icon that fits the legacy section
            ->set_keywords([__('Legacy', 'nh'), __('Our Legacy', 'nh')])
            ->set_description(__('Displays a section about your company legacy with title, subtitle, description, and button', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),

        //********************************************************** */
        //************************ Achievement Section ******************/ 
        //********************************************************** */

        Block::make(__('Achievement Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Achievement Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('image', 'section_image', __('Section Image', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text(__('Upload an image related to your achievements section.')),

                Field::make('association', 'timeline', __('Timeline of Achievements', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'achievements',
                        ]
                    ])
                    ->set_max(10)  // You can set the maximum number of timeline entries (services)
                    ->set_help_text(__('Select services related to achievements to display in the timeline.')),
            ])
            ->set_icon('star-filled')  // You can choose an icon that fits the achievement section
            ->set_keywords([__('Achievement', 'nh'), __('Timeline', 'nh')])
            ->set_description(__('Displays an achievement section with a timeline of services', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),

        //********************************************************** */
        //************************ About SAF Section ******************/ 
        //********************************************************** */

        Block::make(__('About SAF Section', 'nh'))
            ->add_fields([
                Field::make('html', 'crb_information_text')
                    ->set_html('<h2>About SAF Section</h2>'),
                Field::make('text', 'section_heading', __('Section Heading', 'nh')),
                Field::make('textarea', 'section_subheading', __('Section Subheading', 'nh')),
                Field::make('complex', 'button_details', __('Button Details', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('text', 'title', __('Button Title', 'nh')),
                        Field::make('text', 'link', __('Button Link', 'nh')),
                    ]),
            ])
            ->set_icon('info-circle') // You can choose an appropriate icon here
            ->set_keywords([__('About SAF Section Block', 'nh')])
            ->set_description(__('Custom Block for About SAF Section', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Home Video Section ******************/ 
        //********************************************************** */
        Block::make(__('Video Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Video Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),

                Field::make('image', 'thumbnail', __('Thumbnail Image', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text(__('Upload a thumbnail image for the video.')),

                Field::make('text', 'video_url', __('Video URL', 'nh'))
                    ->set_help_text(__('Enter the video URL (e.g., https://domovideo.videos/video.mp4 or a full external URL).')),
            ])
            ->set_icon('video-alt')  // You can choose an appropriate icon here
            ->set_keywords([__('Video', 'nh'), __('Security Video', 'nh')])
            ->set_description(__('Displays a video section with a thumbnail and video URL', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Our Presence Section ******************/ 
        //********************************************************** */
        Block::make(__('Our Presence Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Our Presence Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('text', 'total_offices', __('Total Offices', 'nh')),
                Field::make('text', 'head_office_name', __('Head Office Name', 'nh')),

                Field::make('complex', 'regional_offices', __('Regional Offices', 'nh'))
                    ->add_fields([
                        Field::make('text', 'location', __('Location Name', 'nh'))
                    ])
                    ->set_layout('tabbed-horizontal')
                    ->set_help_text(__('Add regional office locations.')),

                Field::make('complex', 'branch_offices', __('Branch Offices', 'nh'))
                    ->add_fields([
                        Field::make('text', 'location', __('Location Name', 'nh'))
                    ])
                    ->set_layout('tabbed-horizontal')
                    ->set_help_text(__('Add branch office locations.')),

                Field::make('image', 'map_image', __('Map Image', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text(__('Upload an image showing your office locations.')),
            ])
            ->set_icon('location')  // You can customize this icon
            ->set_keywords([__('Presence', 'nh'), __('Offices', 'nh')])
            ->set_description(__('Displays office presence info with regional and branch locations', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),

        //********************************************************** */
        //************************ Work Process Section ******************/ 
        //********************************************************** */
        Block::make(__('Work Process Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Work Process Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),

                Field::make('image', 'background_image', __('Background Image', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text(__('Upload a background image for the section.')),

                Field::make('complex', 'steps', __('Work Steps', 'nh'))
                    ->add_fields([
                        Field::make('text', 'number', __('Step Number', 'nh')),
                        Field::make('text', 'title', __('Step Title', 'nh')),
                        Field::make('textarea', 'description', __('Step Description', 'nh')),
                        Field::make('image', 'icon', __('Step Icon', 'nh'))
                            ->set_value_type('url')
                    ])
                    ->set_layout('tabbed-horizontal')
                    ->set_help_text(__('Add the step-by-step work process details.')),
            ])
            ->set_icon('list-view')
            ->set_keywords([__('Work Process', 'nh'), __('Steps', 'nh'), __('Progress', 'nh')])
            ->set_description(__('Displays a multi-step work process with icons and descriptions', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Why Choose Us Section ******************/ 
        //********************************************************** */
        Block::make(__('Why Choose Us Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Why Choose Us Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('textarea', 'description', __('Section Description', 'nh')),

                Field::make('radio', 'variant', __('Variant', 'nh'))
                    ->add_options([
                        'primary' => 'Primary',
                        'secondary' => 'Secondary',
                    ])
                    ->set_default_value('primary'),

                Field::make('complex', 'cards', __('Why Choose Cards', 'nh'))
                    ->add_fields([
                        Field::make('text', 'name', __('Card Title', 'nh')),
                        Field::make('textarea', 'description', __('Card Description', 'nh')),
                        Field::make('image', 'icon', __('Card Icon', 'nh'))->set_value_type('url'),
                        Field::make('image', 'image', __('Card Image', 'nh'))->set_value_type('url'),
                    ])
                    ->set_layout('tabbed-horizontal')
                    ->set_help_text(__('Add feature cards with icon and image')),
            ])
            ->set_icon('shield-alt')
            ->set_keywords([__('Why Choose', 'nh'), __('Security', 'nh'), __('Feature Cards', 'nh')])
            ->set_description(__('Highlights reasons to choose the company with feature cards', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ We Serve Industries Section ******************/ 
        //********************************************************** */
        Block::make(__('We Serve Industries Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>We Serve Industries Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),

                Field::make('complex', 'cards', __('Industry Cards', 'nh'))
                    ->add_fields([
                        Field::make('text', 'name', __('Industry Name', 'nh')),
                        Field::make('image', 'image', __('Industry Image', 'nh'))->set_value_type('url'),
                    ])
                    ->set_layout('tabbed-horizontal')
                    ->set_help_text(__('Add industries with corresponding images.')),

                Field::make('text', 'button_label', __('Button Label', 'nh')),
                Field::make('text', 'button_link', __('Button Link', 'nh')),
            ])
            ->set_icon('building')
            ->set_keywords([__('Industries', 'nh'), __('We Serve', 'nh'), __('Sectors', 'nh')])
            ->set_description(__('Displays industry sectors served with image cards and a CTA button.', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Testimonial Section ******************/ 
        //********************************************************** */
        Block::make(__('Testimonial Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Testimonial Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),
                Field::make('textarea', 'description', __('Section Description', 'nh')),

                Field::make('association', 'testimonials', __('Testimonials', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'testimonials',
                        ],
                    ])
                    ->set_max(10) // optional: limit how many can be selected
                    ->set_help_text(__('Select testimonial posts to display.')),
            ])
            ->set_icon('format-quote')
            ->set_keywords([__('Testimonials', 'nh'), __('Feedback', 'nh'), __('Reviews', 'nh')])
            ->set_description(__('Displays selected testimonials with section title and description.', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ Download Brochure Section ******************/ 
        //********************************************************** */
        Block::make(__('Download Brochure Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Download Brochure Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh')),

                Field::make('image', 'background_image', __('Background Image', 'nh'))
                    ->set_value_type('url'),

                Field::make('file', 'brochure_file', __('Brochure PDF File', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text(__('Upload the brochure PDF file here.')),

                Field::make('text', 'button_label', __('Button Label', 'nh'))
                    ->set_default_value('Download Brochure'),

                // Optional: This could auto-link to the file if no custom link is given.
                Field::make('text', 'button_link', __('Button Link (optional)', 'nh'))
                    ->set_help_text(__('If left empty, the button will use the PDF file link.')),
            ])
            ->set_icon('download')
            ->set_keywords([__('Brochure', 'nh'), __('Download', 'nh'), __('PDF', 'nh')])
            ->set_description(__('Block for brochure downloads with background image and CTA button.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Latest News Section ******************/ 
        //********************************************************** */
        Block::make(__('Latest News Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Latest News & Articles Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Latest News & Articles'),

                Field::make('association', 'items', __('Select News Articles', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'blogs', // Replace with 'blog' if you’re using a custom post type
                        ],
                    ])
                    ->set_max(6)
                    ->set_help_text(__('Select up to 6 latest blog posts or news items.')),

                Field::make('text', 'button_label', __('Button Label', 'nh'))
                    ->set_default_value('Explore All'),

                Field::make('text', 'button_link', __('Button Link', 'nh'))
                    ->set_default_value('/news-and-media'),
            ])
            ->set_icon('megaphone')
            ->set_keywords([__('News', 'nh'), __('Blog', 'nh'), __('Articles', 'nh')])
            ->set_description(__('Displays selected blog/news posts with a section title and CTA button.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ Contact Details Section ******************/ 
        //********************************************************** */
        Block::make(__('Contact Details Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Contact Details Section</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Contact Us'),

                Field::make('textarea', 'description', __('Description', 'nh'))
                    ->set_rows(3),

                Field::make('textarea', 'address', __('Address', 'nh'))
                    ->set_rows(2),

                Field::make('complex', 'phone_numbers', __('Phone Numbers', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('text', 'label', __('Phone Label', 'nh')),
                        Field::make('text', 'link', __('Phone Link (tel:)', 'nh')),
                    ])
                    ->set_max(5)
                    ->set_help_text('Add phone numbers with display label and tel link.'),

                Field::make('text', 'email', __('Email Address', 'nh'))
                    ->set_default_value('info@sentrysecuritybd.com'),
            ])
            ->set_icon('phone')
            ->set_keywords([__('Contact', 'nh'), __('Phone', 'nh'), __('Email', 'nh')])
            ->set_description(__('Displays contact information including phone numbers, address, and email.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Contact Map Section ******************/ 
        //********************************************************** */
        Block::make(__('Contact Map Section', 'nh'))
            ->add_fields([
                Field::make('html', 'info_text')
                    ->set_html('<h2>Contact Map Section</h2>'),

                Field::make('text', 'map_link', __('Google Map Embed Link', 'nh'))
                    ->set_default_value('https://www.google.com/maps/embed?...')
                    ->set_help_text('Paste the full Google Maps embed link here.'),
            ])
            ->set_icon('location-alt')
            ->set_keywords([__('Map', 'nh'), __('Location', 'nh'), __('Google Maps', 'nh')])
            ->set_description(__('Displays an embedded Google Map using a provided embed URL.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ FAQ Section ******************/ 
        //********************************************************** */
        Block::make(__('FAQ Section', 'nh'))
            ->add_fields([
                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Frequently Asked Questions'),

                Field::make('textarea', 'description', __('Section Description', 'nh')),

                Field::make('complex', 'faq_items', __('FAQ Items', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->add_fields('faq_item', [
                        Field::make('text', 'question', __('Question', 'nh')),
                        Field::make('textarea', 'answer', __('Answer', 'nh')),
                    ])
            ])
            ->set_icon('editor-help')
            ->set_keywords([__('FAQ', 'nh'), __('Questions', 'nh')])
            ->set_description(__('A list of frequently asked questions with answers.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),




        //********************************************************** */
        //************************ Blogs And Article ******************/ 
        //********************************************************** */
        Block::make(__('Blog and Articles Section', 'nh'))
            ->add_fields([
                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Blog And Articles'),

                Field::make('textarea', 'description', __('Description', 'nh')),

                Field::make('checkbox', 'need_filter', __('Enable Filter', 'nh'))
                    ->set_option_value('yes'),

                Field::make('checkbox', 'load_more', __('Enable Load More', 'nh'))
                    ->set_option_value('yes'),

                Field::make('association', 'blogs', __('Select Blogs', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'blogs',
                        ]
                    ]),

                Field::make('text', 'post_per_page', __('Posts Per Page', 'nh'))
                    ->set_default_value(9)
                    ->set_help_text('Maximum number of posts to show per page.'),
            ])
            ->set_icon('admin-post')
            ->set_keywords([__('blog', 'nh'), __('articles', 'nh')])
            ->set_description(__('Displays selected blog posts with filtering and load more option.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Global Banner Section ******************/ 
        //********************************************************** */  
        Block::make(__('Global Banner Section', 'nh'))
            ->add_fields([
                Field::make('text', 'section_title', __('Section Title', 'nh')),

                Field::make('image', 'background_image', __('Background Image', 'nh'))
                    ->set_value_type('url')
                    ->set_help_text('Upload the banner background image. Recommended size: 1920x600px')
            ])
            ->set_icon('format-image')
            ->set_keywords([__('banner', 'nh'), __('global', 'nh')])
            ->set_description(__('A simple banner section with a background image and title.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),

        //********************************************************** */
        //************************ Home Video Section ******************/ 
        //********************************************************** */
        Block::make(__('Home Videos Section', 'nh'))
            ->add_fields([
                Field::make('html', 'crb_information_text')
                    ->set_html('<h2>Home Videos Section</h2>'),
                Field::make('text', 'section_heading', __('Section Heading', 'nh')),
                Field::make('complex', 'videos', __('Videos', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('text', 'video_link', __('Video Link', 'nh'))
                            ->set_help_text(__('Enter the YouTube embed link (e.g., https://www.youtube.com/embed/...).', 'nh')),
                        Field::make('image', 'video_thumbnail', __('Video Thumbnail', 'nh'))
                            ->set_value_type('url')
                            ->set_help_text(__('Upload or select the video thumbnail image.', 'nh')),
                        Field::make('text', 'video_title', __('Video Title', 'nh')),
                    ]),
            ])
            ->set_icon('video') // You can choose an appropriate icon here
            ->set_keywords([__('Videos Section Block', 'nh')])
            ->set_description(__('Custom Block for Videos Section', 'nh'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),

        //********************************************************** */
        //************************ Mission & Vision with Counter Section ******************/ 
        //********************************************************** */
        Block::make(__('Mission & Vision with Counter Section', 'nh'))
            ->add_fields([
                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Our Legacy of Excellence Unveiled'),

                Field::make('text', 'section_subtitle', __('Section Subtitle', 'nh'))
                    ->set_default_value('30 Years of Service, Trusted by 150+ Companies'),

                Field::make('textarea', 'description', __('Description', 'nh')),

                Field::make('image', 'section_image', __('Section Image', 'nh'))
                    ->set_value_type('url'),

                Field::make('complex', 'mission_vision_data', __('Mission & Vision', 'nh'))
                    ->add_fields([
                        Field::make('text', 'mission_vision_title', __('Title (Mission/Vision)', 'nh')),
                        Field::make('textarea', 'mission_vision_description', __('Description', 'nh')),
                    ]),

                Field::make('complex', 'counter_data', __('Counters', 'nh'))
                    ->add_fields([
                        Field::make('text', 'count_number', __('Count Number', 'nh')),
                        Field::make('text', 'suffix', __('Suffix', 'nh'))->set_default_value('+'),
                        Field::make('text', 'counter_description', __('Description', 'nh')),
                    ]),
            ])
            ->set_icon('analytics')
            ->set_keywords([__('mission', 'nh'), __('vision', 'nh'), __('counter', 'nh')])
            ->set_description(__('Displays mission, vision, and statistic counters with an image.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ Half and Half Section ******************/ 
        //********************************************************** */

        Block::make(__('Half and Half Section', 'nh'))
            ->add_fields([
                Field::make('text', 'title', __('Title', 'nh'))
                    ->set_default_value('Our Journey Through Time Revealed!'),

                Field::make('complex', 'descriptions', __('Descriptions', 'nh'))
                    ->add_fields([
                        Field::make('textarea', 'paragraph', __('Paragraph', 'nh')),
                    ]),

                Field::make('complex', 'list_items', __('List Items (Optional)', 'nh'))
                    ->add_fields([
                        Field::make('text', 'text', __('Item Text', 'nh')),
                    ]),
                Field::make('image', 'card_image', __('Card Image', 'nh'))
                    ->set_value_type('url'),
                Field::make('checkbox', 'is_reversed', __('Reverse Layout?', 'nh'))
                    ->set_option_value('yes'),
                Field::make('checkbox', 'has_bg', __('Has Background Image?', 'nh'))
                ->set_option_value('yes'),

            ])
            ->set_icon('columns')
            ->set_keywords([__('half', 'nh'), __('section', 'nh'), __('image and text', 'nh')])
            ->set_description(__('Two-column layout with optional list and image, layout order toggle.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Generic Footer Cards Section ******************/ 
        //********************************************************** */
        Block::make(__('Generic Footer Cards Section', 'nh'))
            ->add_fields([
                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Achievements'),

                Field::make('checkbox', 'is_slider', __('Display as Slider?', 'nh'))
                    ->set_option_value('true'),

                Field::make('text', 'card_item', __('Card Item Label (e.g., "Achievement")', 'nh'))
                    ->set_default_value('Achievement'),

                Field::make('association', 'card_data', __('Select Achievements', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'achievements',
                        ]
                    ])
                    ->set_max(4),

                Field::make('text', 'items_per_row', __('Items per Row', 'nh'))
                    ->set_default_value(4),

                Field::make('complex', 'footer_cta', __('Footer CTA', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('select', 'type', __('CTA Type', 'nh'))
                            ->set_options([
                                'link' => 'Link',
                                'button' => 'Button'
                            ])
                            ->set_default_value('link'),

                        Field::make('text', 'label', __('CTA Label', 'nh'))
                            ->set_default_value('Explore All'),

                        Field::make('text', 'link', __('CTA Link', 'nh'))
                            ->set_default_value('/explore'),
                    ]),

                Field::make('text', 'classnames', __('Additional CSS Classnames', 'nh'))
                    ->set_default_value('gap-[30px]')
            ])
            ->set_icon('awards')
            ->set_keywords(['footer', 'achievements', 'cards', 'cta'])
            ->set_description(__('Generic footer card block, useful for achievements or any post type with optional CTA.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ Members Section ******************/ 
        //********************************************************** */
        Block::make(__('Members Section', 'nh'))
            ->add_fields([
                Field::make('association', 'members', __('Select Members', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'members',
                        ]
                    ])
                    ->set_help_text('Choose members to display in this section.')
            ])
            ->set_icon('groups')
            ->set_keywords(['members', 'team', 'associations'])
            ->set_description(__('Displays selected team or organization members.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),



        //********************************************************** */
        //************************ Achievement Cards Section ******************/ 
        //********************************************************** */
        Block::make(__('Achievement Cards Section', 'nh'))
            ->add_fields([
                Field::make('association', 'achievements', __('Select Achievements', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'achievements',
                        ]
                    ])
                    ->set_help_text('Choose achievement entries to show in this section.')
            ])
            ->set_icon('awards')
            ->set_keywords(['achievements', 'awards', 'cards'])
            ->set_description(__('Displays a list of selected achievements.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ Services Cards Section ******************/ 
        //********************************************************** */
        Block::make(__('Services Cards Section', 'nh'))
            ->add_fields([
                Field::make('association', 'services', __('Select Services', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'services',
                        ]
                    ])
                    ->set_help_text('Choose services entries to show in this section.')
            ])
            ->set_icon('awards')
            ->set_keywords(['services', 'awards', 'cards'])
            ->set_description(__('Displays a list of selected Services.'))
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),

        //********************************************************** */
        //************************ Featured News & Media Section ******************/ 
        //********************************************************** */
        Block::make(__('Featured News & Media Section', 'nh'))
            ->set_icon('media-document')
            ->set_category('nh-blocks', __('NH Blocks', 'nh'))
            ->add_fields([
                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('News & Media'),

                Field::make('association', 'featured_news', __('Featured News', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'blogs', // Change if your post type is different
                        ]
                    ])
                    ->set_max(4)
                    ->set_help_text('Select up to 4 blog posts to feature in this section.')
            ])
            ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),


        //********************************************************** */
        //************************ News Card Section ******************/ 
        //********************************************************** */
        Block::make(__('Blogs And News Card Section', 'nh'))
        ->set_icon('media-document')
        ->set_category('nh-blocks', __('NH Blocks', 'nh'))
        ->add_fields([
            Field::make('text', 'section_title', __('Section Title', 'nh')),
            Field::make('text', 'section_description', __('Section Description', 'nh')),
            Field::make('select', 'card_type', __('Select Card Type', 'nh'))
                ->add_options([
                    'news' => 'News Card',
                    'blog' => 'Blog Card',
                ])
                ->set_help_text('If it is for news section select News Card, else select Blog Card.'),
            Field::make('association', 'selected_posts', __('News or Blog Posts', 'nh'))
                ->set_types([
                    [
                        'type' => 'post',
                        'post_type' => 'blogs',
                    ]
                ])
                ->set_help_text('Select posts to show in the card section.'),
            Field::make('checkbox', 'load_more', __('Enable Load More Button', 'nh'))
                ->set_option_value('yes')
                ->set_default_value('yes'),
            Field::make('checkbox', 'enable_sorting', __('Enable Sorting Option', 'nh'))
                ->set_option_value('yes')
                ->set_default_value(''),
            Field::make('text', 'post_per_page', __('Posts Per Page', 'nh'))
                ->set_default_value(9)
                ->set_help_text('Number of posts to display initially.'),
        ])
        ->set_render_callback(function ($fields, $attributes, $inner_blocks) {}),
    


        //********************************************************** */
        //************************ CSR Image Gallery Section ******************/ 
        //********************************************************** */
        Block::make(__('CSR Image Gallery Section', 'nh'))
            ->set_icon('format-gallery')
            ->set_category('nh-blocks', __('NH Blocks', 'nh'))
            ->add_fields([
                Field::make('complex', 'images', __('Gallery Images', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->set_max(6)
                    ->add_fields([
                        Field::make('image', 'gallery_image', __('Image', 'nh'))
                        ->set_value_type('url'),
                    ])
                    ->set_help_text('Add up to 6 images for the CSR image gallery.')
            ])
            ->set_render_callback(function () {})
        //********************************************************** */
        //************************ END OF BLOCKS ******************/ 
        //********************************************************** */
    );


    // Theme Options
    WpGraphQLCrbContainer::register(
        Container::make('term_meta', __('Custom Data', 'app'))
            ->where('term_taxonomy', '=', 'category')
            ->add_fields([
                Field::make('image', 'crb_img')
                    ->set_value_type('url')
            ])
    );
    WpGraphQLCrbContainer::register(
        Container::make('theme_options', __('Theme Options'))
            ->add_fields(array(
                Field::make('text', 'contact_email', __('Enter Your Contact Email', 'nh')),
                Field::make('text', 'main_website_url', __('Main Website Url', 'nh')),
                Field::make('textarea', 'blank_post_types', 'Post Types with Blank URL (comma separated)'),
                Field::make('complex', 'custom_post_type_urls', 'Custom URLs for Post Types')
                    ->add_fields([
                        Field::make('text', 'post_type', 'Post Type Slug'),
                        Field::make('text', 'url', 'Custom URL Base'),
                    ]),
            ))
    );

    // HOW TO GET THESE THEME OPTION VALUES 
    // carbon_get_theme_option('value_name');
}