var d="font-size:50px;color:#f26826;";
var a="font-size:50px;color:#2e69b3;";
var c="font-size:25px;color:#000000;";
console.log("%cswift%cintern%c.com",d,a,c);
console.log("Join us, send your resume at info@swiftintern.com");

var today = new Date();
var dd = today.getDate();
var mm = today.getMonth()+1;
var yyyy = today.getFullYear();

if(dd<10){ dd='0'+dd} 
if(mm<10){ mm='0'+mm} 
var today = yyyy+'-'+mm+'-'+dd;

// Track basic JavaScript errors
window.addEventListener('error', function(e) {
	ga('send', 'event', 'JavaScript Error', e.message, e.filename, e.lineno);
});

// Track AJAX errors (jQuery API)
$(document).ajaxError(function(e, request, settings) {
	ga('send', 'event', 'Ajax Error', settings.url, e.result);
});

//affiliate
$(document).ready(function() {
	var affiliate_id = GetURLParameter('aff');
	var prev_affiliate_id = getCookie('affiliate_id');

	if(affiliate_id && affiliate_id != prev_affiliate_id){
		setCookie('affiliate_id', affiliate_id, '1');
		$.ajax({
			url: "application/controllers/public/action.php",
			type: 'POST',
			data: {action: 'aff_visit', affiliate_id: affiliate_id}
		})
		.done(function() {
			console.log("added visitor affiliate: "+affiliate_id);
		})
		.fail(function() {
			console.log("error");
			deleteCookie('affiliate_id');
		})
		.always(function() {
			console.log("Affiliation Check Complete");
		});
	}
});

//login
//google
$(".googlelogin").click(function() {
	$(".googlelogin").addClass("disabled");
	$(".googlelogin").html('<i class="fa fa-spinner fa-spin"></i>  | Processing...');
	var action = 'register';
	$.ajax({
		type: "POST",
		url: "library/gplus/process_google.php",
		data: {action: action},
		success: function(data){
			if (data.substring(0, 4) == "http") {
				window.location.href = data;
			}else{
				window.location.href = window.location.pathname;
			}
		}
	});
	return false;
});

$(document).ready(function() {
	$('#loginform').submit(function(e) {
		ga('send', 'event', 'Button', 'Click', 'Login');
		var redirect = GetURLParameter('redirect');
		$('#loginform button').addClass("disabled");
		$('#loginform button').html('<i class="fa fa-spinner fa-spin"></i>  | Processing...')
		var dataString = $("#loginform").serialize();
		$.ajax({
			type: "POST",
			url: "application/controllers/public/action.php",
			data: dataString,
			success: function(data){
				$(this).closest('button').html('Done!!!');
				$('.modal').modal('hide');
				var user = data['0'];
				$('*[data-target="#authorization"]').hide();
				if(redirect != null){
					window.location.href = redirect;
				}else{
					bootbox.alert(data, function(){
						window.location.href = window.location.pathname;
					});
				}
			}
		});
		return false;
	});
});

$('#changepassForm').submit(function (e){
	e.preventDefault();
	var id 			 = URLVariables[2];
	var access_token = URLVariables[3];
	var password 	 = $('#password').val();
	var rpassword 	 = $('#rpassword').val();
	if(password == rpassword){
		changePassword(id, access_token, password).success(function (data) {
			bootbox.alert(data);
			$('#loginform').removeClass('hide');
			$('#changepassForm').addClass('hide');
		});
	}else {
		bootbox.alert('Password Does not match. Re Enter it');
	}
});

$('#chngpass').click(function(){
	bootbox.prompt("Enter your new password?", function(result) {
		if (result != null) {
			updatePassword(result).success(function(data) {
				bootbox.alert(data);
			});
		};
	});
});

$(document).on("click","button[name=mail]",function(e){
	var value 	= this.value;
	var id 		= this.id;
	var name 	= this.name;
	var button  = this;

	switch(value){
		case 'verifyaccount':
			resendVerifyEmail(id).success(function (data) {
				bootbox.alert(data);
			});
			break;
		case 'forgetpassword':
			bootbox.prompt("What is your Email?", function(result) {
				if (result != null) {
					forgetPassword(result).success(function (data) {
						bootbox.alert(data);
					});
				}
			});
			break;
	}
});

//views
$(document).ready(function() {
	$('button[name="edit"]').click(function(e) {
		var value = this.value;
		var id 		= this.id;
		var name 	= this.name;
		var button  = this;
		switch(value){
			case 'blog':
				var post_id = id;
				console.log('Started Editing Post : '+post_id);
				$('#'+value).attr('contenteditable', 'true');
				$('#'+value).focus();
				$(button).addClass('hide');
				$('button[name="save"][id="'+id+'"]').removeClass('hide');
				break;
			default:
				$('#modal_'+value).modal('show');
				break;
		}
	});

	$(document).on("click","button[name=save]",function(e){
		var value 	= this.value;
		var id 		= this.id;
		var name 	= this.name;
		var button  = this;
		switch(value){
			case 'resume':
				var resume 		= $('#showresume').html();
				var dataString 	= "action=save_resume";
				if (typeof resume_id === 'undefined') {
					var resume_id = id;
				}
				dataString 		= dataString.concat("&resume_id="+resume_id, "&resume="+escape(resume));

				saveResume(dataString).success(function (data) {
					bootbox.alert('Saved Resume '+data);
				});
				break;

			case 'blog':
				var content 	= $('#'+value).html();
				$('#'+value).attr('contenteditable', 'false');
				var dataString  = 'id='+id;
				updateItem(dataString.concat('&item='+value, '&content='+escape(content))).success(function (data) {
					bootbox.alert(data);
				});
				break;
		}
	});

	$('button[name="delete"]').click(function(e) {
		var value = this.value;
		var id 		= this.id;
		var name 	= this.name;
		var button  = this;
		bootbox.confirm("Are you sure?", function(result) {
			if (result == true) {
				deleteItem(value, id).success(function (data) {
					switch(value){
						case 'resume':
							$('#result').removeClass('hide');
							$('#showresume').parent('div').addClass('hide');
							$("option[value="+resume_id+"]").remove();
							bootbox.alert(data);
							break;
						case 'blog':
							$('#'+value).html('Deleted Successfully');
							break;
						default:
							$(this).closest('tr').hide();
							break;
					}
				});
			};
		});
	});

	

});

$("#messageform").submit(function() {
    $("#message_btn").html('<i class="fa fa-spinner fa-spin"></i>  | Sending...');
	ga('send', 'event', 'Button', 'Click', 'Message', 'Send');
    var message = $("#messageform textarea").val();
    var user_id = $("button[name=message]").val();
    sendMessage(user_id, message).success(function (data) {
    	$("#message_btn").html('Send Message');
        $('#message_modal').modal('hide');
        bootbox.alert(data);
    });
    return false;
});

//student views
//student register
$("#student_register").submit(function(e) {
	e.preventDefault();
	ga('send', 'event', 'Button', 'Click', 'Register', 'Student');
	$('#student_register button').addClass('disabled');
	$('#student_register button').html('Processing....<i class="fa fa-spinner fa-spin"></i>');
	var dataString = $("#student_register").serialize();
	studentRegister(dataString).success(function (data) {
		$('#student_register button').html('Done!!!');
		bootbox.alert(data, function(){
			window.location.href = 'student';
		});
	});
});

//educator/register.php
$("#educator_register").submit(function(e) {
	e.preventDefault();
	ga('send', 'event', 'Button', 'Click', 'Register', 'Educator');
	$('#educator_register button').addClass('disabled');
	$('#educator_register button').html('Processing....<i class="fa fa-spinner fa-spin"></i>');
	var dataString = $("#educator_register").serialize();
	educatorRegister(dataString).success(function (data) {
		$('#educator_register button').html('Done!!!');
		bootbox.alert(data);
	});
});

//affliate/register.php
$("#affiliate_register").submit(function(e) {
	e.preventDefault();
	ga('send', 'event', 'Button', 'Click', 'Register', 'Affiliate');
	$('#affiliate_register button').addClass('disabled');
	$('#affiliate_register button').html('Processing....<i class="fa fa-spinner fa-spin"></i>');
	var dataString = $("#affiliate_register").serialize();
	affiliateRegister(dataString).success(function (data) {
		$('#affiliate_register button').html('Done!!!');
		bootbox.alert(data, function(){
			window.location.href = 'affiliate';
		});
	});
});


//employer/register.php
$(document).ready(function() {
    $('#employer_register').submit(function(e) {
        e.preventDefault();
		ga('send', 'event', 'Button', 'Click', 'Register', 'Employer');
        $('#employer_register button').addClass('disabled');
        $('#employer_register button').html('Processing....<i class="fa fa-spinner fa-spin"></i>');
        $(this).ajaxSubmit({ 
            target: '#output',
            beforeSubmit: beforeSubmit,
            url: 'application/controllers/public/action.php',
            type: 'POST',
            resetForm: true,
            success: function (data) {
                $('#employer_register button').addClass('Done!!!');
                bootbox.alert(data, function(){
					window.location.href = 'employer';
				});
            }
        });
    });
});

//employer/companies.php
$('#orgsearch').submit(function(e) {
    $('#search_results').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
    e.preventDefault();
    var keyword = $('#orgkeyword').val();
    findOrganization(keyword, '12', '0', '1').success(function (data) {
        $('#search_results').html('');
        if (data != '') {
            $.each(data, function (i, item) {
                if(item.name.length > '20'){ var name = item.name.substr(0, 20)+'...';}
                else{ var name = item.name;}
                $('#search_results').append('<div class="col-sm-4 col-md-3"><div class="thumbnail"><img src="'+item.image+'" alt="'+item.name+'"><div class="caption"><p>'+name+'</p><p><a href="organization/'+encodeURI(item.name)+'/'+item.id+'" class="btn btn-primary" role="button">Profile</a> <button name="follow" value="organization" id="'+item.id+'" class="btn btn-success">Follow</button></p></div></div></div>');
            });
            $('#loadmorebtn').html('Load More');
        } else{
            $('#search_results').html('<p class="text-center">No '+keyword+' company found please try a different keyword.</p>');
        };
        $('#loadmorebtn').addClass('hide');
    });
});

function loadmoreComps(page, per_page) {
    var offset      = (page-1)*per_page;
    var limit       = per_page;
    var property    = 'type';
    var property_id = 'company';
    var validity    = '1';

    $('#loadmorebtn').addClass('disabled');
    $('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
    fetchOrganization(property, property_id, limit, offset, validity).success(function (data) {
        if (data != '') {
            $.each(data, function (i, item) {
                if(item.name.length > '18'){ var name = item.name.substr(0, 18)+'...';}
                else{ var name = item.name;}
                $('#search_results').append('<div class="col-sm-4 col-md-3"><div class="thumbnail"><img src="'+item.image+'" alt="'+item.name+'"><div class="caption"><p><small>'+name+'</small></p><p><a href="organization/'+encodeURI(item.name)+'/'+item.id+'" class="btn btn-primary btn-sm">Profile</a> <button name="follow" value="organization" id="'+item.id+'" class="btn btn-success btn-sm">Follow</button></p></div></div></div>');
            });
            $('#loadmorebtn').removeClass('disabled');
            $('#loadmorebtn').html('Load More');
        } else{
            $('#loadmorebtn').addClass('hide');
        };
    });
}

//resume/success.php
$(document).on("click","button[name=print]",function(e){
	var value = this.value;
	if (!window.print){
		alert("You need NS4.x to use this print button!")
		return
	}
	window.print();
});

//resume/create.php
$('#basics').submit(function(e) {
	e.preventDefault();
	var email = $('#basics input[name="email"]').val();
	findUserByEmail(email).success(function (data) {
		if (data == 'false') {
			createResumeBasic($('#basics').serialize()).success(function (data) {
				window.user 	= data[0];
				window.student 	= data[1];
			});
			$('#showMessage').html('We have Added your Basic Information. Now Add Eduaction Qualification');
			$('#basics').addClass('hide');
			$('#education').removeClass('hide');
		}else{
			bootbox.alert('hi '+user.name+' we have created your resume. click ok to edit your resume.', function () {
				window.location.href = "resume/success";
			});
		};
	});
});

$('#education').submit(function(e) {
	e.preventDefault();
	var dataString = $('#education').serialize();
	saveQualification(dataString).success(function (data) {
		$('#showMessage').html('Education Qualification added Successfully, enter work experience to make your resume look good');
		$('#education').addClass('hide');
		$('#work').removeClass('hide');
	});
});

$('#work').submit(function(e) {
	e.preventDefault();
	var dataString = $('#work').serialize();
	saveWork(dataString).success(function (data) {
		$('#showMessage').html('Created Successfully');
		$('#work').addClass('hide');
		window.location.href = 'resume/success';
	});
});

$('button[name=more]').click(function(e) {
	var refid 	= this.value;
	var element = $('#'+refid).html();
	$('#'+refid).after(element);
});

//modal_profile.php
$('#modal_profile form').submit(function(e) {
	e.preventDefault();
	var dataString = $(this).serialize();
	updateStuProfile(dataString).success(function (data) {
		$('#modal_profile').modal('hide');
		bootbox.alert(data, function(){
			window.location.href = window.location.pathname;
		});
	});
});

//modal_qualification.php
$('#modal_qualification form').submit(function(e) {
	e.preventDefault();
	$('#modal_qualification button[type="submit"]').addClass('disabled');
	var dataString = $(this).serialize();
	saveQualification(dataString).success(function (data) {
		$('#modal_qualification').modal('hide');
		bootbox.alert('Saved Successfully.', function(){
			window.location.href = window.location.pathname;
		});
	});
});

//modal_work.php
$('#modal_work form').submit(function(e) {
	e.preventDefault();
	$('#modal_work button[type="submit"]').addClass('disabled');
	var dataString = $(this).serialize();
	saveWork(dataString).success(function (data) {
		$('#modal_work').modal('hide');
		bootbox.alert('Saved Successfully.', function(){
			window.location.href = window.location.pathname;
		});
	});
});

//student/applications.php
$('button[name="application"]').click(function(e) {
	var application_id = this.value;
	fetchApplication(application_id).success(function (data) {
		bootbox.alert(data);
		$('.bootbox .modal-dialog').attr('style', 'width: 800px');
	})
});

//student/profile/resumes.php
$(document).ready(function() {
	$('#fetch_resume').submit(function(e) {
		$('#result').addClass('hide');
		$('#showresume').parent('div').removeClass('hide');
		$('#showresume').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
		e.preventDefault();
		var resume_id 	 = $('#resume_id').val();
		window.resume_id = resume_id;
		fetchResume(resume_id).success(function (data) {
			var resume = data[0];
			if(resume.type == 'text'){
				$('#showresume').html(resume.resume);
				$('#showresume').focus();
				//$('button[name="delete"][value="resume"]').attr('id', resume_id);
				$('button[name="save"][value="resume"]').attr('id', resume_id);
			} else{
				$('#showresume').html('<div class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="http://docs.google.com/gview?url=http://assets.swiftintern.com/uploads/files/'+resume.resume+'&embedded=true" frameborder="0"></iframe></div>');
				$('#showresume').attr('contenteditable', 'false');
				$('button[name="save"][value="resume"]').addClass('hide');
			}
		});
	});
	
	$('#uploadResume').submit(function(e) {
		e.preventDefault();
		var dataString = $(this).serialize();
		$(this).ajaxSubmit({
			target: '#output',
			beforeSubmit: filebeforeSubmit,
			url: 'application/controllers/student/action.php',
			type: 'POST',
			data: dataString,
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				$('#output').html('<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="'+percentComplete+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percentVal+'"><span class="sr-only">'+percentVal+' Complete</span></div></div>');
			},
			resetForm: true
		});
	});
});

//student/profile/recommended.php
function loadmoreRecommended(page, per_page) {
	var offset 		= (page-1)*per_page;
	var limit  		= per_page;
	var keyword 	= $('#skills').html();
	var location 	= $('#location').html();

	$('#loadmorebtn').addClass('disabled');
	$('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
	findOpportunity(keyword, location, limit, offset).success(function (data) {
		if (data != '') {
			$.each(data, function (i, item) {
				$('#opportunity').append('<tr><td><div class="media"><a class="pull-left" href="#"><img src="'+item.image+'" class="media-object small_image" alt="'+item.title+'"></a><div class="media-body"><h4 class="media-heading"><a href="'+item.title+'/'+item.id+'">'+item.title+'</a></h4>'+item.eligibility+'</div></div></td><td class="job-location"><p><i class="fa fa-calendar fa-fw"></i> '+item.last_date+'</p><p><i class="fa fa-map-marker fa-fw"></i> '+item.city+'</p></td></tr>');
			});
			$('#loadmorebtn').removeClass('disabled');
			$('#loadmorebtn').html('Load More');
		} else{
			$('#loadmorebtn').addClass('hide');
		};
	});
}

//student/profile/messages.php
function loadmoreMessages(page, property, property_id) {
    var offset  = (page-1)*per_page;
    var limit   = per_page;

    $('#loadmorebtn').addClass('disabled');
    $('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
    fetchMessages(property, property_id, limit, offset, validity).success(function (data) {
        if (data != '') {
            $.each(data, function (i, item) {
                $('#'+property).append('<tr><td>'+item.name+'</td><td>'+item.message+'</td><td><small>'+item.created+'</small></td><td><button name="message" value="'+item.user_id+'" class="btn btn-warning btn-xs"><i class="fa fa-envelope fa-fw"></i> Message</button></td></tr>');
            });
            $('#loadmorebtn_'+property).removeClass('disabled');
            $('#loadmorebtn_'+property).html('Load More');
        } else{
            $('#loadmorebtn_'+property).addClass('hide');
        };
    });
}

//papers/companies.php
function loadmoreExpComp(page, per_page) {
	var offset 	= (page-1)*per_page;
	var limit  	= per_page;

	$('#loadmorebtn').addClass('disabled');
	$('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
	findExpOrg(limit, offset).success(function (data) {
		if (data != '') {
			$.each(data, function(i, item) {
				if(item.name.length > '20'){ var name = item.name.substr(0, 18)+'...';}
				else{ var name = item.name;}
				$('#search_results').append('<div class="col-sm-4 col-md-3"><div class="thumbnail"><img src="'+item.image+'" alt="'+item.name+'"><div class="caption"><p><small>'+name+'</small></p><p><a href="organization/'+encodeURI(item.name)+'/'+item.id+'#experience" class="btn btn-primary btn-sm">Papers</a> <a href="organization/'+encodeURI(item.name)+'/'+item.id+'" class="btn btn-info btn-sm">Profile</a></p></div></div></div>');
			});
			$('#loadmorebtn').removeClass('disabled');
			$('#loadmorebtn').html('Load More');
		} else{
			$('#loadmorebtn').addClass('hide');
		};
	});
}

$(document).ready(function() {
	$('#ExpCompSearch').submit(function(e) {
		e.preventDefault();
		var id 		= $('select').val();
		var name 	= $('select option[value="'+id+'"]').html();
		//alert(name);
		if (name != undefined) {
			window.location.href = 'organization/'+encodeURI(name)+'/'+id;
		}else{
			bootbox.alert('Select Company');
		};
	});
});

//public/blog.php
$(document).ready(function() {
	$('#blog_search').submit(function(e) {
		e.preventDefault();
		var keywords = $('#blog_keywords').val();
		findBlogPost(keywords, '18', '0').success(function (data) {
			$('#search_results').html('');
			$.each(data, function(i, post) {
				$('#search_results').append('<div class="panel panel-info"><div class="panel-heading"><h3 class="panel-title"><a href="blog/'+post.title+'/'+post.id+'">'+post.title+'</a></h3></div><div class="panel-body"><div class="media"><a class="pull-left" href="blog/'+post.title+'/'+post.id+'"><img src="'+post.image+'" alt="'+post.title+'" width="100"></a><div class="media-body">'+post.content+'</div></div></div><div class="panel-footer">By : '+post.author+' at <small>'+post.created+'</small><span class="pull-right"><a class="btn btn-primary btn-sm" href="blog/'+post.title+'/'+post.id+'">Read more</a></span></div></div>')
			});
		});
	});
});

function loadmoreBlogPost(page, per_page) {
	var offset 	= (page-1)*per_page;
	var limit  	= per_page;
	var keyword = $('#blog_keywords').val();

	$('#loadmorebtn').addClass('disabled');
	$('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
	findBlogPost(keyword, limit, offset).success(function (data) {
		if (data != '') {
			$.each(data, function(i, post) {
				$('#search_results').append('<div class="post-preview"><a href="blog/'+post.title+'/'+post.id+'"><h2 class="post-title">'+post.title+'</h2><h3 class="post-subtitle">'+post.content+'</h3></a><p class="post-meta">By : '+post.author+' at <small>'+post.created+'</small></p></div><hr>');
			});
			$('#loadmorebtn').removeClass('disabled');
			$('#loadmorebtn').html('Load More');
		} else{
			$('#loadmorebtn').addClass('hide');
		};
	});
}


//oppotuunity.php
function loadSponsored(page, per_page) {
	var offset 	 = (page-1)*per_page;
	var limit  	 = per_page;
	var status   = '1';
	var validity = '1';
	sponsoredOpportunity(limit, offset, status, validity).success(function (data) {
		$.each(data, function (i, item) {
			$('#sponsored').html('<div class="media"><a class="media-left" href="'+item.title+'/'+item.id+'" onclick="ga("send", "event", "Button", "Click", "Sponsored");"><img src="'+item.image+'" width=60 alt="'+item.title+'"></a><div class="media-body"><p class="media-heading"><a href="'+item.title+'/'+item.id+'" onclick="ga("send", "event", "Button", "Click", "Sponsored");" target="_blank">'+item.title+'</a></p></div></div>');
		});
	});
}

$('button[name="apply"]').click(function(e) {
	var value 	= this.value;
	var id 		= this.id;
	var name 	= this.name;
	var button  = this;
	
	ga('send', 'event', 'Button', 'Click', 'Opportunity', opportunity_id);
	sessionStatus().success(function (data) {
		if (data == 'false') {
			$('#authorization').modal({
				keyboard: true
			});
		}else{
			var user 		= data[0];
			window.user_id 	= user.user_id;
			if (user.type == 'student') {
				var student 	  = data[1];
				window.student_id = student.id;
				switch(value){
					case 'test':
						bootbox.alert('Hi '+user.name+', you have to appear for a test to apply to this opportunity. Select ok to see the test syllabus and then proceed to exam or select cancel if you want to apply later.', function () {
							window.location.href = 'test-details/selction_test/'+id;
						});
						break;
					case 'question':
						fetchItem('question', id).success(function (ques) {
							$('#apply_question_question').html(ques.question);
							$('#apply_question_body input[name="ques_id"]').attr('value', ques.id);
							$('#apply_'+value).modal('show');
						});
						break;
					case 'resume':
						fetchResumes(student_id).success(function (data) {
						if(data != ''){
							$.each(data, function(i, resume) {
								$('#apply_resume_resumes').append('<option value="'+resume.id+'">Resume Id : '+resume.id+'</option>');
							});
						} else{
							$('#apply_resume_resumes').remove();
							$('#fileInput').attr('required', 'true');
						}
						})
						$('#apply_'+value).modal('show');
						break;
					default:
						$('#apply_'+value).modal('show');
						break;
				}
			} else{
				bootbox.alert('Hi '+user.name+' you are not student, only students can apply to this opportunity. please register as a student to apply');
			};
		};
	});
});


//home page
$(document).ready(function() {
	$('#new_category li').click(function(e) {
		$('#opportunity').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
		var keyword  = $(this).children('span').html();
		ga("send", "event", "Button", "Click", "Category", keyword);
		var location = '';
		goTo('#featured');
		findOpportunity(keyword, location, '10', '0').success(function (data) {
			$('#opportunity').html('');
			$('.sub-heading').html('<span id="keyword">'+keyword+'</span><span id="location" class="hide">'+location+'</span><span class="pull-right"><small>'+data.length+' results</small></span>');
			if(keyword.length != '0'){
				window.history.pushState("search", "Title", "search/"+encodeURI(keyword));
				$('title').html(keyword);
			}
			if (data.length != '0') {
				$.each(data, function (i, item) {
					$('#opportunity').append('<tr><td><div class="media"><a class="pull-left" href="#"><img src="'+item.image+'" class="media-object small_image" alt="'+item.title+'"></a><div class="media-body"><h4 class="media-heading"><a href="'+item.title+'/'+item.id+'">'+item.title+'</a></h4>'+item.eligibility+'</div></div></td><td class="job-location"><p><i class="fa fa-calendar fa-fw"></i> '+item.last_date+'</p><p><i class="fa fa-map-marker fa-fw"></i> '+item.city+'</p></td></tr>');
				});
			} else{
				$('#opportunity').append('<tr><td><p>The internship You are Looking Has not Been Found.</p><p>Keep Checking this site to get regular Updates.</p></td></tr>');
			};
		});
	});
	
	$('#navbar_search').submit(function(e) {
		e.preventDefault();
		var keyword = $('#navbar_search_keyword').val();
		window.location.href = 'search/'+keyword;
	});

	$('#advancedsearch').submit(function(e) {
		e.preventDefault();
		$('#opportunity').html('<p class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
		goTo('#featured');
		var keyword 	= $('#advancedsearch input[name="keyword"]').val();
		var location 	= $('#intern_location').val();
		var payment 	= $('#advancedsearch input[name="payment"]').val();
		ga("send", "event", "Form", "Submit", "Search", keyword);
		findOpportunity(keyword, location, '10', '0', payment).success(function (data) {
			$('#opportunity').html('');
			$('.sub-heading').html('<span id="keyword">'+keyword+'</span><span id="location" class="hide">'+location+'</span><span class="pull-right"><small>'+data.length+' results</small></span>');
			if(keyword.length != '0'){
				window.history.pushState("search", "Title", "search/"+encodeURI(keyword));
				$('title').html(keyword);
			}
			if (data.length != '0') {
				$.each(data, function (i, item) {
					$('#opportunity').append('<tr><td><div class="media"><a class="pull-left" href="#"><img src="'+item.image+'" class="media-object small_image" alt="'+item.title+'"></a><div class="media-body"><h4 class="media-heading"><a href="'+item.title+'/'+item.id+'">'+item.title+'</a></h4>'+item.eligibility+'</div></div></td><td class="job-location"><p><i class="fa fa-calendar fa-fw"></i> '+item.last_date+'</p><p><i class="fa fa-map-marker fa-fw"></i> '+item.city+'</p></td></tr>');
				});
			} else{
				$('#opportunity').append('<tr><td><p>The internship You are Looking Has not Been Found.</p><p>Keep Checking this site to get regular Updates.</p></td></tr>');
			};
		});
	});
});

function loadmoreOpportunity(page, per_page) {
	var offset 	= (page-1)*per_page;
	var limit  	= per_page;
	var keyword = $('#keyword').html();

	$('#loadmorebtn').addClass('disabled');
	$('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
	findOpportunity(keyword, '', limit, offset).success(function (data) {
		if (data != '') {
			$.each(data, function (i, item) {
				if(item.last_date < today){
					var expiry = '<span class="label label-warning">Expired</span>';
				}else{
					var expiry = '';
				}
				$('#opportunity').append('<tr><td><div class="media"><a class="pull-left" href="#"><img src="'+item.image+'" class="media-object small_image" alt="'+item.title+'"></a><div class="media-body"><h4 class="media-heading"><a href="'+item.title+'/'+item.id+'">'+item.title+'</a></h4>'+item.eligibility+' '+expiry+'</div></div></td><td class="job-location"><p><i class="fa fa-calendar fa-fw"></i> '+item.last_date+'</p><p><i class="fa fa-map-marker fa-fw"></i> '+item.city+'</p></td></tr>');
			});
			$('#loadmorebtn').removeClass('disabled');
			$('#loadmorebtn').html('Load More');
		} else{
			$('#loadmorebtn').addClass('hide');
		};
	});
}

//test/alltest.php
$(document).ready(function() {
	$('#test_search').submit(function(e) {
		var keywords = $('#test_keywords').val();
		ga("send", "event", "Form", "Submit", "TestSearch", keyword);
		findTest(keywords, '20', '0', '1').success(function (data) {
			$('#search_results').html('');
			if (data != '') {
				$.each(data, function(i, item) {
					$('#search_results').append('<div class="col-sm-4 col-md-3"><div class="thumbnail"><a href="test/'+item.title+'/'+item.id+'"><img src="'+item.image+'" alt="'+item.title+'" width="100"></a><div class="caption"><p><b>'+item.title.substr(0, 21) +'</b></p><p><a href="test-details/'+item.title+'/'+item.id+'" class="btn btn-primary">Details</a> <a href="test/'+item.title+'/'+item.id+'" class="btn btn-success" id="taketest">Start Test</a></p></div></div></div>');
				});
			} else{
				$('#search_results').html(keywords+' not Found. Try Checking Later');
			};
		});
		return false;
	});
});

function loadmoreTest(page, per_page) {
	var offset 	= (page-1)*per_page;
	var limit  	= per_page;
	var keyword = $('#test_keywords').val();

	$('#loadmorebtn').addClass('disabled');
	$('#loadmorebtn').html('<i class="fa fa-spinner fa-spin"></i> Loading');
	findTest(keyword, limit, offset, '1').success(function (data) {
		if (data != '') {
			$.each(data, function(i, item) {
				$('#search_results').append('<div class="col-sm-4 col-md-3"><div class="thumbnail"><a href="test/'+item.title+'/'+item.id+'"><img src="'+item.image+'" alt="'+item.title+'" width="100"></a><div class="caption"><p><b>'+item.title.substr(0, 21) +'</b></p><p><a href="test-details/'+item.title+'/'+item.id+'" class="btn btn-primary">Details</a> <a href="test/'+item.title+'/'+item.id+'" class="btn btn-success" id="taketest">Start Test</a></p></div></div></div>');
			});
			$('#loadmorebtn').removeClass('disabled');
			$('#loadmorebtn').html('Load More');
		} else{
			$('#loadmorebtn').addClass('hide');
		};
	});
}

//test.php
$(document).ready(function() {
	$('#testform').submit(function(e) {
		ga("send", "event", "Form", "Submit", "Test");
		submitTest();
		e.preventDefault();
	});
});

$('#taketest').click(function(e) {
	e.preventDefault();
	sessionStatus().success(function (data) {
		if (data == 'false') {
			$('#authorization').modal({
				keyboard: true
			});
		}else{
			window.location.href = $('#taketest').attr('href');
		};
	});
});

function submitTest() {
	var endtime = new Date().getTime();
	var finaltime = +endtime - +starttime;
	var dataString = $("#testform").serialize().concat('&time_taken='+Math.round(finaltime/1000));
	//alert(dataString);
	$.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: dataString,
		success: function(data) {
			window.location.href = data;
		}
	});
}

//modal experience.php
$(document).ready(function() {
	$('#share_experience form').submit(function(e) {
		e.preventDefault();
		tinyMCE.triggerSave();
		$('#share_experience form button[type="submit"]').addClass('disabled');
		$('#share_experience form button[type="submit"]').html('Adding...');
		var dataString 	= $(this).serialize();
		var details 	= $('#details_experience').val();
		ga("send", "event", "Form", "Submit", "Experience");
		sessionStatus().success(function (data) {
			var user_id = '0'
			if (data != 'false') {
				var user = data[0];
				user_id = user.user_id;
			};
			saveExperience(dataString.concat('&user_id='+user_id, '&details='+escape(details))).success(function (experience_id) {
				$('#share_experience').modal('hide');
				bootbox.alert('Your experince has been submitted and is pending approval. See <a href="experience/company/'+experience_id+'">here</a>')
			})
		});
	});
});

//modal apply_resume.php
$(document).ready(function() {
	$('#apply_resume form').submit(function(e) {
		e.preventDefault();
		$('#apply_resume_submitbtn').addClass('disabled');
		$('#apply_resume_submitbtn').html('<i class="fa fa-spinner fa-spin"></i> Processing');
		var resume_id 		= $('#apply_resume_resumes').val();
		ga("send", "event", "Form", "Submit", "Opportunity", "Resume");
		$(this).ajaxSubmit({
			target: '#output',
			beforeSubmit: filebeforeSubmit,
			url: 'application/controllers/student/action.php',
			type: 'POST',
			data: { student_id: student_id, resume_id: resume_id, opportunity_id: opportunity_id},
			uploadProgress: function(event, position, total, percentComplete) {
				var percentVal = percentComplete + '%';
				$('#output').html('<div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="'+percentComplete+'" aria-valuemin="0" aria-valuemax="100" style="width: '+percentVal+'"><span class="sr-only">'+percentVal+' Complete</span></div></div>');
			},
			resetForm: true
		});
		$('#apply_resume_submitbtn').addClass('hide');
		$('#apply_resume_body').addClass('hide');
		$('#apply_resume_submit').removeClass('hide');
	});
});


//modal apply_question.php
$('#apply_question_answer').submit(function(e) {
	e.preventDefault();
	$('#apply_question_submitbtn').addClass('disabled');
	$('#apply_question_submitbtn').html('<i class="fa fa-spinner fa-spin"></i> Processing');
	var dataString1 	= $('#apply_question_answer').serialize();
	var ques_id 		= $('#apply_question_body input[name="ques_id"]').val();
	ga("send", "event", "Form", "Submit", "Opportunity", "Question");
	createAnswer(dataString1.concat('&user_id='+user_id)).success(function (answer_id) {
		var dataString2 = '&property_id='+answer_id;
		saveApplication(dataString2.concat('&student_id='+student_id, '&opportunity_id='+opportunity_id)).success(function (data) {
			$('#apply_question_submitbtn').addClass('hide');
			$('#apply_question_body').addClass('hide');
			$('#apply_question_submit').removeClass('hide');
		});
	});
});



//controllers
$(function(){
	$('select[value]').each(function(){
		$(this).val(this.getAttribute("value"));
	});
});

$(document).on("click","button[name=message]",function(e){
    $('#message_modal').modal('show');
});

$(document).on("click","button[name=follow]",function(e){
    var following_id = this.id;
    var type 		 = this.value;
    var element 	 = this;
    sessionStatus().success(function (data) {
    	if (data == 'false') {
    		$('#authorization').modal('show');
    	} else{
    		follow(following_id, type).success(function (data) {
    			$(element).attr({
    				id: data,
    				name: 'unfollow'
    			});
    			$(element).html('Unfollow');
    		});
    	};
    });
});

$(document).on("click","button[name=unfollow]",function(e){
	var following_id = '';
    var type 		= this.value;
    var follower_id = this.id;
    var element 	= this;

    deleteItem('follow', follower_id).success(function (data) {
    	$(element).addClass('disable');
    	$(element).html('Unfollowed');
    });
});

function follow(following_id, type, follower_id) {
	var action = 'follow';
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: {action: action, following_id: following_id, type: type, follower_id: follower_id}
	});
}

function goTo(id){
	$('html,body').animate(
		{scrollTop: $(id).offset().top},
		'slow'
	);
}

function parseUrl (sPageURL) {
    var sURLVariables = sPageURL.split('/');
    for (var i = 0; i < sURLVariables.length; i++) {
        if ($.isNumeric(sURLVariables[i])) {
            return sURLVariables[i];
        }
    }
}

//function to check image size before uploading.
function beforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob) {
   		if( !$('#imageInput').val()) {
   			//check empty input filed
   			$("#output").html("Are you kidding me?");
   			return false
   		}

   		var fsize = $('#imageInput')[0].files[0].size; //get file size
   		var ftype = $('#imageInput')[0].files[0].type; // get file type

   		//allow only valid image file types 
   		switch(ftype) {
   			case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
   				break;
   			default:
   				$("#output").html("<b>"+ftype+"</b> Unsupported file type!");
   				return false
   		}

   		//Allowed file size is less than 1 MB (1048576)
   		if(fsize>1048576) {
   			$("#output").html("<b>"+(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
   			return false
        }
        $("#output").html("");
    }else {
        //Output error to older browsers that do not support HTML5 File API
        $("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
        return false;
    }
}

//function to check file size before uploading.
function filebeforeSubmit(){
    //check whether browser fully supports all File API
   if (window.File && window.FileReader && window.FileList && window.Blob) {
   		if( $('#fileInput').val()) {
   			var fsize = $('#fileInput')[0].files[0].size; //get file size
	   		var ftype = $('#fileInput')[0].files[0].type; // get file type

	   		//allow only valid image file types 
	   		switch(ftype) {
	   			case 'application/pdf':
	   				break;
	   			default:
	   				$("#output").html("<b>"+ftype+"</b> Unsupported file type!");
	   				return false
	   		}

	   		//Allowed file size is less than 2 MB (2097152)
	   		if(fsize>2097152) {
	   			$("#output").html("<b>"+(fsize) +"</b> Too big Image file! <br />Please reduce the size of your pdf online.");
	   			return false
	        }
	        $("#output").html("");
   		}
    }else {
        //Output error to older browsers that do not support HTML5 File API
        $("#output").html("Please upgrade your browser, because your current browser lacks some new features we need!");
        return false;
    }
}


function GetURLParameter(param){
	var query = window.location.search.substring(1);
	var query_split = query.split("&");
	for(var i = 0; i < query_split.length; i++){
		var hash_split = query_split[i].split('=');
		if(hash_split[0] === param) 
			return hash_split[1];
	}
	return null;
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

function checkCookie(cname) {
    var user = getCookie(cname);
    if (user != "") {
        return true;
    } else {
        return false;
    }
}

function deleteCookie(name) {
	document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

function sendMessage(user_id, message) {
    var action = 'send_message';
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: {action: action, user_id: user_id, message: message}
    });
}

function saveApplication(dataString) {
    var action = 'update_application';
    var dataString = dataString.concat('&action='+action);
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: dataString
    });
}

function verifyAccount(id, access_token) {
    var action = 'verify_account';
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: {action: action, id: id, access_token: access_token}
    });
}

function resendVerifyEmail(email) {
    var action = 'resend_verify_email';
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: {action: action, email: email}
    });
}

function forgetPassword(email) {
    var action = 'forget_password';
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: {action: action, email: email}
    });
}

function changePassword(id, access_token, password) {
    var action = 'change_password';
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: {action: action, id: id, access_token: access_token, password: password}
    });
}

function updatePassword(password) {
    var action = 'update_password';
    return $.ajax({
        type: "POST",
        url: "application/controllers/student/action.php",
        data: {action: action, password: password}
    });
}

function createAnswer(dataString) {
    var action = 'create_answer';
    var dataString = dataString.concat('&action='+action);
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: dataString
    });
}

function studentRegister(dataString) {
    var action = 'student_register';
    var dataString = dataString.concat('&action='+action);
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: dataString
    });
}

function educatorRegister(dataString) {
    var action = 'educator_register';
    var dataString = dataString.concat('&action='+action);
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: dataString
    });
}

function affiliateRegister(dataString) {
    var action = 'affiliate_register';
    var dataString = dataString.concat('&action='+action);
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: dataString
    });
}

function saveExperience(dataString) {
    var action = 'save_experience';
    var dataString = dataString.concat('&action='+action);
    return $.ajax({
        type: "POST",
        url: "application/controllers/public/action.php",
        data: dataString
    });
}

function findOpportunity(keyword, location, limit, offset, payment) {
	var action = 'find_opportunity';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, limit: limit, offset: offset, keyword: keyword, location: location, payment: payment}
	});
}

function fetchOpportunity (opportunity_id) {
    var action = 'opportunity';
    return $.ajax({
        type: "GET",
        url: "application/controllers/public/action.php",
        data: {action: action, opportunity_id: opportunity_id}
    });
}

function sponsoredOpportunity(limit, offset, status, validity) {
	var action = 'sponsored_opportunity';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, limit: limit, offset: offset, is_active: status, validity: validity}
	});
}

function categoryOpportunity(category, limit) {
	var action = 'category_opportunity';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, category:category, limit: limit}
	});
}

function findOrganization(keyword, limit, offset, validity) {
	var action = 'find_organization';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, limit: limit, offset: offset, keyword: keyword, validity: validity}
	});
}

function fetchOrganization(property, property_id, limit, offset, validity) {
	var action = 'fetch_organization';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, property: property, property_id: property_id, limit: limit, offset: offset, validity: validity}
	});
}

function findUserByEmail(email) {
	var action = 'find_user_by_email';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, email: email}
	});
}

function fetchEduInfo(user_id) {
	var action = 'education_student_info';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, user_id: user_id}
	});
}

function fetchStudentInfo(user_id) {
	var action = 'student_info';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, user_id: user_id}
	});
}

function fetchWorkInfo(user_id) {
	var action = 'work_student_info';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, user_id: user_id}
	});
}

function fetchTestGiven(user_id) {
	var action = 'test_student_info';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, user_id: user_id}
	});
}

function fetchMessages(property, property_id, limit, offset, validity) {
	var action = 'fetch_message';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, property: property, property_id: property_id, limit: limit, offset: offset, validity: validity}
	});
}

function fetchApplications(user_id) {
	var action = 'fetch_applications';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, user_id: user_id}
	});
}

function fetchApplication(application_id) {
    var action = 'fetch_application';
    return $.ajax({
        type: "GET",
        url: "application/controllers/employer/action.php",
        data: {action: action, application_id: application_id}
    });
}

function fetchSkills(user_id) {
	var action = 'fetch_skills';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, user_id: user_id}
	});
}

function fetchResume(resume_id) {
	var action = 'fetch_resume';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, resume_id: resume_id}
	});
}

function fetchResumes(student_id) {
	var action = 'fetch_resumes';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, student_id: student_id}
	});
}

function saveResume(dataString) {
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		dataType: "JSON",
		data: dataString
	});
}

function deleteResume(resume_id) {
	var action = 'delete_resume';
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: {action: action, resume_id: resume_id}
	});
}

function findTest(keywords, limit, offset, validity) {
	var action = 'find_test';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, keywords: keywords, limit: limit, offset: offset, validity: validity}
	});
}

function sessionStatus() {
	var action = 'session_status';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action}
	});
}

function testDetails(test_id) {
	var action = 'test_details';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, test_id: test_id}
	});
}

function findBlogPost(keywords, limit, offset, validity) {
	var action = 'find_blogpost';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, keywords: keywords, limit: limit, offset: offset, validity: validity}
	});
}

function findExpOrg(limit, offset) {
	var action = 'find_experience_org';
	return $.ajax({
		type: "GET",
		url: "application/controllers/public/action.php",
		data: {action: action, limit: limit, offset: offset}
	});
}

function saveQualification(dataString) {
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: dataString
	});
}

function fetchItem(item, id) {
	var action = 'fetch_item';
	return $.ajax({
		type: "GET",
		url: "application/controllers/student/action.php",
		data: {action: action, item: item, id: id}
	});
}

function saveWork(dataString) {
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: dataString
	});
}


function createResumeBasic(dataString) {
	return $.ajax({
		type: "POST",
		url: "application/controllers/public/action.php",
		data: dataString
	});
}

function updateStuProfile(dataString) {
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: dataString
	});
}

function deleteItem(item, id) {
	var action = 'delete_item';
	return $.ajax({
		type: "POST",
		url: "application/controllers/student/action.php",
		data: {action: action, item: item, id: id}
	});
}

function updateItem(dataString) {
	var action = '&action=update_item';
	return $.ajax({
		type: "POST",
		url: "application/controllers/admin/action.php",
		data: dataString.concat(action)
	});
}