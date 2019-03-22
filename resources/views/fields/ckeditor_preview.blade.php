<div class="form-group mt-5">

    <ul class="nav nav-tabs">
        <li role="tab" class="active"><a href="#tpl_edit"> <i class="glyphicon glyphicon-edit"></i> Edit Template</a></li>
        <li role="tab"><a href="#tpl_preview"><i class="glyphicon glyphicon-envelope"></i> Preview Template</a></li>
    </ul>

</div>

<div class="tab-content">

    <div role="tabpanel" id="tpl_edit" class="form-group tab-pane active">
        <textarea class="form-control" placeholder="Message" id="message" name="body">{{ $template->body }}</textarea>
    </div>

    <div role="tabpanel" id="tpl_preview" class="form-group tab-pane">

        <div class="panel panel-default">
            <div class="panel-body">
                <iframe name="preview_body" frameborder="0" width="100%" height="400"></iframe>
            </div>
        </div>

    </div>
</div>


<script>
    $(document).ready(function () {

        //CKEDITOR.plugins.addExternal('autogrow', '/plugins/autogrow/', 'plugin.js');

        editor = CKEDITOR.replace('message', {

            /*
            extraPlugins: 'autogrow',
            autoGrow_onStartup: true,
            autoGrow_minHeight: 400,
            autoGrow_bottomSpace: 60,
            */

            height: '25em',
            removeButtons: 'Strike,Subscript,Anchor,About',
            autoUpdateElement: true
        });

        editor.on('instanceReady', function (evt) {
            $("[type=submit]").removeAttr("disabled");
        });

        $('.nav a').click(function (e) {
            e.preventDefault();
            $(this).tab('show')
        }).on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href");

            if (target == '#tpl_preview') {

                var msg = editor.getData();

                $.post('/admin/mail-templates/preview', {
                    body: msg
                }, function (data) {

                    console.log(data);

                    //$("[name=frame_preview]").attr('height', editor_height + 60);
                    var doc = $("[name=preview_body]").get(0).contentDocument;
                    doc.open();
                    doc.write(data);
                    doc.close();
                });

            }
        });


    });

</script>
