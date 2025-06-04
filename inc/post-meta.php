<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;
use Carbon_Fields\Block;
use WpGraphQLCrb\Container as WpGraphQLCrbContainer;

function Postmeta()
{

    // NAVIGATION MENU 
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', 'Navigation Builder')
            ->where('post_type', '=', 'custom_navigations')
            ->add_fields([
                // Logos
                Field::make('complex', 'logo', 'Logos')
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('image', 'primary_logo', 'Primary Logo')->set_value_type('url'),
                        Field::make('image', 'secondary_logo', 'Secondary Logo')->set_value_type('url'),
                    ]),

                // Navigation Items
                Field::make('complex', 'nav_items', 'Navigation Items')
                    ->set_layout('tabbed-horizontal')
                    ->add_fields([
                        Field::make('text', 'label', 'Label')->set_required(true),
                        Field::make('text', 'link', 'Link')->set_required(true),
                        Field::make('complex', 'children', 'Dropdown Items')
                            ->add_fields([
                                Field::make('text', 'label', 'Label')->set_required(true),
                                Field::make('text', 'link', 'Link')->set_required(true),
                            ]),
                    ]),

                // Contact Info (Navbar)
                Field::make('complex', 'contact_info', 'Contact Info')
                    ->add_fields([
                        Field::make('text', 'label', 'Label')->set_required(true),
                        Field::make('text', 'link', 'Link')->set_required(true),
                        Field::make('radio', 'type', __('Type', 'nh'))
                        ->add_options([
                            'link' => __('Link', 'nh'),
                            'number' => __('Number', 'nh'),
                        ])
                        ->set_required(true),
                 
                    ]),

                // Footer Links
                Field::make('complex', 'footer_links', 'Footer Links')
                    ->add_fields([
                        Field::make('text', 'label', 'Label')->set_required(true),
                        Field::make('text', 'link', 'Link')->set_required(true),
                    ]),

                // Footer Logo
                Field::make('image', 'footer_primary_logo', 'Footer Primary Logo')->set_value_type('url'),

                // Footer Company Info
                Field::make('text', 'footer_company_title', 'Company Title'),
                Field::make('textarea', 'footer_company_description', 'Company Description'),

                // Footer Contact Info
                Field::make('text', 'footer_address', 'Address'),
                Field::make('text', 'footer_email', 'Email'),
                Field::make('complex', 'footer_phone_numbers', 'Phone Numbers')
                    ->add_fields([
                        Field::make('text', 'number', 'Phone Number')
                    ]),
                Field::make('complex', 'footer_social_links', 'Social Links')
                    ->add_fields([
                        Field::make('text', 'platform', 'Platform')->set_required(true),
                        Field::make('text', 'link', 'Link')->set_required(true),
                    ]),

                // Copyright
                Field::make('text', 'footer_copyright', 'Copyright Text'),
            ])
    );


    // MEMBERS
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', 'Custom Data')
            ->where('post_type', '=', 'members')
            ->add_fields(array(
                Field::make('image', 'image', 'Thumbnail Image')
                    ->set_value_type('url')
                    ->set_required(true),
                Field::make('text', 'position', 'Position')
                    ->set_required(true),
                Field::make('rich_text', 'message', 'Message')
                    ->set_required(true),
            ))
    );

    // SERVICES 
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', __('Service Details', 'nh'))
            ->where('post_type', '=', 'services')
            ->add_fields([
                Field::make('textarea', 'service_description', __('Description', 'nh'))
                    ->set_required(true),
                Field::make('image', 'service_image', __('Service Thumbnail', 'nh'))
                    ->set_value_type('url')
                    ->set_required(true),
                Field::make('image', 'service_icon', __('Service Icon', 'nh'))
                    ->set_value_type('url')
                    ->set_required(true),
                Field::make('complex', 'service_details_sections', __('Service Details Sections', 'nh'))
                    ->set_layout('tabbed-horizontal')
                    ->set_required(true)
                    ->add_fields([
                        Field::make('text', 'section_title', __('Section Title', 'nh')),
                        Field::make('textarea', 'section_description', __('Section Description', 'nh')),

                        Field::make('complex', 'section_points', __('Section Points', 'nh'))
                            ->set_layout('tabbed-horizontal')
                            ->add_fields([
                                Field::make('text', 'point_text', __('Point', 'nh')),
                            ]),

                        Field::make('complex', 'section_cards', __('Section Cards', 'nh'))
                            ->set_layout('tabbed-horizontal')
                            ->add_fields([
                                Field::make('text', 'card_title', __('Card Title', 'nh')),
                                Field::make('textarea', 'card_description', __('Card Description', 'nh')),
                            ]),

                        Field::make('complex', 'section_gallery', __('Section Gallery Images', 'nh'))
                            ->set_layout('tabbed-horizontal')
                            ->add_fields([
                                Field::make('image', 'gallery_image', __('Gallery Image', 'nh'))
                                    ->set_value_type('url')
                            ]),
                    ]),
                Field::make('association', 'testimonials', __('Select testimonials', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'testimonials',
                        ]
                    ])
                    ->set_required(true)
                    ->set_help_text('Choose testimonials entries to show in this section.'),
                Field::make('association', 'top_clients', __('Select Clients for this service', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'clients',
                        ]
                    ])
                    ->set_required(true)
                    ->set_help_text('Choose clients entries to show in this section.'),
                Field::make('association', 'you_may_like_services', __('Select Related services for this service', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'services',
                        ]
                    ])
                    ->set_required(true)
                    ->set_help_text('Choose services entries to show in this section.'),
                Field::make('html', 'info_text')
                    ->set_html('<h2>Contact Details for this service</h2>'),

                Field::make('text', 'section_title', __('Section Title', 'nh'))
                    ->set_default_value('Ready to Enhance Your Security?'),

                Field::make('image', 'contact_background', __('Contact Section Background Image', 'nh'))
                    ->set_required(true)
                    ->set_value_type('url'),


                Field::make('textarea', 'address', __('Address', 'nh'))
                    ->set_rows(2)
                    ->set_required(true),
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
            ]),


    );

    // BLOGS 
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', __('Blogs', 'nh'))
            ->where('post_type', '=', 'blogs')
            ->add_fields([
                Field::make('radio', 'blog_type', __('Type', 'nh'))
                    ->add_options([
                        'news' => __('News', 'nh'),
                        'blog' => __('Blog', 'nh'),
                    ])
                    ->set_required(true),

                Field::make('rich_text', 'blogs_details', __('Blog Details', 'nh'))
                    ->set_required(true),

                Field::make('image', 'blog_image', __('Blog Thumbnail', 'nh'))
                    ->set_value_type('url')
                    ->set_required(true),

                Field::make('text', 'related_blogs_title', __('Related Blogs Section Title', 'nh'))
                    ->set_default_value('Similar Blogs')
                    ->set_required(true),
                Field::make('association', 'related_blogs', __('Select Related Blogs', 'nh'))
                    ->set_types([
                        [
                            'type' => 'post',
                            'post_type' => 'blogs',
                        ]
                    ])
                    ->set_required(true)
                    ->set_max(3)
                    ->set_help_text('Choose related blogs for this post or blog.'),
            ])

    );
    //ACHIEVEMENTS
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', 'Custom Data')
            ->where('post_type', '=', 'achievements')
            ->add_fields(array(
                Field::make('text', 'title', __('Achievement Title', 'nh'))
                    ->set_required(true),
                Field::make('text', 'year', __('Year', 'nh'))
                    ->set_required(true),
                Field::make('textarea', 'description', __('Description', 'nh'))
                    ->set_required(true),
                Field::make('image', 'image', __('Image', 'nh'))
                    ->set_value_type('url')
                    ->set_required(true),
            ))
    );

    //TESTIMONIALS
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', 'Custom Data')
            ->where('post_type', '=', 'testimonials')
            ->add_fields(array(
                Field::make('image', 'company_image', __('Company Image', 'nh'))
                    ->set_value_type('url')
                    ->set_required(true),
                Field::make('text', 'company_name', __('Company Name', 'nh'))
                    ->set_required(true),
                Field::make('textarea', 'description', __('Description', 'nh'))
                    ->set_required(true),
                Field::make('text', 'name', __('Name', 'nh'))
                    ->set_required(true),
                Field::make('text', 'position', __('Position', 'nh'))
                    ->set_required(true),

            ))
    );

    //CLIENTS
    WpGraphQLCrbContainer::register(
        Container::make('post_meta', 'Custom Data')
            ->where('post_type', '=', 'clients')
            ->add_fields(array(
                Field::make('image', 'client_image', __('Client Image', 'nh'))
                    ->set_value_type('url')
                    ->set_required(true),
                Field::make('text', 'client_name', __('Client Name', 'nh'))
                    ->set_required(true),
                Field::make('text', 'website', __('Client Website', 'nh'))
                    ->set_required(true),
            ))
    );

}

add_action('carbon_fields_register_fields', 'Postmeta');
