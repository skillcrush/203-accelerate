<img class="nf-logo" src="<?php echo NF_PLUGIN_URL . 'images/nf-logo.png'; ?>">
<div class="nf-upgrade">
    <h1>Ninja Forms THREE is Here!</h1>

    <p>You are only moments away from the biggest thing to ever happen to the WordPress form building experience.</p>

    <p>Ninja Forms THREE is the most <em>intuitive</em>, <em>powerful</em>, and <em>enjoyable</em> form builder you will ever use.</p>

    <img src="<?php echo NF_PLUGIN_URL; ?>images/ninja-forms-three.png" />

    <h2>Before you update, we want to make you aware of a few <strong>very important points:</strong></h2>

    <ol>
        <li>
            <p>
                <span class="dashicons dashicons-welcome-widgets-menus"></span><strong>This is completely new form building experience</strong>
                <br />The Ninja Forms 3.0 form builder looks nothing like what you're used to currently. We've spent considerable resources to improve this over all experience, but we want you to be prepared for the difference.
            </p>
        </li>
        <li>
            <p>
                <span class="dashicons dashicons-admin-page"></span><strong><a href='https://ninjaforms.com/documentation/'>Our documentation has been updated to assist through this change</a></strong>
                <br />Ninja Forms documentation has been completely reworked in preparation of Ninja Forms 3.0. We will continue to invest time and energy to improve this resource.
            </p>
        </li>
        <li>
            <p>
                <span class="dashicons dashicons-smiley"></span><strong><a href='https://ninjaforms.com/contact/'>We are here if you have any questions</a></strong>
                <br />This is a big change. We've tested everything. And yet we know that things happen and questions arise. Don't hesitate to reach out to us during your transition to THREE.
            </p>
        </li>
    </ol>

    <hr>

    <div id="nfUpgradeApp">

    </div>

    <script type="text/html" id="tmpl-test">
        Test
    </script>

    <script type="text/html" id="tmpl-table">

        <h2>{{ data.title }}</h2>

        <!--{{{ data.legend }}}-->

        <table>
            <thead>
            <tr>
                <# _.each( data.headers, function( header ) { #>
                <th>{{header}}</th>
                <# }); #>
            </tr>
            </thead>
            <tbody>

                <# _.each( data.rows, function( row ) { #>
                    <# if( ! row.title ) { return; } #>
                <tr>
                    <td>{{row.title}}</td>
                    <td>
                        <span class="dashicons dashicons-{{row.icon}}"></span>
                    </td>
                </tr>
                <# }); #>

                <# if( 'checking' == data.step && ! data.readyToConvert ) { #>
                <tr>
                    <td colspan="2" style="text-align: center;"><span class="dashicons dashicons-update"></span></td>
                </tr>
                <# } #>

            </tbody>

        </table>

        <# if( 'undefined' != typeof data.showSupportLink && data.showSupportLink ) { #>
            <div style="text-align: center;">
                There was an error converting one or more of your forms.<br />
                Please contact <a href="http://ninjaforms.com/contact/">support</a>.
            </div>
        <# } #>

        <# if( 'checking' == data.step && data.readyToConvert ) { #>
            <button class="nf-upgrade-button js-nfUpgrade-startConversion">{{data.next}}</button>
        <# } #>
    </script>

    <script type="text/html" id="tmpl-legend">
        <span class="dashicons dashicons-yes"></span> = {{data.no_issues_detected}}
        &nbsp;
        <span class="dashicons dashicons-flag"></span> = {{data.will_need_attention}}
    </script>

</div>