<div id="nf-pre-builder" style="background:#fff;position:fixed;top:0;right:0;bottom:0;left:0;z-index:9999999;"></div>

<div id="nf-builder" class="grey"></div>

<script id="tmpl-nf-builder" type="text/template">
    <div id="nf-app-admin-header">
        <div id="nf-logo"></div>
        <?php
        /*
         * TODO: Make this much more dynamic.
         */
        $nf_settings = get_option( 'ninja_forms_settings' );
        $disable_admin_notices = ( isset ( $nf_settings[ 'disable_admin_notices' ] ) ) ? $nf_settings[ 'disable_admin_notices' ] : false;

        if( ! function_exists( 'NF_Layouts' ) && ! $disable_admin_notices ) {
            $u_id = get_option( 'nf_aff', false );
            if ( !$u_id ) $u_id = apply_filters( 'ninja_forms_affiliate_id', false );
            $link = 'https://ninjaforms.com/extensions/layout-styles/?utm_medium=plugin&utm_source=plugin-builder&utm_campaign=Ninja+Forms+Builder&utm_content=Layout+and+Styles';
            if ( $u_id ) {
                $link = 'http://www.shareasale.com/r.cfm?u=' . $u_id . '&b=812237&m=63061&afftrack=&urllink=' . $link;
            }
        ?>
            <a href="<?php echo $link; ?>" target="_blank" class="nf-cta-bubble"><?php printf( __( "Create multi-column form layouts with Layout & Styles...%slearn more now!%s", 'ninja-forms' ), '<span>', '</span>' ); ?></a>
        <?php
        }
        ?>

        <a href="admin.php?page=ninja-forms" class="fa fa-times"></a></div>
    <div id="nf-overlay"></div>
    <div id="nf-header"></div>
    <div id="nf-main" class="nf-app-main"></div>
    <div id="nf-menu-drawer"></div>
    <div id="nf-drawer"></div>
    <span class="merge-tags-content" style="display:none;"></span>
    <div id="merge-tags-box"></div>
</script>

<!-- MERGE TAGS BOX TEMPLATES -->
<script id="tmpl-nf-merge-tag-box" type="text/template">
    <div class="merge-tag-filter"></div>
    <div class="merge-tag-container">
        <div class="merge-tag-sections"></div>
        <div class="merge-tag-list"></div>
    </div>
</script>
<script id="tmpl-nf-merge-tag-box-section" type="text/template">
    {{{ data.label }}}
</script>
<script id="tmpl-nf-merge-tag-box-tag" type="text/template">
    <span data-tag="{{{data.tag}}}">{{{ data.label }}} <small>{{{data.tag}}}</small></span>
</script>
<script id="tmpl-nf-merge-tag-box-filter" type="text/template">
    <input type="text" placeholder="Search for merge tags" >
</script>
<!-- END: MERGE TAGS BOX TEMPLATES -->

<script id="tmpl-nf-admin-header" type="text/template">
    <div id="nf-app-admin-header"></div>
</script>

<script id="tmpl-nf-header" type="text/template">
    <div id="nf-app-header"></div>
    <div id="nf-app-form-title"></div>
    <div id="nf-app-sub-header"></div>
</script>

<script id="tmpl-nf-header-form-title" type="text/template">
    <h2>{{{ data.renderTitle() }}}</h2>
</script>

<script id="tmpl-nf-sub-header-fields" type="text/template">
    <a class="nf-master-control nf-open-drawer" title="<?php _e( 'Add new field', 'ninja-forms' ); ?>" href="#" data-drawerid="addField">
        <i class="fa fa-plus" data-drawerid="addField" aria-hidden="true"></i>
        <span data-drawerid="addField"><?php _e( 'Add new field', 'ninja-forms' ); ?></span>
    </a>
</script>

<script id="tmpl-nf-sub-header-actions" type="text/template">
    <a class="nf-master-control nf-open-drawer" title="<?php _e( 'Add new action', 'ninja-forms' ); ?>" href="#" data-drawerid="addAction">
        <i class="fa fa-plus" data-drawerid="addAction" aria-hidden="true"></i>
        <span><?php _e( 'Add new action', 'ninja-forms' ); ?></span>
    </a>
</script>

<script id="tmpl-nf-sub-header-settings" type="text/template">

</script>

<script id="tmpl-nf-app-header" type="text/template">
    <!-- <div id="nf-logo"></div> -->
    <ul class="nf-app-menu"></ul>
    <span class="nf-mobile-menu-button"></span>
    <span class="nf-app-buttons"></span>
</script>

<script id="tmpl-nf-app-header-action-button" type="text/template">
    {{{ data.renderPublish() }}}
    {{{ data.maybeRenderCancel() }}}
</script>

<script id="tmpl-nf-mobile-menu-button" type="text/template">
    <a class="nf-button nf-mobile-menu" title="<?php _e( 'Expand Menu', 'ninja-forms' ); ?>" {{{ data.maybeDisabled() }}}" href="#"><span class="dashicons dashicons-editor-ul"></span></a>
</script>

<script id="tmpl-nf-app-header-publish-button" type="text/template">
    <a href="#" style="width:{{{ data.publishWidth }}} !important" class="nf-button primary {{{ data.maybeDisabled() }}} publish" title="<?php _e( 'Publish', 'ninja-forms' ); ?>"><?php _e( 'PUBLISH', 'ninja-forms' ); ?></a>
</script>

<script id="tmpl-nf-add-header-publish-loading" type="text/template">
    <a href="#" {{{ data.publishWidth }}} class="nf-button primary {{{ data.maybeDisabled() }}} publish" title="<?php _e( 'Loading', 'ninja-forms' ); ?>">
        <div class="nf-loading">
            <ul>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
    </a>
</script>

<script id="tmpl-nf-app-header-view-changes" type="text/template">
    <a class="nf-cancel viewChanges" title="<?php _e( 'View Changes', 'ninja-forms' ); ?>" style="text-decoration: none;" href="#"><span class="dashicons dashicons-backup"></span></a>
</script>

<script id="tmpl-nf-main" type="text/template">
    <div id="nf-main-content" class="nf-app-area">
        <div id="nf-main-gutter-left"></div>
        <div id="nf-main-body"></div>
        <div id="nf-main-gutter-right"></div>
    </div>
</script>

<script id="tmpl-nf-main-header-fields" type="text/template">
    <input class="nf-button secondary nf-change-domain" data-domain="actions" type="button" value="Edit Emails and Actions" />
</script>

<script id="tmpl-nf-main-header-actions" type="text/template">
    <input class="nf-button secondary nf-change-domain" data-domain="settings" type="button" value="Manage Settings" />
</script>

<script id="tmpl-nf-main-header-settings" type="text/template">

</script>

<script id="tmpl-nf-main-content-fields-empty" type="text/template">
    <div class="nf-fields-empty">
        <h3><?php _e( 'Add form fields', 'ninja-forms' ); ?></h3>
        <p><?php _e( 'Get started by adding your first form field.', 'ninja-forms' ); ?> <?php _e( "It's that easy.", 'ninja-forms' ); ?>
    </div>
</script>

<script id="tmpl-nf-main-content-actions-empty" type="text/template">
    <tr>
        <td colspan="4">
            <h3><?php _e( 'Add form actions', 'ninja-forms' ); ?></h3>
            <p><?php _e( "Get started by adding your first form field. Just click the plus and select the actions you want. It's that easy.", 'ninja-forms' ); ?></p>
        </td>
    </tr>
</script>

<script id="tmpl-nf-main-content-field" type="text/template">
    <div id="{{{ data.getFieldID() }}}" class="{{{ data.renderClasses() }}}" data-id="{{{ data.id }}}">{{{ data.renderIcon() }}}<span class="nf-field-label">{{{ _.escape( data.label ) }}} {{{ data.renderRequired() }}}</span>
        <div class="nf-item-controls"></div>
    </div>
</script>

<script id="tmpl-nf-item-controls" type="text/template">
    <div class="nf-item-edit nf-item-control"><a href="#" title="<?php _e( 'Edit', 'ninja-forms' ); ?>"><i class="nf-edit-settings fa fa-cog" aria-hidden="true"></i><!-- <span class="nf-item-editing">Editing {{{ data.objectType }}}</span> --></a></div>
    <div class="nf-item-duplicate nf-item-control"><a href="#" title="<?php _e( 'Duplicate (^ + C + click)', 'ninja-forms' ); ?>"><i class="nf-duplicate fa fa-files-o" aria-hidden="true"></i></a></div>
    <div class="nf-item-delete nf-item-control"><a href="#" title="<?php _e( 'Delete (^ + D + click)', 'ninja-forms' ); ?>"><i class="nf-delete fa fa-trash" aria-hidden="true"></i></a></div>
</script>

<script id="tmpl-nf-action-table" type="text/template">
    <table id="nf-table-display" class="nf-actions-table">
        <thead>
            <tr>
                <th></th>
                <th><?php _e( 'Name', 'ninja-forms' ); ?></th>
                <th><?php _e( 'Type', 'ninja-forms' ); ?></th>
                <th><?php _e( 'Actions', 'ninja-forms' ); ?></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</script>

<script id="tmpl-nf-action-item" type="text/template">
    <td>{{{ data.renderToggle() }}}</td>
    <td>{{{ data.label }}}</td>
    <td>{{{ data.renderTypeNicename() }}}</td>
    <td>
        <div class="nf-item-controls"></div>
    </td>
</script>

<script id="tmpl-nf-form-setting-type" type="text/template">
    <div class="{{{ data.renderClasses() }}}"><span>{{{ data.nicename }}}</span></div>
</script>

<script id="tmpl-nf-mobile-menu" type="text/template">
    <ul class="primary">
        <li class="nf-publish {{{ data.maybeDisabled() }}}"><?php _e( 'Publish', 'ninja-forms' ); ?></li>
    </ul>
    <ul class="secondary"></ul>
</script>

<script id="tmpl-nf-mobile-menu-item" type="text/template">
    <li><a href="{{{ data.renderUrl() }}}" title="{{{ data.nicename }}}" tabindex="-1" target="{{{ data.renderTarget() }}}" {{{ data.renderDisabled() }}} >{{{ data.renderDashicons() }}}{{{ data.nicename }}}</a></li>
</script>

<script id="tmpl-nf-drawer" type="text/template">
    <div id="nf-drawer-header"></div>
    <span id="nf-drawer-content"></span>
    <a class="nf-toggle-drawer" title="<?php _e ( 'Toggle Drawer', 'ninja-forms' ); ?>">
        <span class="dashicons dashicons-admin-collapse"></span><span class="nf-expand-off"><?php _e( 'Full screen', 'ninja-forms' ); ?></span><span class="nf-expand-on"><?php _e( 'Half screen', 'ninja-forms' ); ?></span>
    </a>
    <span id="nf-drawer-footer"></span>
</script>

<script id="tmpl-nf-drawer-content-add-field" type="text/template">
    <div id="nf-drawer-staging" class="nf-settings nf-stage">
        <div class="nf-reservoir nf-drawer-staged-fields nf-field-type-droppable"></div>
    </div>
    <span id="nf-drawer-primary"></span>
    <span id="nf-drawer-secondary"></span>
</script>

<script id="tmpl-nf-drawer-content-add-action" type="text/template">
    <span id="nf-drawer-primary"></span>
    <span id="nf-drawer-secondary"></span>
</script>

<script id="tmpl-nf-drawer-content-view-changes-item" type="text/template">
    <# if ( ! data.disabled ) { #>
    <tr>
    <# } else { #>
    <tr class="disabled-row">
    <# } #>

        <td>
            <span class="dashicons dashicons-{{{ data.label.dashicon }}}"></span> <span class="nf-changes-item {{{ ( data.disabled ) ? 'disabled' : '' }}}"></span>
        </td>
        <td>
            {{{ data.label.object }}}
        </td>
        <td>
            {{{ data.label.label }}}
        </td>
        <td>
            {{{ data.label.change }}}
        </td>
        <td>
            <# if ( ! data.disabled ) { #>
                <a href="#" title="<?php _e( 'Undo', 'ninja-forms' ); ?>" class="undoSingle disabled" style="text-decoration:none;">
            <# } #>

            <span class="dashicons dashicons-image-rotate {{{ ( data.disabled ) ? 'disabled' : '' }}}"></span>

            <# if ( ! data.disabled ) { #>
                </a>
            <# } #>
        </td>
    </tr>
</script>

<script id="tmpl-nf-drawer-content-edit-settings" type="text/template">
    <span class="nf-setting-title"></span>
    <span class="nf-setting-groups"></span>
</script>

<script id="tmpl-nf-drawer-content-edit-settings-title-default" type="text/template">
    <h2>{{{ data.renderTypeNicename() }}}</h2>
</script>

<script id="tmpl-nf-drawer-content-edit-settings-title-fields" type="text/template">
    <h2>{{{ data.renderSavedStar() }}} {{{ data.renderTypeNicename() }}}</h2>
    <span class="nf-add-saved-field" style="display:none"></span>
</script>

<script id="tmpl-nf-add-saved-field" type="text/template">
    <input type="text" placeholder="Saved Field Name" value="{{{ data.label }}}">
    <span class="add-button"></span>
</script>

<script id="tmpl-nf-add-saved-field-button" type="text/template">
    <a href="#" title="<?php _e( 'Add', 'ninja-forms' ); ?>" class="nf-button primary"><?php _e( 'Add', 'ninja-forms' ); ?></a>
</script>

<script id="tmpl-nf-add-saved-field-loading" type="text/template">
    <a href="#" class="nf-button primary">&nbsp;
        <div class="nf-loading">
            <ul>
                <li></li>
                <li></li>
                <li></li>
            </ul>
        </div>
    </a>
</script>

<script id="tmpl-nf-drawer-content-edit-field-setting-group" type="text/template">
    <section class="nf-settings">
        {{{ data.renderLabel() }}}
        <span class="nf-field-settings"></span>
    </section>
</script>

<script id="tmpl-nf-drawer-content-edit-setting-group-label" type="text/template">
    <h3 class="toggle"><span class="dashicons dashicons-arrow-{{{ data.renderArrowDir() }}}"></span>{{{ data.label }}}</h3>
</script>

<script id="tmpl-nf-drawer-staged-field" type="text/template">
     <span class="nf-item-dock" id="{{{ data.id }}}" data-id="{{{ data.slug }}}"><span class="fa fa-{{{ data.icon }}}" data-id="{{{ data.slug }}}"></span>{{{ data.nicename }}}<span class="dashicons dashicons-dismiss"></span>
</script>

<script id="tmpl-nf-drawer-field-type-section" type="text/template">
    <section class="nf-settings {{{ data.classes }}}">
        <h3>{{{ data.nicename }}}</h3>
        {{{ data.renderFieldTypes() }}}
    </section>
</script>

<script id="tmpl-nf-drawer-field-type-button" type="text/template">
    <div class="nf-field-type-button nf-field-type-draggable {{{ data.savedField() }}}" data-id="{{{ data.id }}}">
        <div class="nf-item" data-id="{{{ data.id }}}" tabindex="0"><span class="fa fa-{{{ data.icon }}}" data-id="{{{ data.id }}}"></span>{{{ data.nicename }}}</div>
    </div>
</script>

<script id="tmpl-nf-drawer-action-type-section" type="text/template">
    <section class="nf-settings nf-action-items {{{ data.renderClasses() }}}">
        <h3>{{{ data.renderNicename() }}}</h3>
        <span class="action-types"></span>
    </section>
</script>

<script id="tmpl-nf-drawer-action-type-button" type="text/template">
    <div class="nf-one-third nf-action-type-draggable" data-type="{{{ data.id }}}">
        <div class="{{{ data.renderClasses() }}}" style="{{{ data.renderStyle() }}}">{{{ data.nicename }}}</div>
    </div>
</script>

<script id="tmpl-nf-drawer-header-default" type="text/template">
    <header class="nf-drawer-header">
        <div class="nf-search">
            <input type="search" class="nf-filter" value="" placeholder="Filter" tabindex="-1" />
        </div>
        <a href="#" title="<?php _e( 'Done', 'ninja-forms' ); ?>" class="nf-button primary nf-close-drawer {{{ data.renderDisabled() }}}" tabindex="-1"><?php _e( 'Done', 'ninja-forms' ); ?></a>
    </header>
</script>

<script id="tmpl-nf-drawer-header-edit-settings" type="text/template">
    <header class="nf-drawer-header">
        <a href="#" title="<?php _e( 'Done', 'ninja-forms' ); ?>" class="nf-button primary nf-close-drawer {{{ data.renderDisabled() }}}" tabindex="-1"><?php _e( 'Done', 'ninja-forms' ); ?></a>
    </header>
</script>

<script id="tmpl-nf-drawer-header-view-changes" type="text/template">
    <header class="nf-drawer-header">
        <div>
            <a href="#" title="<?php _e( 'Undo All', 'ninja-forms' ); ?>" class="nf-button secondary undoChanges" style="float:left;" tabindex="-1"><span class="dashicons dashicons-backup"></span><?php _e( ' Undo All', 'ninja-forms' ); ?></a>
        </div>
        <a href="#" title="<?php _e( 'Done', 'ninja-forms' ); ?>" class="nf-button primary nf-close-drawer" tabindex="-1"><?php _e( 'Done', 'ninja-forms' ); ?></a>
    </header>
</script>

<script id="tmpl-nf-drawer-header-new-form" type="text/template">
    <header class="nf-drawer-header">
        <h3><?php _e( 'Almost there...', 'ninja-forms' ); ?></h3>
    </header>
</script>

<script id="tmpl-nf-drawer-content-new-form" type="text/template">
    <span class="new-form-name"></span>
    <div class="new-form-submit"></div>
    <div>
        <a href="#" title="<?php _e( 'Not Yet', 'ninja-forms' ); ?>" class="nf-button secondary nf-close-drawer" style="float:left;" tabindex="-1"><?php _e( 'Not Yet', 'ninja-forms' ); ?></a>
    </div>
    <a href="#" title="<?php _e( 'Publish', 'ninja-forms' ); ?>" class="nf-button primary nf-close-drawer publish" tabindex="-1"><?php _e( 'Publish', 'ninja-forms' ); ?></a>
</script>

<script id="tmpl-nf-app-menu-item" type="text/template">
    <li><a href="{{{ data.renderUrl() }}}" title="{{{ data.nicename }}}" class="{{{ data.renderClasses() }}}" target="{{{ data.renderTarget() }}}" {{{ data.renderDisabled() }}}><span class="app-menu-text">{{{ data.nicename }}}</span>{{{ data.renderDashicons() }}}</a></li>
</script>

<script id="tmpl-nf-staged-fields-drag" type="text/template">
    <div class="nf-staged-fields-drag">
        <div id="drag-item-1" class="nf-staged-fields-drag-wrap">{{{ data.num }}}<?php _e( ' Fields', 'ninja-forms' ); ?></div>
        <div id="drag-item-2" class="nf-staged-fields-drag-wrap">&nbsp;</div>
        <div id="drag-item-3" class="nf-staged-fields-drag-wrap">&nbsp;</div>
    </div>
</script>

<script id="tmpl-nf-drawer-staged-fields-empty" type="text/template">
    <div class="nf-staged-fields-empty"></div>
</script>

<script id="tmpl-nf-empty" type="text/template">

</script>

<script id="tmpl-nf-merge-tags-section" type="text/template">
    <h4>{{{ data.label }}}</h4>
    <ul class="merge-tags"></ul>
</script>

<script id="tmpl-nf-merge-tags-item" type="text/template">
    <a href="#" title="{{{ data.label }}}" tabindex="1" class="{{{ data.renderClasses() }}}">{{{ _.escape( data.label ) }}}</a>
</script>

<!-- Field Settings Templates -->

<script id="tmpl-nf-edit-setting-wrap" type="text/template">
    <div class="{{{ data.renderClasses() }}}" {{{ data.renderVisible() }}}>
        {{{ data.renderSetting() }}}
        <div class="nf-setting-error"></div>
    </div>
</script>

<script id="tmpl-nf-edit-setting-option-repeater-wrap" type="text/template">
    <div class="{{{ data.renderClasses() }}}" {{{ data.renderVisible() }}}>
        {{{ data.renderSetting() }}}
        <span class="nf-setting-error"></span>
        <span class="nf-import-options" style="display:none">
            <?php _e( 'Please use the following format', 'ninja-forms' ); ?>:
            <br>
            <br>
            <strong><?php _e( 'Label, Value, Calc Value', 'ninja-forms' ); ?></strong>
            <br>
            <br>
            <em>
            Example:
            </em>
            <pre>
Label One, value-one, 1
Label Two, value-two, 2
Label Three, value-three, 3
            </pre>
            <textarea></textarea>
            <a href="#" class="nf-button primary nf-import extra"><?php _e( 'Import', 'ninja-forms' ); ?></a>
        </span>
    </div>
</script>

<script id="tmpl-nf-edit-setting-error" type="text/template">
    <div>{{{ data.error || data.warning }}}</div>
</script>

<script id="tmpl-nf-edit-setting-textbox" type="text/template">
    <label for="{{{ data.name }}}" class="{{{ data.renderLabelClasses() }}}">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <input type="text" class="setting" id="{{{ data.name }}}" value="{{{ data.value }}}" placeholder="{{{ data.placeholder }}}" />
        {{{ data.renderMergeTags() }}}
    </label>
</script>

<script id="tmpl-nf-edit-setting-media" type="text/template">
    <label for="{{{ data.name }}}" class="{{{ data.renderLabelClasses() }}} has-merge-tags">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <input type="text" class="setting" id="{{{ data.name }}}" value="{{{ data.value }}}" placeholder="{{{ data.placeholder }}}" />
        <span class="extra open-media-manager dashicons dashicons-admin-media merge-tags"></span>
    </label>
</script>

<script id="tmpl-nf-edit-setting-datepicker" type="text/template">
    <label for="{{{ data.name }}}" class="{{{ data.renderLabelClasses() }}}">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <input type="text" class="setting" id="{{{ data.name }}}" value="{{{ data.value }}}" placeholder="{{{ data.placeholder }}}" />
    </label>
</script>

<script id="tmpl-nf-edit-setting-number" type="text/template">
    <label for="{{{ data.name }}}">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <input type="number" class="setting" id="{{{ data.name }}}"
               value="{{{ data.value }}}" {{{ data.renderMinMax() }}}
               placeholder="{{{ ('undefined' != typeof data.placeholder ) ? data.placeholder : '' }}}" />
	    <em>{{{ data.renderMinMaxHelper() }}}</em>
    </label>
</script>

<script id="tmpl-nf-edit-setting-textarea" type="text/template">
    <label for="{{{ data.name }}}" class="{{{ data.renderLabelClasses() }}}">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <textarea id="{{{ data.name }}}" class="setting">{{{ data.value }}}</textarea>
        {{{ data.renderMergeTags() }}}
    </label>
</script>

<script id="tmpl-nf-edit-setting-rte" type="text/template">
    <label class="{{{ data.renderLabelClasses() }}}">{{{ data.label }}} {{{ data.renderTooltip() }}}</label>
        <div id="{{{ data.name }}}" class="setting">{{{ data.value }}}</div>
        {{{ data.renderMergeTags() }}}

</script>

<script id="tmpl-nf-edit-setting-select" type="text/template">
    <label for="{{{ data.name }}}" class="nf-select">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <select id="{{{ data.name }}}" class="setting">
            <#
            _.each( data.options, function( option ) {
                #>
                <option value="{{{ option.value }}}" {{{ ( data.value == option.value ) ? 'selected="selected"' : '' }}}>{{{ option.label }}}</option>
                <#
            } );
            #>
        </select>
        <div></div>
    </label>
</script>

<script id="tmpl-nf-edit-setting-email-select" type="text/template">
	<label for="{{{ data.name }}}" class="nf-select">{{{ data.label }}} {{{ data.renderTooltip() }}}
			{{{ data.renderEmailFieldOptions() }}}
		<div></div>
	</label>
</script>

<script id="tmpl-nf-edit-setting-field-select" type="text/template">
    <label for="{{{ data.name }}}" class="nf-select">{{{ data.label }}} {{{ data.renderTooltip() }}}
        <select id="{{{ data.name }}}" class="setting">
            <#
            _.each( data.options, function( option ) {
                #>
                <option value="{{{ option.value }}}" {{{ ( data.value == option.value ) ? 'selected="selected"' : '' }}}>{{{ option.label }}}</option>
                <#
            } );
            #>
        </select>
        <div></div>
    </label>
</script>

<script id="tmpl-nf-edit-setting-field-list" type="text/template">
    <fieldset>
        <legend> {{{ data.label }}} </legend>
        <span class="nf-field-sub-settings"></span>
    </fieldset>
</script>

<script id="tmpl-nf-edit-setting-checkbox" type="text/template">

    <span class="nf-setting-label">{{{ data.label }}}</span> {{{ data.renderTooltip() }}}
    <input type="checkbox" id="{{{ data.name }}}" class="nf-checkbox setting" {{{ ( 1 == data.value ) ? 'checked' : '' }}} />
    <label for="{{{ data.name }}}">{{{ data.label }}}</label>

</script>

<script id="tmpl-nf-edit-setting-toggle" type="text/template">

    <span class="nf-setting-label">{{{ data.label }}}{{{ data.renderTooltip() }}}</span>
    <input type="checkbox" data-setting="{{{ data.settingName }}}" id="{{{ data.name }}}" class="nf-toggle setting" {{{ ( 1 == data.value ) ? 'checked' : '' }}} />
    <label for="{{{ data.name }}}">{{{ data.label }}}</label>

</script>


<script id="tmpl-nf-edit-setting-radio" type="text/template">

    <span class="nf-setting-label">{{{ data.label }}}{{{ data.renderTooltip() }}}</span>
    <#
    _.each( data.options, function( option ) {
    #>
    <span class="nf-setting-label">{{{ option.label }}}</span>
    <input type="radio" value="{{{ option.value }}}" name="{{{ data.name }}}" {{{ data.value == option.value ? "checked" : '' }}}></option>
    <#
    } );
    #>

</script>

<script id="tmpl-nf-edit-setting-button-toggle" type="text/template">

	<span class="nf-setting-label">{{{ data.label }}}{{{ data.renderTooltip() }}}</span>
	<div class="nf-setting button-toggle">
		<#
		_.each( data.options, function( option ) {
		#>
			<label for="field-{{{ option.value }}}"
				data-option_value="{{{ option.value }}}">
				<input type="radio" id="field-{{{ option.value }}}"
			       style="display:none;"
			       class="nf-button-toggle setting"
					value="{{{ option.value }}}" name="{{{data.name }}}"
	                {{{ data.value == option.value ? "checked" : '' }}}>
				<span class="nf-button primary {{{ data.value != option.value ?
				"disabled": "" }}}">{{{ option.label }}}</span>
			</label>
			<#
		} );
		#>
	</div>


</script>

<script id="tmpl-nf-edit-setting-color" type="text/template">

    <label for="{{{ data.name }}}" class="{{{ data.renderLabelClasses() }}}">{{{ data.label }}} {{{ data.renderTooltip() }}}</label>

    <input type="text" id="{{{ data.name }}}" value="{{{ data.value }}}" class="setting" data-default-color="#F9F9F9" />

    <div class="nf-colorpicker">

    </div>

</script>

<script id="tmpl-nf-edit-setting-fieldset" type="text/template">
    <fieldset>
        <legend>{{{ data.label }}}</legend>
        <span class="nf-field-sub-settings"></span>
    </fieldset>
</script>

<script id="tmpl-nf-edit-setting-option-repeater" type="text/template">

    <fieldset class="nf-list-options {{{ data.renderFieldsetClasses() }}}" {{{ data.renderVisible() }}}>
        <legend>{{{ data.label }}}</legend>
        <div class="nf-div-table">
            <div class="nf-table-row nf-table-header">
                {{{ data.renderHeaders() }}}
            </div>

            <div class="nf-list-options-tbody">
            </div>
        </div>
    </fieldset>
</script>

<script id="tmpl-nf-edit-setting-option-repeater-empty" type="text/template">

</script>

<script id="tmpl-nf-edit-setting-option-repeater-error" type="text/template">
    {{{ data.renderErrors() }}}
</script>

<script id="tmpl-nf-edit-setting-option-repeater-default-row" type="text/template">
    <div>
        <span class="dashicons dashicons-menu handle"></span>
    </div>
    <#
        var columns = data.getColumns();

        if ( 'undefined' != typeof columns.label ) {
        #>
             <div>
                <input type="text" class="setting" value="{{{ data.label }}}" data-id="label">
            </div>
            <#
        }
    #>
    <#
        if ( 'undefined' != typeof columns.value ) {
            #>
             <div>
                <input type="text" class="setting" value="{{{ data.value }}}" data-id="value">
            </div>
            <#
        }
    #>
    <#
        if ( 'undefined' != typeof columns.calc ) {
        #>
             <div>
                <input type="text" class="setting" value="{{{ data.calc }}}" data-id="calc">
            </div>
            <#
        }
    #>
    <#
        if ( 'undefined' != typeof columns.selected ) {
            #>
            <div>
                <input type="checkbox" class="setting" class="nf-checkbox" {{{ ( 1 == data.selected ) ? 'checked="checked"' : '' }}} value="1" data-id="selected">
            </div>
            <#
        }
    #>

    <div>
        <span class="dashicons dashicons-dismiss nf-delete"></span>
    </div>
</script>

<script id="tmpl-nf-edit-setting-html" type="text/template">
    <div class="nf-note">
        {{{ data.value }}}
    </div>
</script>

<!-- Calculation Row Template -->

<script id="tmpl-nf-edit-setting-calculation-repeater-row" type="text/template">
    <div>
        <span class="dashicons dashicons-menu handle"></span>
    </div>
    <div class="calc-left">
        <div>
            <input type="text" class="setting" value="{{{ data.name }}}" data-id="name">
            <span class="nf-option-error"></span>
        </div>
        <div><?php _e( 'Decimals', 'ninja-forms' ); ?></div>
        <div>
            <input type="text" class="setting" value="{{{ data.dec }}}" data-id="dec">
            <span class="nf-option-error"></span>
        </div>
    </div>
    <div>
        <textarea class="setting" data-id="eq">{{{ data.eq }}}</textarea>
        <span class="dashicons dashicons-list-view merge-tags"></span>
    </div>
    <div>
        <span class="dashicons dashicons-dismiss nf-delete"></span>
    </div>
</script>

<!-- Rich Text Editor Templates -->

<script id="tmpl-nf-rte-media-button" type="text/template">
    <span class="dashicons dashicons-admin-media"></span>
</script>

<script id="tmpl-nf-rte-merge-tags-button" type="text/template">
    <span class="dashicons dashicons-list-view"></span>
</script>

<script id="tmpl-nf-rte-link-button" type="text/template">
    <span class="dashicons dashicons-admin-links"></span>
</script>

<script id="tmpl-nf-rte-unlink-button" type="text/template">
    <span class="dashicons dashicons-editor-unlink"></span>
</script>

<script id="tmpl-nf-rte-link-dropdown" type="text/template">
    <div class="summernote-link">
        URL
        <input type="url" class="widefat code link-url"> <br />
        Text
        <input type="url" class="widefat code link-text"> <br />
        <label>
            <input type="checkbox" class="link-new-window"><?php _e( ' Open in new window', 'ninja-forms' ); ?>
        </label>
        <input type="button" class="cancel-link extra" value="Cancel">
        <input type="button" class="insert-link extra" value="Insert">
    </div>
</script>

<script id="nf-tmpl-save-field-repeater-row" type="text/template">
	<div>
		<span class="dashicons dashicons-menu handle"></span>
	</div>
	<div class="nf-select">
		<# try { #>
		{{{ data.renderNonSaveFieldSelect( 'field', data.field ) }}}
		<# } catch ( err ) { #>
		<input type="text" class="setting" value="{{ data.field }}" data-id="field" >
		<# } #>
	</div>
	<div>
		<span class="dashicons dashicons-dismiss nf-delete"></span>
	</div>
</script>

<?php do_action( 'ninja_forms_builder_templates' ); ?>
