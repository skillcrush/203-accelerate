<!-- Application Root Element -->
<div class="wrap">
    <div id="ninja-forms-dashboard"></div>
</div>

<!-- Dashboard -->
<script id="tmpl-nf-dashboard" type="text/template">
    <header class="topbar">
        <div class="app-title">
            <strong><?php esc_html_e( 'Ninja Forms Dashboard', 'ninja-forms' ); ?></strong>
        </div>
    </header>
    <div class="notices"></div>
    <div class="promotions"></div>
    <nav class="sections">
        <ul>
            {{{ data.renderNav() }}}
        </ul>
    </nav>
    <main class="content"></main>
</script>

<!-- OAuth -->
<script id="tmpl-nf-notices-oauth" type="text/template">
  <# if( null !== data.connected ) { #>
    <# if( ! data.connected ){ #>
    <!-- <a href="{{{ data.connect_url }}}" class="nf-oauth--connect">
      Connect to My.NinjaForms.com
    </a> -->
    <# } else { #>
    <div class="nf-oauth--connected">
      Connected to My.NinjaForms.com &nbsp; <span style="cursor:pointer;" onclick="Backbone.Radio.channel( 'dashboard' ).request( 'oauth:learn-more' );">Learn More</span>
      <span class="js--disconnect" style="float:right;cursor:pointer;">
        <span class="dashicons dashicons-no"></span>
      </span>
    </div>
    <# } #>
  <# } else { #>
    <div class="nf-oauth--checking">
      Checking connection...
    </div>
  <# } #>
</script>

<!-- Promotion -->
<script id="tmpl-nf-promotion" type="text/template">
    <div
      class="promotion--wrapper"
      <# if( data.script ){ #>
      onclick="{{{data.script}}}"
      <# } #>
      >
      <div class="promotion--{{{ data.id }}}">
        {{{ data.content }}}
      </div>
    </div>
</script>

<!-- Section: Widgets -->
<script id="tmpl-nf-widgets" type="text/template">
    <div class="widget widget-forms"></div>
</script>

<!-- Section: Services -->
<script id="tmpl-nf-services" type="text/template">
  <div class="services"></div>
</script>
<script id="tmpl-nf-service" type="text/template">
  <div class="nf-box-inside" style="padding:10px 20px;">
    <h2>{{{ data.name }}}</h2>
    <div class="nf-extend-content">
      <p>{{{ data.description }}}</p>
      <div class="nf-extend-buttons">
        <# if( data.is_connected ){ #>
          <# if( null !== data.enabled){ #>
          <div style="float: left;">
            <input id="nfServiceTransactionalEmail" class="nf-toggle setting" {{{ ( data.enabled ) ? 'checked="checked"' : '' }}} {{{ ( data.isUpdating ) ? 'disabled="disabled"' : '' }}} type="checkbox">
            <label for="nfServiceTransactionalEmail"></label>
          </div>
          <# } #>
          <# if( data.serviceLink ){ #>
            <a
              href="{{{ data.serviceLink.href }}}"
              class="{{{data.serviceLink.classes}}}"
              <# if( data.serviceLink.target ) { #>target="{{{ data.serviceLink.target }}}"<# } #>
              style="float:right;">{{{data.serviceLink.text}}}</a>
          <# } #>
        <# } #>
        <# if( data.learnMore ) { #>
        <button class="nf-button secondary js--learn-more" style="float:left;">Learn More</button>
        <# } #>
        <# if( ( ! data.is_connected ) || ( data.slug && data.installPath ) ){ #>
          <# if( ! data.is_installing ){ #>
            <a href="#services" class="nf-button primary js--install" style="float:right;">
              <# if( data.setupButtonText ){ #>
                {{{ data.setupButtonText }}}
              <# } else { #>
                <?php esc_html_e( 'Setup', 'ninja-forms' ); ?>
              <# } #>
            </a>
          <# } else { #>
            <a href="#services" class="nf-button primary" style="float:right;" disabled>
              <span class="dashicons dashicons-update dashicons-update-spin"></span>
            </a>
          <# } #>
        <# } #>
      </div>
    </div>
  </div>
</script>


<!-- Section: apps and Integrations -->
<script id="tmpl-nf-apps" type="text/template">

    <div class="widget widget-plan-notice">
        <p class="widget-title">What else comes with Ninja Forms?</p>
        <a class="nf-button primary feature-list-link" href="https://ninjaforms.com/features/?utm_source=Ninja+Forms+Plugin&utm_medium=Apps+and+Integrations&utm_campaign=Dashboard+Features+Link" target="_blank" rel="noopener">We're glad you asked! Checkout our full list of features! <i class="fa fa-chevron-circle-right" aria-hidden="true"></i></a>
        <div><em>All plans include a 14 day money back guarantee.</em></div>
        <div>Requires a current active license and subject to our <a href="https://ninjaforms.com/terms-conditions/?utm_medium=plugin&utm_source=plugin-dashboard&utm_campaign=Ninja+Forms+Memberships&utm_content=Terms+Conditions" target="_blank" rel="noopener">Terms &amp; Conditions</a>.</div>
    </div>

    <?php Ninja_Forms()->menus[ 'add-ons']->display(); ?>
</script>

<!-- Section: Required Updates -->
<script id="tmpl-nf-requiredUpdates" type="text/template">
    <div>
        <h1><?php esc_html_e( 'Required Updates', 'ninja-forms' ); ?></h1>
        <div>
            <p>
                <?php esc_html_e( "Ninja Forms needs to run some updates on your installation before you can continue. You'll be able to create and edit forms after the updates listed below have completed.", 'ninja-forms' ); ?>
            </p>
            <p>
                <?php esc_html_e( "Normally, users will still be able to view and submit forms while these updates take place. If an update needs to modify database information, we'll put the affected form in maintenance mode until we get done with that update.", 'ninja-forms' ); ?>
            </p>
            <p>
                <?php esc_html_e( "It's always a good idea to have an up to date backup of your WordPress site on hand. That's especially true when you run plugin and theme updates. Luckily, there are plenty of good backup plugins available.", 'ninja-forms' ); ?>
            </p>
            <p>
                <?php esc_html_e( "When you're ready, just click the \"Do Required Updates\" button below to get started. You'll be able to create and edit forms in no time.", 'ninja-forms' ); ?>
            </p>
        </div>
        <div id="nfUpgradeApp">
            <table id="nf-upgrades-table">
                <thead>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div>
            <input class="nf-required-update nf-update-button" type='button' id='nf-required-updates-btn' name='nf-required-updates-btn' value="<?php esc_html_e( 'Do Required Updates' ); ?>" />
        </div>
        <div class="nf-update-progress jBox-content" id="nf-required-updates-progress"></div>
    </div>
</script>

<!-- Widget: Forms -->
<script id="tmpl-nf-widget-forms" type="text/template">
    <header>
        <div class="action">
            <button class="add nf-button primary"><?php esc_html_e( 'Add New', 'ninja-forms' ); ?></button>
            <button class="cancel nf-button secondary"><?php esc_html_e( 'Cancel', 'ninja-forms' ); ?></button>
        </div>
        <div class="filter nf-search"></div>
    </header>
    <main class="content"></main>
</script>

<!-- Widget: Forms - Filter -->
<script id="tmpl-nf-widget-forms-filter" type="text/template">
    <input class="nf-filter" type="search" placeholder="<?php esc_html_e( 'Search Forms', 'ninja-forms' ); ?>">
</script>

<!-- Widget: Forms - Table -->
<script id="tmpl-nf-widget-forms-table" type="text/template">
    <thead>
        <th class="sortable th-title" data-sort="title"><?php esc_html_e( 'Title', 'ninja-forms' ); ?></th>
        <th class="sortable th-shortcode" data-sort="shortcode"><?php esc_html_e( 'Shortcode', 'ninja-forms' ); ?></th>
        <th class="sortable th-created" data-sort="created_at"><?php esc_html_e( 'Date Created', 'ninja-forms' ); ?></th>
        <th></th>
    </thead>
    <tbody class="content">
        <?php esc_html_e( 'Loading Forms...', 'ninja-forms' ); ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" class="action2">
                <button class="more"><?php esc_html_e( 'More', 'ninja-forms' ); ?></button>
                <button class="less"><?php esc_html_e( 'Less', 'ninja-forms' ); ?></button>
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
            <li><a href="admin.php?page=ninja-forms&form_id={{{ data.id }}}"><?php esc_html_e( 'Edit', 'ninja-forms' ); ?></a></li>
            <li><a class="duplicate"><?php esc_html_e( 'Duplicate', 'ninja-forms' ); ?></a></li>
            <li><a href="<?php print( get_home_url() ); ?>/?nf_preview_form={{{ data.id }}}" target="_blank"><?php esc_html_e( 'Preview Form', 'ninja-forms' ); ?></a></li>
            <# if(data.public_link_key) { #>
            <li><a href="<?php
                global $wp_rewrite;
                if($wp_rewrite->permalink_structure) {
                    echo site_url() . '/ninja-forms/{{{ data.public_link_key }}}';
                } else {
                    echo site_url('?nf_public_link={{{ data.public_link_key }}}');
                }
                ?>"><?php esc_html_e( 'Public Link', 'ninja-forms' ); ?></a></li>
            <# } #>
            <li><a href="edit.php?post_status=all&post_type=nf_sub&form_id={{{ data.id }}}" target="_blank"><?php esc_html_e( 'View Submissions', 'ninja-forms' ); ?></a></li>
            <li><a class="delete"><?php esc_html_e( 'Delete', 'ninja-forms' ); ?></a></li>
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
    <td colspan="4"><?php esc_html_e( 'No Forms', 'ninja-forms' ); ?></td>
</script>
<script id="tmpl-nf-widget-forms-table-loading" type="text/template">
    <td colspan="4"><?php esc_html_e( 'Loading Forms', 'ninja-forms' ); ?></td>
</script>

<!-- Widget: Forms - New Forms Templates -->
<script id="tmpl-nf-widget-forms-template" type="text/template">
    <div class="template {{{ data.type }}}">
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
