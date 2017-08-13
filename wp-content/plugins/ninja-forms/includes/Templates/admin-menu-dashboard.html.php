<!-- Application Root Element -->
<div class="wrap">
    <div id="ninja-forms-dashboard"></div>
</div>

<!-- Dashboard -->
<script id="tmpl-nf-dashboard" type="text/template">
    <header class="topbar">
        <div class="app-title">
            <strong><?php _e( 'Ninja Forms Dashboard', 'ninja-forms' ); ?></strong>
        </div>
    </header>
    <div class="notices"></div>
    <nav class="sections">
        <ul>
            {{{ data.renderNav() }}}
        </ul>
    </nav>
    <main class="content"></main>
</script>

<!-- Auth Notice -->
<script id="tmpl-nf-auth-notice" type="text/template">
    [AUTH GOES HERE]
    <div id="auth-notice"></div>
</script>

<!-- Section: Widgets -->
<script id="tmpl-nf-widgets" type="text/template">
    <div class="widget widget-forms"></div>
</script>

<!-- Section: Apps -->
<script id="tmpl-nf-apps" type="text/template">
    <!-- TODO: Create static template -->
    <?php
     Ninja_Forms()->menus['add-ons']->display();
    ?>
</script>

<!-- Section: Memberships -->
<script id="tmpl-nf-memberships" type="text/template">
    <div class="widget widget-memberships">
        <div class="pricing-container">
            <div class="pricing-block widget">
                <div class="pricing-header">
                    <div class="pricing-title"><?php _e( 'Agency', 'ninja-forms' ); ?></div>
                    <div class="pricing-price">$499</div>
                    <div class="pricing-savings"><?php _e( 'Save', 'ninja-forms' ); ?> $2000+</div>
                    <div class="pricing-cta"><a target="_blank" href="https://ninjaforms.com/checkout/?edd_action=add_to_cart&download_id=203757&utm_medium=plugin&utm_source=plugin-dashboard&utm_campaign=Ninja+Forms+Memberships&utm_content=Agency+Membership" class="nf-button primary"><?php _e( 'Buy Now', 'ninja-forms' ); ?></a></div>
                </div>
                <div class="pricing-body">
                    <div>
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'Unlimited Sites', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Updates & Support', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( '50% off recurring renewals', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Renews at $249.50/year', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-rocket" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'Fastest Support', 'ninja-forms' ); ?></span>
                        <span><?php _e( '1 Year of Updates & Support', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-trophy" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'All add-ons included!', 'ninja-forms' ); ?></span>
                        <ul>
                            <li><?php _e( 'Builder PRO Pack', 'ninja-forms' ); ?></li>
                            <li><?php _e( 'PDF Form Submissions', 'ninja-forms' ); ?></li>
                            <li><?php _e( 'Zapier', 'ninja-forms' ); ?></li>
                            <li><?php _e( 'Newsletters (MailChimp, etc.)', 'ninja-forms' ); ?></li>
                            <li><?php _e( 'CRMs (Saleforce, etc.)', 'ninja-forms' ); ?></li>
                            <li><?php _e( 'Payments (PayPal, etc.)', 'ninja-forms' ); ?></li>
                            <li><?php _e( 'And so much more...', 'ninja-forms' ); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="pricing-block widget highlight">
                <!-- <div class="callout">
                    Most Popular
                </div> -->
                <div class="pricing-header">
                    <div class="pricing-title"><?php _e( 'Professional', 'ninja-forms' ); ?></div>
                    <div class="pricing-price">$199</div>
                    <div class="pricing-savings"><?php _e( 'Save $197', 'ninja-forms' ); ?></div>
                    <div class="pricing-cta"><a target="_blank" href="https://ninjaforms.com/checkout/?edd_action=add_to_cart&download_id=471355&utm_medium=plugin&utm_source=plugin-dashboard&utm_campaign=Ninja+Forms+Memberships&utm_content=Professional+Membership" class="nf-button primary"><?php _e( 'Buy Now', 'ninja-forms' ); ?></a></div>
                </div>
                <div class="pricing-body">
                    <div>
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( '20 Sites', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Updates & Support', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( '50% off recurring renewals', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Renews at $99.50/year', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-plane" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'Faster Support', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-wrench" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'Builder Pro Pack', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Layout & Styles, Multi-Part Forms, Conditional Logic, File Uploads', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-percent" aria-hidden="true"></i>
                        <span>Plus <strong>40% off</strong><br /><?php _e( 'Additional Add-Ons', 'ninja-forms' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="pricing-block widget">
                <div class="pricing-header">
                    <div class="pricing-title"><?php _e( 'Personal', 'ninja-forms' ); ?></div>
                    <div class="pricing-price">$99</div>
                    <div class="pricing-savings"><?php _e( 'Save $57', 'ninja-forms' ); ?></div>
                    <div class="pricing-cta"><a target="_blank" href="https://ninjaforms.com/checkout/?edd_action=add_to_cart&download_id=471356&utm_medium=plugin&utm_source=plugin-dashboard&utm_campaign=Ninja+Forms+Memberships&utm_content=Personal+Membership" class="nf-button primary"><?php _e( 'Buy Now', 'ninja-forms' ); ?></a></div>
                </div>
                <div class="pricing-body">
                    <div>
                        <i class="fa fa-user" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( '1 Site', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Updates & Support', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-money" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( '50% off recurring renewals', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Renews at $49.50/year', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-car" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'Fast Support', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-wrench" aria-hidden="true"></i>
                        <span class="pricing-body-title"><?php _e( 'Builder Pro Pack', 'ninja-forms' ); ?></span>
                        <span><?php _e( 'Layout & Styles, Multi-Part Forms, Conditional Logic, File Uploads', 'ninja-forms' ); ?></span>
                    </div>
                    <div>
                        <i class="fa fa-percent" aria-hidden="true"></i>
                        <span>Plus <strong>20% off</strong><br /><?php _e( 'Additional Add-Ons', 'ninja-forms' ); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="widget widget-plan-notice">
        <p class="widget-title"><?php _e( 'That sounds amazing! What else comes with Ninja Forms?', 'ninja-forms' ); ?></p>
        <a href="https://ninjaforms.com/features/?utm_medium=plugin&utm_source=plugin-dashboard&utm_campaign=Ninja+Forms+Memberships&utm_content=Features" target="_blank" class="nf-button primary feature-list-link"><?php _e( 'We\'re glad you asked! Checkout our full list of features!', 'ninja-forms' ); ?> <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
        <div><em><?php _e( 'All plans include 50% discount on automatic renewals, and a 14 day money back guarantee.', 'ninja-forms' ); ?></em></div>
        <div><?php _e( 'Requires a current active license and subject to our', 'ninja-forms' ); ?> <a target="_blank" href="https://ninjaforms.com/terms-conditions/?utm_medium=plugin&utm_source=plugin-dashboard&utm_campaign=Ninja+Forms+Memberships&utm_content=Terms+Conditions"><?php _e( 'Terms & Conditions', 'ninja-forms' ); ?></a>.</div>
    </div>
</script>

<!-- Widget: Forms -->
<script id="tmpl-nf-widget-forms" type="text/template">
    <header>
        <div class="action">
            <button class="add nf-button primary"><?php _e( 'Add New', 'ninja-forms' ); ?></button>
            <button class="cancel nf-button secondary"><?php _e( 'Cancel', 'ninja-forms' ); ?></button>
        </div>
        <div class="filter nf-search"></div>
    </header>
    <main class="content"></main>
</script>

<!-- Widget: Forms - Filter -->
<script id="tmpl-nf-widget-forms-filter" type="text/template">
    <input class="nf-filter" type="search" placeholder="<?php _e( 'Search Forms', 'ninja-forms' ); ?>">
</script>

<!-- Widget: Forms - Table -->
<script id="tmpl-nf-widget-forms-table" type="text/template">
    <thead>
        <th class="sortable th-title" data-sort="title"><?php _e( 'Title', 'ninja-forms' ); ?></th>
        <th class="th-shortcode"><?php _e( 'Shortcode', 'ninja-forms' ); ?></th>
        <th class="sortable th-created" data-sort="created_at"><?php _e( 'Date Created', 'ninja-forms' ); ?></th>
        <th></th>
    </thead>
    <tbody class="content">
        <?php _e( 'Loading Forms...', 'ninja-forms' ); ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="action2">
                <button class="more"><?php _e( 'More', 'ninja-forms' ); ?></button>
                <button class="less"><?php _e( 'Less', 'ninja-forms' ); ?></button>
            </td>
        </tr>
    </tfoot>
</script>
<script id="tmpl-nf-widget-forms-table-row" type="text/template">
    <td class="">
        <span class="title">
            <a href="admin.php?page=ninja-forms&form_id={{{ data.id }}}">{{{ data.title }}}</a>
        </span>
        <ul class="form-row-actions">
            <li><a href="admin.php?page=ninja-forms&form_id={{{ data.id }}}"><?php _e( 'Edit', 'ninja-forms' ); ?></a></li>
            <li><a class="duplicate"><?php _e( 'Duplicate', 'ninja-forms' ); ?></a></li>
            <li><a href="<?php print( get_home_url() ); ?>/?nf_preview_form={{{ data.id }}}" target="_blank"><?php _e( 'Preview Form', 'ninja-forms' ); ?></a></li>
            <li><a href="edit.php?post_status=all&post_type=nf_sub&form_id={{{ data.id }}}" target="_blank"><?php _e( 'View Submissions', 'ninja-forms' ); ?></a></li>
            <li><a class="delete"><?php _e( 'Delete', 'ninja-forms' ); ?></a></li>
        </ul>
    </td>
    <td>
        {{{ data.shortcode }}}
    </td>
    <td>
        {{{ data.created_at }}}
    </td>
    <td><div class="nf-item-controls">
    <div class="nf-item-edit nf-item-control"><a title="Edit"><i class="nf-edit-settings fa fa-cog" aria-hidden="true"></i></div></div>
</div></div></td>
</script>
<script id="tmpl-nf-widget-forms-table-empty" type="text/template">
    <td colspan="4"><?php _e( 'No Forms', 'ninja-forms' ); ?></td>
</script>
<script id="tmpl-nf-widget-forms-table-loading" type="text/template">
    <td colspan="4"><?php _e( 'Loading Forms', 'ninja-forms' ); ?></td>
</script>

<!-- Widget: Forms - New Forms Templates -->
<script id="tmpl-nf-widget-forms-template" type="text/template">
    <div class="template">
        <a href="admin.php?page=ninja-forms&form_id={{{ data.id }}}">
            <strong class="title">{{{ data.title }}}</strong>
            <div class="desc">{{{ data.desc }}}</div>
        </a>
    </div>
</script>

<!-- Content Template -->
<script id="tmpl-nf-content" type="text/template">
    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ab aliquid cupiditate ducimus fugit illo itaque maxime nihil perferendis praesentium voluptates. Aperiam culpa delectus distinctio illo ipsum officia, officiis pariatur quasi.</p>
</script>
