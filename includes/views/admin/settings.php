<!--

@author Zishan J.

-->
<script>
    jQuery(function ($) {
        $.showStatusDialog = function (options) {
            var defaults = {
                showImage: true,
                show: true,
                message: ''
            };
            var merged_options = $.extend({}, defaults, options);

            if (merged_options.showImage) {
                $('div#statusdialog').find('img').show();
            } else {
                $('div#statusdialog').find('img').hide();
            }
            if (merged_options.message != '') {
                $('div#statusdialog').find('span').html(merged_options.message);
            }
            if (merged_options.show) {
                $('div#statusdialog').dialog('open');
            } else {
                setTimeout(function () {
                    $('div#statusdialog').dialog('close');
                }, 1000);
            }
        };
        $('div#statusdialog').dialog({
            draggable: false,
            resizable: false,
            modal: true,
            minWidth: false,
            minHeight: false,
            width: "auto",
            autoOpen: false
        });
        $(".ui-dialog-titlebar").hide();
        $(window).resize(function () {
            $("div#statusdialog").dialog("option", "position", "center");
        });

    });
</script>
<div ng-app="GI-MediaLibrary">
    <div class="clear"></div>
    <div id="statusdialog" style="-webkit-box-shadow: 2px 2px 5px #888; -moz-box-shadow: 2px 2px 5px #888; padding: 5px;">
        <div class="col"><img src="<?php echo GIML_URI . 'images/ajax-loader.gif'; ?>" style="display:none;" alt="loading" id="loading" /></div>
        <div class="col-right"><span style="font-size: 20px; line-height:37px"></span></div>
        <div class="clear"></div>
    </div>
    <div ng-controller="Settings as setting">
        <div id="settings-message" ng-bind-html="setting.statusMessage|html"></div>
        <h3 class="title">General</h3>
        <table class="form-table">
            <tr>
                <th scope="row">Layout</th>
                <td>
                    <fieldset><legend class="screen-reader-text"><span>Layout</span></legend>
                        <label for="disable-jqueryui-css">
                            <input name="disable-jqueryui-css" type="checkbox" id="disable-jqueryui-css" ng-model="setting.disableJqueryuiCss">
                            Disable jQuery UI CSS</label>
                        <p class="description">You can disable plugin jQuery UI CSS if you are using similar jQuery UI CSS file with your template to avoid overriding.</p><br/>
                        <label for="disable-bootstrap-css">
                            <input name="disable-bootstrap-css" type="checkbox" id="disable-bootstrap-css" ng-model="setting.disableBootstrapCss">
                            Disable Bootstrap CSS</label>
                        <p class="description">You can disable plugin Bootstrap CSS if you are using similar Bootstrap CSS file with your template to avoid overriding.</p>
                        <br/>
                        <label for="player-color">Player color:</label>
                        <input type="text" name="player-color" id="player-color" ng-model="setting.playerColor">
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Search Page</th>
                <td>
                    <fieldset><legend class="screen-reader-text"><span>Search Page</span></legend>
                        <label for="searchbar-caption">Search bar caption:</label>
                        <input type="text" name="searchbar-caption" id="searchbar-caption" class="regular-text" ng-model="setting.searchBarCaption">
                        <p class="description">Default caption to display in search bar.</p><br/>
                        <label for="searchpage-title">Search page title:</label>
                        <input type="text" name="searchpage-title" id="searchpage-title" class="regular-text" ng-model="setting.searchPageTitle">
                    </fieldset>
                </td>
            </tr>
            <tr>
                <th scope="row">Template Editor</th>
                <td>
                    <fieldset><legend class="screen-reader-text"><span>Template Editor</span></legend>
                        <textarea cols="80" rows="30" ng-model="setting.template"></textarea>
                        <p class="description">Search result page template.</p>
                    </fieldset>
                </td>
            </tr>
        </table>
        <p class="submit"><input type="button" name="submit" id="submit" class="button button-primary" value="Save Changes" ng-click="setting.save()"></p>
    </div>
</div>