<?php
/*
SNISTAF Public Code
By Srikanth Kasukurthi
Copyright (c) 2015 for SNIST

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the 'Software'), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED 'AS IS', WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
require_once("../models/config.php");
if(!isUserLoggedIn()) {
	addAlert("warning", "Login to continue!");
	apiReturnError($ajax, SITE_ROOT."login.php");
}
setReferralPage(getAbsoluteDocumentPath(__FILE__));
 ?>
<html>
<?php

echo renderAccountPageHeader(array("#SITE_ROOT#" => SITE_ROOT, "#SITE_TITLE#" => SITE_TITLE, "#PAGE_TITLE#" => "Thread"));
?>
<body>

	<div id="wrapper">
		<?php echo renderMenu("Forum");
		?>
	<div id="pagewrapper" padding-left="60px" >
<div class="row">
				<div id='display-alerts' class="col-lg-12">

				</div></div>
				<div class="row" 	>
					<div class="col-lg-12">
  <?php echo renderMenu	("Forum");?>
  <?php
  $tid=$_GET['id'];
	$fid=getParentForum($tid);
	addThreadStatsViews($tid);
$resultarray=loadThreadPosts($tid);
if(isset($_GET['page'])){
$offset=$_GET['page'];}
else
$offset=0;
$next=$offset+1;
$prev=$offset-1;
$arr_length=count($resultarray);
$results=array_slice($resultarray,$offset*10,10,true);
//print_r($offset." ".$next);
//print_r($results);
//print_r($resultarray);?>
<div id="pagewrapper" padding-left="60px" >
	<ol class="breadcrumb">
	<li><a href="index.php">Home</a></li>
	<li ><a href="viewForum.php?id=<?php echo $fid;?>"><?php echo getForumName($fid);?></a></li>
	<li class="active"><a href="?id=<?php echo $tid;?>"><?php echo getThreadName($tid);?></a></li>
	</ol>

<!-- Modal -->
<table class="table table-responsive">
	<tbody>
<?php foreach($results as $row): array_map('htmlentities',$row); ?>

    <tr >
			<td class="col-md-1" rowspan="2">


        <img src="../account/image.php?id=<?php echo $row['added_by']; ?>" width="60px" height="60px" alt="DP" class="img-responsive">
                <figcaption><a href="../account/Profile.php?id=<?php echo $row['added_by'] ?>" ><?php echo getDisplayNameById($row['added_by']); ?></a>
								</br><?php echo getTitleById($row['added_by']); ?></figcaption>

      </td>
<td class="col-md-12 ">


        <p><?php 	echo nl2br($row['content']); ?></p>

</td></tr><tr>
<td colspan="2" class="col-md-6 inline">
	<p class="pull-left">Likes:<span><?php echo $row['likes']; ?></span></p>
	<button class="btn btn-default pull-right" id="like" onclick="like(this)"  value="<?php echo $row['id'];?>">Like</button>
	<button class="btn btn-default pull-right" id="reply" onclick="reply(this)"  value="<?php echo strip_tags($row['content']);?>" > Reply</button>
	<?php if(isMod($loggedInUser->user_id,$fid)) { ?><button class="btn btn-default pull-right" id="delete" onclick="del(this)"  value="<?php echo $row['id'];?>">Delete</button> <?php } ?>
</td>
</tr>
<?php endforeach; ?></tbody></table>
<div class="modal fade" id="postModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Create Post</h4>
      </div>
      <div class="modal-body">
	<form name="cpost" id="cpost" class="form-group" action="" method="POS	T" >
	<input type="hidden" name="tid" value="<?php echo $_GET['id'];?>">
	<input type="hidden" name="fil" id="fil" value="">
	<textarea form="cpost" id="content" class="form-control" rows="3" name="content"></textarea>

</div>
	<div class="modal-footer">
		<input type="submit" name="submit" class="btn btn-default" >
		</form>
		<span class="btn btn-success fileinput-button">
				<i class="glyphicon glyphicon-plus"></i>
				<span>Add files...</span>
				<!-- The file input field used as target for the file upload widget -->
				<input id="fileupload" type="file"  name="files[]" multiple>
		</span>
		<br>
		<br>
		<!-- The global progress bar -->
		<div id="progress" class="progress">
				<div class="progress-bar progress-bar-success"></div>
		</div>
		<!-- The container for the uploaded files -->
		<div id="files" class="files"></div>
		</div>
	</div>
</div>
</div>
<button type="button" class="btn btn-primary btn-small pull-left" data-toggle="modal" data-target="#postModal">
Post To Thread
</button>
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Image preview</h4>
      </div>
      <div class="modal-body">
        <img src="" id="imagepreview" style="width: 400px; height: 264px;" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<ul class="pager">
<li>	<a href="?id=<?php echo $tid;?>&page=<?php echo $offset==0?0:$prev;?>" class="btn btn-info" role="button"><span class="fa fa-angle-left" aria-hidden="true" ></span></a>
</li><li>	<a href="?id=<?php echo $tid;?>&page=<?php echo $next*10<=$arr_length?$next:$offset;?>" class="btn btn-info" role="button"><span class="fa fa-angle-right" aria-hidden="true"></span></a>
</li></ul>
<script>
			$(document).ready(function() {

		// Load jumbotron links
		$(".jumbotron-links").load("jumbotron_links.php");

		alertWidget('display-alerts');

		$("form[name='cpost']").submit(function(e){
		var form = $(this);
		var serializedData = form.serialize();
		serializedData += '&ajaxMode=true';
		var url = '../api/createPost.php';
		$.ajax({
			type: "POST",
			url: url,
			data: {
				tid:	form.find('input[name="tid"]').val(),
				content: form.find('textarea[name="content"]').val(),
				fil: form.find('input[name="fil"]').val(),
				ajaxMode: "true"
			},
			success: function(result) {
			var resultJSON = processJSONResult(result);
			if (resultJSON['errors'] && resultJSON['errors'] > 0){
				alertWidget('display-alerts');
			} else {
				/*window.location.replace("");
				alertWidget('success');*/
				//alert("success");
				window.location.replace("");
			}
			}
		});
		// Prevent form from submitting twice
		e.preventDefault();
		});

	});

		function like(e) {
	        var text = $(e)	.attr('value');

		$.ajax({
			type: "GET",
			url: "../api/incLikes.php",
			data: {
				pid:	$(e).attr("value"),
				ajaxMode: "true"
			},
			success: function(result) {
			var resultJSON = processJSONResult(result);
			if (resultJSON['errors'] && resultJSON['errors'] > 0){
				alertWidget('display-alerts');
			} else {
					window.location.replace("");
			}
			}
		});
	}
	function del(e){
				var text = $(e)	.attr('value');

	$.ajax({
		type: "GET",
		url: "../api/deletePost.php",
		data: {
			pid:	$(e).attr("value"),
			ajaxMode: "true"
		},
		success: function(result) {
		var resultJSON = processJSONResult(result);
		if (resultJSON['errors'] && resultJSON['errors'] > 0){
			alertWidget('display-alerts');
		} else {
				window.location.replace("");
		}
		}
	});
	}

	function reply(e) {
			var text="\"";
				var text2 = $(e).attr('value');
				 text=text.concat(text2);
				text=	text.concat("\"");
				document.getElementById("content").value=text;
				$('#postModal').modal({show: true});

}

</script>
<script>
/*jslint unparam: true, regexp: true */
/*global window, $ */
$(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = 'fileUpload.php',
        uploadButton = $('<button/>')
            .addClass('btn btn-primary')
            .prop('disabled', true)
            .text('Processing...')
            .on('click', function () {
                var $this = $(this),
                    data = $this.data();
                $this
                    .off('click')
                    .text('Abort')
                    .on('click', function () {
                        $this.remove();
                        data.abort();
                    });
                data.submit().always(function () {
                    $this.remove();
                });
            });
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        autoUpload: false,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf])$/i,
        maxFileSize: 5000000, // 5 MB
        // Enable image resizing, except for Android and Opera,
        // which actually support image resizing, but fail to
        // send Blob objects via XHR requests:
        disableImageResize: /Android(?!.*Chrome)|Opera/
            .test(window.navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true
    }).on('fileuploadadd', function (e, data) {
        data.context = $('<div/>').appendTo('#files');
        $.each(data.files, function (index, file) {
            var node = $('<p/>')
                    .append($('<span/>').text(file.name));
            if (!index) {
                node
                    .append('<br>')
                    .append(uploadButton.clone(true).data(data));
            }
            node.appendTo(data.context);
        });
    }).on('fileuploadprocessalways', function (e, data) {
        var index = data.index,
            file = data.files[index],
            node = $(data.context.children()[index]);
        if (file.preview) {
            node
                .prepend('<br>')
                .prepend(file.preview);
        }
        if (file.error) {
            node
                .append('<br>')
                .append($('<span class="text-danger"/>').text(file.error));
        }
        if (index + 1 === data.files.length) {
            data.context.find('button')
                .text('Upload')
                .prop('disabled', !!data.files.error);
        }
    }).on('fileuploadprogressall', function (e, data) {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        $('#progress .progress-bar').css(
            'width',
            progress + '%'
        );
    }).on('fileuploaddone', function (e, data) {
        $.each(data.result.files, function (index, file) {
            if (file.url) {
                var link = $('<a>')
                    .attr('target', '_blank')
                    .prop('href', file.url);
							 $(data.context.children()[index])
                    .wrap(link);

									document.getElementById("fil").value=file.url;
            } else if (file.error) {
                var error = $('<span class="text-danger"/>').text(file.error);
                $(data.context.children()[index])
                    .append('<br>')
                    .append(error);
            }
        });
    }).on('fileuploadfail', function (e, data) {
        $.each(data.files, function (index) {
            var error = $('<span class="text-danger"/>').text('File upload failed.');
						//alert(JSON.stringify($(data).files));
            $(data.context.children()[index])
                .append('<br>')
                .append(error);
        });

    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
				$('#fileupload').bind('fileuploadprocessfail', function (e, data) {
				    alert(data.files[data.index].error);
				});
});
/*$("#imagePop").on("click", function() {
   $('#imagepreview').attr('src', $('#postImage').attr('src')); // here asign the image to the modal when the user click the enlarge link
   $('#imagemodal').modal('show'); // imagemodal is the id attribute assigned to the bootstrap modal, then i use the show function
});*/
</script>
</div></div></div>
</body>
</html>
