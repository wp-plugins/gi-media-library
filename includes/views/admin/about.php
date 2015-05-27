<!--

@author Zishan J.

-->

<script>
    jQuery(function($) {
        $.showStatusDialog = function(options) {
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
                setTimeout(function() {
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
        $(window).resize(function() {
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
    <div id="aboutgicatalog" ng-controller="About as about">
        <div id="about-message" ng-bind-html="about.statusMessage|html"></div>
        <div id="post-body" class="metabox-holder column-2">
            <div id="postbox-container-1" class="postbox-container">
                <div id="normal-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle">About GI-Media Library</h3>
                        <div class="inside">
                            <p>GI-Media Library was developed especially for institutions providing online education. With this plugin, it's easy to create your course/media library in a tabular form without any effort of custom building pages and layouts. You can organize it into a group (course) and subgroup (subjects), create combo items (chapters), create playlist section (sections) under that item and then add your links to course materials or media's under that section. You can create your own table with desired number of columns like topic, duration, files etc. It supports all type of libraries like audio, video, pdf, doc etc. As of version 3.0, this plugin uses WordPress built-in audio player:</p>
                            <ul>
                                <li>HTML5: mp3, mp4 (AAC/H.264), ogg (Vorbis/Theora), webm (Vorbis/VP8)</li>
                            </ul>
                            <p>Playing external videos (YouTube and Vimeo) are also supported.</p>
			    <p>You can fully customize the layout by providing CSS stylesheet class and change the text direction from LTR to RTL, if you want to use Arabic, Persian, Urdu languages.</p>
                            <p>If you are looking for advanced feature with Student Registration and LMS (Learning Management System), then check our <a href="http://www.glareofislam.com/#gi-lms">GI-Learning Management System</a> which will work with GI-Media Library to provide you with complete Learning Management System.</p>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle">User's Manual</h3>
                        <div class="inside">
                            <div class="col-right"><a href="http://www.adobe.com/go/getreader"><img src="<?php echo GIML_URI; ?>images/get_adobe_reader.gif"></a></div>
                            <div class="clear"></div>
                            <iframe src="http://www.glareofislam.com/softwares/gimedialibrary-manual.pdf?nocache=<?php echo time(); ?>" data-auto-height="false" data-aspect-ratio="undefined" scrolling="no" width="100%" height="600" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-2" class="postbox-container">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle">Author and License</h3>
                        <div class="inside">
                            <p>This plugin was written and developed by <a href="http://www.glareofislam.com/softwares/gimedialibrary.html" target="_blank">Zishan Javaid</a>. It is licensed as Free Software under <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU General Public License 2 (GPL 2)</a>. If you like the plugin, giving a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8" target="_blank">donation</a> is recommended. Please rate and review the plugin in the <a href="http://wordpress.org/plugins/gi-media-library/" target="_blank">WordPress Plugin Directory</a>. Donations and good ratings encourage me to further develop the plugin and to provide countless hours of support. Any amount is appreciated! Thanks!</p>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle">Donate by PayPal</h3>
                        <div class="inside">
                            <p align="center"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=HQ2DHNS7TQNZ8" target="_blank">
                                    <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" id="paypal-submit" alt="PayPal - The safer, easier way to pay online!" style="cursor:pointer"></a></p>
                        </div>
                    </div>
                    <div class="postbox">
                        <div class="handlediv" title="Click to toggle"><br></div>
                        <h3 class="hndle">Comments/Suggestion</h3>
                        <div class="inside">
                            <div class="input-text-wrap">
                                <label class="prompt" for="giml-about-name"><font color="#FF0000">*</font> Name:</label><br />
                                <input type="text" id="giml-about-name" name="giml-about-name" style="width:100%" autocomplete="off" ng-model="about.name">
                            </div>
                            <div class="input-text-wrap">
                                <label class="prompt" for="giml-about-email"><font color="#FF0000">*</font> Email:</label><br />
                                <input type="text" id="giml-about-email" name="giml-about-email" style="width:100%" autocomplete="off" ng-model="about.email">
                            </div>
                            <div class="input-text-wrap">
                                <label class="prompt" for="giml-about-subject"><font color="#FF0000">*</font> Subject:</label><br />
                                <input type="text" id="giml-about-subject" name="giml-about-subject" style="width:100%" autocomplete="off" ng-model="about.subject">
                            </div>
                            <div class="textarea-wrap">
                                <label class="prompt" for="giml-about-comment"><font color="#FF0000">*</font> Message:</label><br />
                                <textarea ui-tinymce="about.basicEd" id="giml-about-comment" style="width:100%" name="giml-about-comment" cols="34" rows="10" ng-model="about.message"></textarea>
                            </div>
                            <input type="submit" name="submit" value="Send" class="button-primary" ng-disabled="about.name.length==0 || about.email.length==0 || about.subject.length==0 || about.message.length==0" ng-click="about.send()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="clear"></div>
    </div>      <!-- END ABOUT TAB -->
</div>
