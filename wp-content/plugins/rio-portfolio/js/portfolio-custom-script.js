jQuery(document).ready(function($){
	/*tab control starts from here...*/
	
	/*$("#link_content").hide();
	$("#video_content").hide();
	$("#audio_content").hide();*/
		
	$("#image_tab").click(function(){
		
		$("#link_tab").removeClass("nav-tab-active");
		$("#video_tab").removeClass("nav-tab-active");
		$("#audio_tab").removeClass("nav-tab-active");
		
		$("#image_tab").addClass("nav-tab-active");
		
		$("#link_content").hide();
		$("#video_content").hide();
		$("#audio_content").hide();
		
		$("#image_content").show();	
		$("#image_content").removeClass("hide");
	});
	
	$("#link_tab").click(function(){
		
		$("#image_tab").removeClass("nav-tab-active");
		$("#video_tab").removeClass("nav-tab-active");
		$("#audio_tab").removeClass("nav-tab-active");
		
		$("#link_tab").addClass("nav-tab-active");
		
		$("#image_content").hide();	
		$("#video_content").hide();
		$("#audio_content").hide();
		
		$("#link_content").show();
		$("#link_content").removeClass("hide");
	});
	
	$("#video_tab").click(function(){
		
		$("#image_tab").removeClass("nav-tab-active");
		$("#link_tab").removeClass("nav-tab-active");
		$("#audio_tab").removeClass("nav-tab-active");
		
		$("#video_tab").addClass("nav-tab-active");
		
		
		$("#image_content").hide();	
		$("#link_content").hide();
		$("#audio_content").hide();
		
		$("#video_content").show();
		$("#video_content").removeClass("hide");
	});	
	$("#audio_tab").click(function(){
		
		$("#image_tab").removeClass("nav-tab-active");
		$("#link_tab").removeClass("nav-tab-active");
		$("#video_tab").removeClass("nav-tab-active");
		
		$("#audio_tab").addClass("nav-tab-active");
		
		$("#image_content").hide();	
		$("#link_content").hide();
		$("#video_content").hide();
		
		$("#audio_content").show();
		$("#audio_content").removeClass("hide");
	});	
	/*tab control ends here...*/
	if($('#portfolio_post_order').is(':checked'))
		{
        	//$('#tbl_image_settings').toggle();
			$('#tbl_portfolio_postorder_settings').show();
		}
		else
		{
			$('#tbl_portfolio_postorder_settings').hide();
		}
	
	//toggle postorder asc and desc..
	  $('#portfolio_post_order').click(function(){
		if($(this).is(':checked'))
		{
        	//$('#tbl_image_settings').toggle();
			$('#tbl_portfolio_postorder_settings').show();
		}
		else
		{
			$('#tbl_portfolio_postorder_settings').hide();
		}
		
    });
	
	//delete image
	$('.delete_image').click(function(){
	var _id=$(this).attr('id');
	var res=confirm("Do you want want to delete image");
	if(res == true)
	{
	if(_id == 'del_video_img')//image post format
	{
	$('#portfolio_image').val('');
	$('#portfolio_image_show').hide();
	}
	else if(_id == 'del_link_img')//image post format
	{
	$('#portfolio_link_image').val('');
	$('#portfolio_link_image_show').hide();
	}
	else if(_id == 'del_video_img')//image post format
	{
	$('#portfolio_video_image').val('');
	$('#portfolio_video_image_show').hide();
	}
	
	}
	else
	{
	return false;
	}
	});

});
//Wordpress file uploader
jQuery(document).ready(function($){
	  
	  var _custom_media = true,
		_orig_send_attachment = wp.media.editor.send.attachment;
		$('.btn_upload').click(function() {
		var btn_id = $(this).attr("id");
		var send_attachment_bkp = wp.media.editor.send.attachment;
		_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment)
			{
				if ( _custom_media ) 
				{
					if(btn_id == "portfolio_image_button")
					{
						$("#portfolio_image").val(attachment.url);
						$("#portfolio_image_show").html('&nbsp;<img width="150" height="100" src="'+attachment.url+'" title="" alt="Portfolio Image">');
					}
					else if(btn_id == "portfolio_video_image_button")
					{
						$("#portfolio_video_image").val(attachment.url);
						$("#portfolio_video_image_show").html('&nbsp;<img width="150" height="100" src="'+attachment.url+'" title="" alt="Portfolio video Image">');
					}
					else if(btn_id == "portfolio_link_image_button")
					{
						$("#portfolio_link_image").val(attachment.url);
					}
				} 
				else 
				{
				return _orig_send_attachment.apply( this, [props, attachment] );
			};
		}
		wp.media.editor.open(this);
		return false;
	});
  });