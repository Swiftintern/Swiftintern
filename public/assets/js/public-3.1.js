(function (window, Model) {
    window.request = Model.initialize();
    window.opts = {};
}(window, window.Model));

//Google Tag Manager
(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KFFXD4');

$(document).ready(function () {

    var dt = new Date(),
            starttime = dt.getTime();
    window.opts.time = starttime;

    $('#new_category li').click(function (e) {
        window.opts.query = $(this).children('span').html();
        window.opts.page = '1';
        $('#results').html('');
        $('#results').html('<p id="loader" class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
        loadOpportunities(opts);
    });

    $('#search_opportunities').submit(function (e) {
        e.preventDefault();
        window.opts = {};
        var data = $(this).serializeArray();
        $.each(data, function () {
            if (window.opts[this.name] !== undefined) {
                if (!window.opts[this.name].push) {
                    window.opts[this.name] = [window.opts[this.name]];
                }
                window.opts[this.name].push(this.value || '');
            } else {
                window.opts[this.name] = this.value || '';
            }
        });
        $('#results').html('');
        $('#results').html('<p id="loader" class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
        loadOpportunities(window.opts);
        $('#loader').html('');
    });

    $('#load_opportunities').click(function (e) {
        $(this).html('<i class="fa fa-spinner fa-spin"></i> Loading');
        loadOpportunities(window.opts);
        $(this).html('Load More');
    });

    setInterval(loadSponsored({
        limit: 3
    }), 5000);

    $('#apply_internship').click(function (e) {
        var self = this,
                path = window.location.pathname.split("/");
        $(self).addClass('disabled');
        $(self).html('<i class="fa fa-spinner fa-spin"></i>  | Processing...');

        request.read({
            action: 'students',
            data: {},
            callback: function (person) {
                request.create({
                    action: path[1] + '/' + path[2] + '/' + path[3],
                    data: {
                        action: "application",
                        opportunity_id: path[3]
                    },
                    callback: function (data) {
                        console.log(data);
                        $(self).html('Applied Successfully');
                    }
                });
            }
        });

    });

    //onlinetest
    $('#search_test').submit(function (e) {
        e.preventDefault();
        window.opts = {};
        var data = $(this).serializeArray();
        $.each(data, function () {
            if (window.opts[this.name] !== undefined) {
                if (!window.opts[this.name].push) {
                    window.opts[this.name] = [window.opts[this.name]];
                }
                window.opts[this.name].push(this.value || '');
            } else {
                window.opts[this.name] = this.value || '';
            }
        });
        $('#results').html('');
        $('#results').html('<p id="loader" class="text-center"><i class="fa fa-spinner fa-spin fa-5x"></i></p>');
        loadTests(window.opts);
        $('#loader').html('');
    });

    $('#load_test').click(function (e) {
        $(this).html('<i class="fa fa-spinner fa-spin"></i> Loading');
        loadTests(window.opts);
        $(this).html('Load More');
    });

    //test
    $('.prevQues').click(function (e) {
        e.preventDefault();
        var ques = window.opts.ques,
                prev = "#question_" + (+ques + -1),
                cur = "#question_" + ques,
                self = this;
        if ($(prev).length > 0) {
            $(cur).addClass('hide');
            $(prev).removeClass('hide');

            window.opts.ques = --ques;
        } else {
            $(self).addClass('disabled');
        }
        console.log(window.opts.ques);
    });

    $('.nextQues').click(function (e) {
        e.preventDefault();
        var ques = window.opts.ques,
                next = "#question_" + (+ques + +1),
                cur = "#question_" + ques,
                self = this;
        if ($(next).length > 0) {
            $(cur).addClass('hide');
            $(next).removeClass('hide');

            window.opts.ques = ++ques;
        } else {
            $(self).addClass('disabled');
        }
        console.log(window.opts.ques);
    });

    $('#test').submit(function (e) {
        e.preventDefault();
        var test_id = $('input[name="test_id"]').val();

        var test = data[0];
        if (test.time_limit != '0') {
            var timeSpent = test.time_limit;
            function countdown() {
                console.log(timeSpent);
                timeSpent -= 1;
                if (timeSpent > 0) {
                    setTimeout(countdown, 1000);
                }
                if (timeSpent == 1) {
                    alert('submitted');
                    submitTest();
                }
                ;
            }
            setTimeout(countdown, 1000);
        }
    });

    //footer ads onclick close and save for session
    $('.close').click(function () {
        $('#footerAds').hide();
        console.log('footer hide');
        setCookie('footerAds', '413', '1');
    });

    var footerAds = getCookie("footerAds");
    if (footerAds) {
        $('#footerAds').hide();
        console.log('footer hide');
    }

});

function findResume(resumes) {
    var id = "";
    $.each(resumes, function (i, resume) {
        if (resume._type == "file") {
            id = resume._id;
        }
    });
    return id;
}

function loadOpportunities(opts) {
    request.read({
        action: "home/index",
        data: opts,
        callback: function (data) {
            $('#loader').html('');
            if (data.count > 1) {
                $.each(data.opportunities, function (i, opportunity) {
                    $('#results').append('<tr><td><div class="media"><a class="pull-left hidden-xs" href="' + encodeURI(opportunity._title) + '/' + opportunity._id + '"><img src="/organizations/photo/' + opportunity._organization_id + '" class="media-object small_image" alt="' + opportunity._title + '"></a><div class="media-body"><h4 class="media-heading"><a href="' + opportunity._type + '/details/' + encodeURI(opportunity._title) + '/' + opportunity._id + '">' + opportunity._title + '</a></h4>' + opportunity._eligibility + '</div></div></td><td class="job-location"><p><i class="fa fa-calendar fa-fw"></i>' + opportunity._last_date + '</p><p><i class="fa fa-map-marker"></i>' + opportunity._location + '</p></td></tr>');
                });
            } else {
                $('#results').append("No Results Found, Check later");
            }
        }
    });
}

function loadTests(opts) {
    request.read({
        action: "onlinetest/index",
        data: opts,
        callback: function (data) {
            $('#loader').html('');
            console.log(data);
            if (data.count >= 1) {
                $.each(data.exams, function (i, exam) {
                    $('#results').append('<div class="col-sm-4 col-md-3"><div class="thumbnail"><a href="test/' + encodeURI(exam._title) + '/' + exam._id + '"><img src="/onlinetest/photo/' + exam._id + '" alt="' + exam._title + '" width="100"></a><div class="caption"><p><b>' + exam._title + '</b></p><p><a href="/onlinetest/details/' + encodeURI(exam._title) + '/' + exam._id + '" class="btn btn-primary btn-xs">Details</a><a href="/test/' + encodeURI(exam._title) + '/' + exam._id + '" class="btn btn-success btn-xs" id="taketest">Start Test</a></p></div></div></div>');
                });
            } else {
                $('#results').append("No Results Found, Check later");
            }
        }
    });

}

function login() {
    request.read({
        action: "students/register",
        callback: function (data) {
            if (data.url) {
                //set session redirect

                //redirect
                window.location.href = data.url;
            }
        }
    });
}

function loadSponsored(opts) {
    request.read({
        action: "app/sponsored",
        data: opts,
        callback: function (data) {
            if (data.sponsoreds) {
                $.each(data.sponsoreds, function (i, item) {
                    $('#sponsored').html('<div class="media"><a class="media-left" href="' + item._type + '/details/' + encodeURI(item._title) + '/' + item._id + '"><img src="/organizations/photo/' + item._organization_id + '" width=60 alt="' + item.title + '"></a><div class="media-body"><p class="media-heading"><a href="' + item._type + '/details/' + encodeURI(item._title) + '/' + item._id + '" target="_blank">' + item._title + '</a></p></div></div>');
                });
            }
        }
    });
}

function createApplication(opts) {
    console.log(opts);
    request.create({
        action: opts.url,
        data: opts,
        callback: function (data) {

        }
    });
}

function toObject(data) {
    var obj = {};
    $.each(data, function () {
        if (obj[this.name] !== undefined) {
            if (!obj[this.name].push) {
                obj[this.name] = [obj[this.name]];
            }
            obj[this.name].push(this.value || '');
        } else {
            obj[this.name] = this.value || '';
        }
    });
    return obj;
}

function studentLogin(action) {
    var redirectUri = window.location.pathname;
    var data;
    if (redirectUri === '/students/register') {
        data = 'action=' + action;
    } else {
        data = 'action=' + action + '&redirectUrl=' + redirectUri;
    }

    request.read({
        action: "/students/register",
        data: data,
        callback: function (data) {
            if (data.url) {
                //redirect
                window.location.href = data.url;
            }
        }
    });
}

function getCookie(name) {
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0)
            return null;
    }
    else {
        begin += 2;
        var end = document.cookie.indexOf(";", begin);
        if (end == -1) {
            end = dc.length;
        }
    }
    return unescape(dc.substring(begin + prefix.length, end));
}

function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function listCookies() {
    var theCookies = document.cookie.split(';');
    var aString = '';
    for (var i = 1; i <= theCookies.length; i++) {
        aString += i + ' ' + theCookies[i - 1] + "\n";
    }
    return aString;
}

function cancelTest() {
    var location = window.location.pathname;
    var participant_id = $("#testForm").attr("action").split("/").pop();
    request.create({
        action: location,
        data: {action: 'cancelTest', id: participant_id},
        callback: function (d) {
            if (d.canceled) {
                window.location.href = '/onlinetest/result/' + participant_id;
            }
        }
    });
}

function testimonials () {
    $("#testimonials").html('<i class="fa fa-spinner fa-spin"></i>  | Processing...');

    request.read({
        action: 'home/testimonials',
        data: {},
        callback: function (data) {
            
        }
    });
}

//Start of Zopim Live Chat Script
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute("charset","utf-8");
$.src="//v2.zopim.com/?3RxEIkzsUZsvxcaF9qoCaxQcPVvDYeJJ";z.t=+new Date;$.
type="text/javascript";e.parentNode.insertBefore($,e)})(document,"script");