<div ng-app="GI-MediaLibrary">
    <div class="clear"></div>
    <div ng-controller="Import as import" uploader="uploader" filters="queueLimit, customFilter">
        <div ng-bind-html="import.message"></div>
        <div ng-bind-html="import.statusMessage"></div>
        <div class="row show-hide" ng-show="import.isImported">
            <div class="col" style="padding-top:5px;padding-bottom:5px">Select XML file to upload:</div>
            <div class="col"><input type="file" nv-file-select uploader="uploader" /></div>
            <div class="clear"></div>
        </div>
        <div class="hr-separator"></div>
        <div class="row show-hide" ng-show="import.isUploading">
            <div class="col-sm-12" style="margin-bottom: 40px">

                <h3>Upload progress</h3>

                <table class="table">
                    <thead>
                        <tr>
                            <th width="50%">Name</th>
                            <th ng-show="uploader.isHTML5">Size</th>
                            <th ng-show="uploader.isHTML5">Progress</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="item in uploader.queue">
                            <td><strong>{{ item.file.name}}</strong></td>
                            <td ng-show="uploader.isHTML5" nowrap>{{ item.file.size / 1024 / 1024|number:2 }} MB</td>
                            <td ng-show="uploader.isHTML5">
                                <div class="progress" style="margin-bottom: 0;">
                                    <div class="progress-bar" role="progressbar" ng-style="{ 'width': item.progress + '%' }"></div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span ng-show="item.isSuccess"><i class="glyphicon glyphicon-ok"></i></span>
                                <span ng-show="item.isCancel"><i class="glyphicon glyphicon-ban-circle"></i></span>
                                <span ng-show="item.isError"><i class="glyphicon glyphicon-remove"></i></span>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
