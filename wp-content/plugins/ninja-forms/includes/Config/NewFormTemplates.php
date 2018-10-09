<?php if ( ! defined( 'ABSPATH' ) ) exit;

/**
    'slug_name' => array(
        //Same as slug name/
        'id'            => 'slug_name',
        'title'         => 'nicename',
        'template-desc' => '',
    ),
 */

$templates = array(
    /**
     * Regular old form templates
     */

    'formtemplate-contactform'          => array(
        'id'                            => 'formtemplate-contactform',
        'title'                         => __( 'Contact Us', 'ninja-forms' ),
        'template-desc'                 => __( 'Allow your users to contact you with this simple contact form. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-quoterequest'         => array(
        'id'                            => 'formtemplate-quoterequest',
        'title'                         => __( 'Quote Request', 'ninja-forms' ),
        'template-desc'                 => __( 'Manage quote requests from your website easily with this template. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-eventregistration'    => array(
        'id'                            => 'formtemplate-eventregistration',
        'title'                         => __( 'Event Registration', 'ninja-forms' ),
        'template-desc'                 => __( 'Allow user to register for your next event this easy to complete form. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-enquiry'              => array(
        'id'                            => 'formtemplate-enquiry',
        'title'                         => __( 'General Enquiry', 'ninja-forms' ),
        'template-desc'                 => __( 'Collect user enquiries with this simple, generic form. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-feedback'             => array(
        'id'                            => 'formtemplate-feedback',
        'title'                         => __( 'Collect feedback', 'ninja-forms' ),
        'template-desc'                 => __( 'Collect feedback for an event, blog post, or anything else. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-questionnaire'        => array(
        'id'                            => 'formtemplate-questionnaire',
        'title'                         => __( 'Questionnaire', 'ninja-forms' ),
        'template-desc'                 => __( 'Collect information about your users. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-jobapplication'        => array(
        'id'                            => 'formtemplate-jobapplication',
        'title'                         => __( 'Job Application', 'ninja-forms' ),
        'template-desc'                 => __( 'Allow users to apply for a job. You can add and remove fields as needed.', 'ninja-forms' ),
    ),

    'formtemplate-deletedata'        => array(
	    'id'                            => 'formtemplate-deletedata',
	    'title'                         => __( 'Delete Data Request', 'ninja-forms' ),
	    'template-desc'                 => __( 'Includes action to add users to WordPress\' personal data delete tool, allowing admins to comply with the GDPR and other privacy regulations from the site\'s front end.', 'ninja-forms' ),
    ),

    'formtemplate-exportdata'        => array(
	    'id'                            => 'formtemplate-exportdata',
	    'title'                         => __( 'Export Data Request', 'ninja-forms' ),
	    'template-desc'                 => __( 'Includes action to add users to WordPress\' personal data export tool, allowing admins to comply with the GDPR and other privacy regulations from the site\'s front end.', 'ninja-forms' ),
    ),
);

$ads = array(
    /**
     * Ads
     */

    'mailchimp-signup'                  => array(
        'id'                            => 'mailchimp-signup',
        'title'                         => __( 'MailChimp Signup', 'ninja-forms' ),
        'template-desc'                 => __( 'Add a user to a list in MailChimp. You can add and remove fields as needed.', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get MailChimp for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <img src="' . Ninja_Forms::$url . 'assets/img/add-ons/mailchimp-for-ninja-forms.png"/>
                                                <p>In order to use this template, you need MailChimp for Ninja Forms.</p>
                                                <p>The MailChimp extension allows you to quickly create newsletter signup forms for your MailChimp account using the power and flexibility that Ninja Forms provides.</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/mail-chimp/?utm_medium=plugin&amp;utm_source=dashboard&amp;utm_campaign=New+Form+Templates&utm_content=MailChimp" title="MailChimp" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),

    'stripe-payment'                    => array(
        'id'                            => 'stripe-payment',
        'title'                         => __( 'Stripe Payment', 'ninja-forms' ),
        'template-desc'                 => __( 'Collect a payment using Stripe. You can add and remove fields as needed.', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get Stripe for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <div class="video-wrapper"><iframe src="https://www.youtube.com/embed/WdFmgAffA50" allowfullscreen="" name="fitvid0" frameborder="0"></iframe></div>
                                                <p>In order to use this template, you need Stripe for Ninja Forms.</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/stripe/?utm_medium=plugin&utm_source=dashboard&utm_campaign=New+Form+Templates&utm_content=Stripe" title="Stripe" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),

    'file-upload'                       => array(
        'id'                            => 'file-upload',
        'title'                         => __( 'File Upload', 'ninja-forms' ),
        'template-desc'                 => __( 'Allow users to upload files with their forms. You can add and remove fields as needed.', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get File Uploads for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <div class="video-wrapper"><iframe src="https://www.youtube.com/embed/Tl91cuFsnvM" allowfullscreen="" name="fitvid0" frameborder="0"></iframe></div>
                                                <p>In order to use this template, you need File Uploads for Ninja Forms.</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/stripe/?utm_medium=plugin&utm_source=dashboard&utm_campaign=New+Form+Templates&utm_content=File+Uploads" title="File Uploads" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),

    'paypal-payment'                    => array(
        'id'                            => 'paypal-payment',
        'title'                         => __( 'PayPal Payment', 'ninja-forms' ),
        'template-desc'                 => __( 'Collect a payment using PayPal Express. You can add and remove fields as needed.', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get PayPal Express for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <img src="' . Ninja_Forms::$url . 'assets/img/add-ons/paypal-express.png"/>
                                                <p>In order to use this template, you need PayPal Express for Ninja Forms.</p>
                                                <p>PayPal Express allows you to accept payments using Ninja Forms. It leverages the powerful processing engine that runs each Ninja Form to get a total, perform a checkout, and send your users to PayPal to complete their transaction.</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/paypal-express/?utm_medium=plugin&utm_source=dashboard&utm_campaign=New+Form+Templates&utm_content=PayPal+Express" title="PayPal Express" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),

    'create-post'                       => array(
        'id'                            => 'create-post',
        'title'                         => __( 'Create a Post', 'ninja-forms' ),
        'template-desc'                 => __( 'Allow users to create posts from the front-end using a form, including custom post meta!', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get Front-End Posting for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <img src="' . Ninja_Forms::$url . 'assets/img/add-ons/front-end-posting.png"/>
                                                <p>In order to use this template, you need Front-End Posting for Ninja Forms.</p>
                                                <p>Create posts, pages, or any custom post type from the front-end.</p>
                                                <p>The Ninja Forms Front-End Posting extension gives you the power of the WordPress post editor on any publicly viewable page you choose.</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/front-end-posting/?utm_medium=plugin&utm_source=dashboard&utm_campaign=New+Form+Templates&utm_content=Frontend+Posting" title="Create Post" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),

    'register-user'                     => array(
        'id'                            => 'register-user',
        'title'                         => __( 'Register a User', 'ninja-forms' ),
        'template-desc'                 => __( 'Register a WordPress User', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get User Management for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <img src="' . Ninja_Forms::$url . 'assets/img/add-ons/user-management.png"/>
                                                <p>In order to use this template, you need User Management for Ninja Forms.</p>
                                                <p>User Management brings you the remarkable flexibility to register new WordPress users and manage existing ones through your Ninja Forms!</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/user-management/?utm_medium=plugin&utm_source=dashboard&utm_campaign=New+Form+Templates&utm_content=User+Management" title="User Management" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),

    'update-profile'                    => array(
        'id'                            => 'update-profile',
        'title'                         => __( 'Edit User Profile', 'ninja-forms' ),
        'template-desc'                 => __( 'Allow WordPress users to edit their profiles from the front-end, including custom user meta!', 'ninja-forms' ),
        'type'                          => 'ad',
        'modal-title'                   => 'Get User Management for Ninja Forms',
        'modal-content'                 => '<div class="modal-template">
                                                <img src="' . Ninja_Forms::$url . 'assets/img/add-ons/user-management.png"/>
                                                <p>In order to use this template, you need User Management for Ninja Forms.</p>
                                                <p>User Management brings you the remarkable flexibility to register new WordPress users and manage existing ones through your Ninja Forms!</p>
                                                <div class="actions">
                                                    <a target="_blank" href="https://ninjaforms.com/extensions/user-management/?utm_medium=plugin&utm_source=dashboard&utm_campaign=New+Form+Templates&utm_content=User+Management" title="User Management" class="primary nf-button">Learn More</a>
                                                </div>
                                            </div>',
    ),
);

/**
 * If we've disabled marketing using our filter, don't merge in our ads.
 */
$disable_marketing = false;
if ( ! apply_filters( 'ninja_forms_disable_marketing', $disable_marketing ) ) {
    $templates = array_merge( $templates, $ads );
}

return apply_filters( 'ninja_forms_new_form_templates', $templates );