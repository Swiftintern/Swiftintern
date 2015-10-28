function setEndTime(time) {
  if (!time || time == "00:00:00") {
    time = "00:30:00";
  }
  time = time.split(":");
  var sec = Number(time[0]) * 3600 + Number(time[1]) * 60 + Number(time[2]);

  var end = new Date();
  end.setSeconds(end.getSeconds() + sec);

  var stamp = end.getFullYear() + "/" + (end.getMonth() + 1) + "/" + end.getDate();
  stamp += " ";
  stamp += end.getHours() + ":" + end.getMinutes() + ":" + end.getSeconds();

  return stamp;  
}

var warn = 0;
function navigateAway() {
  if (warn > 1) {
    cancelTest();
    alert("Your Test is now cancelled");
  } else {
    alert("You are not allowed to navigate away during the test.");  
  }
  ++warn;
}


function submitTest() {
  $("#testForm").submit();
}

jQuery(document).ready(function($) {
  window.opts.ques = 1;
    
  $(window).blur(navigateAway);

  $(window).on('load', function () {
    var labels = ['', '', 'hours', 'minutes', 'seconds'],
      nextYear = setEndTime($('#testTimeLimit').html()), // output => yy/mm/dd HH:mm:ss (24 hrs)
      template = _.template($('#main-example-template').html()),
      currDate = '00:00:00:00:00',
      nextDate = '00:00:00:00:00',
      parser = /([0-9]{2})/gi,
      $example = $('#main-example');
    // Parse countdown string to an object
    function strfobj(str) {
      var parsed = str.match(parser),
        obj = {};
      labels.forEach(function (label, i) {
        if (label)
          obj[label] = parsed[i];
      });
      return obj;
    }
    // Return the time components that diffs
    function diff(obj1, obj2) {
      var diff = [];
      labels.forEach(function (key) {
        if (obj1[key] !== obj2[key]) {
          diff.push(key);
        }
      });
      return diff;
    }
    // Build the layout
    var initData = strfobj(currDate);
    labels.forEach(function (label, i) {
      if (label) {
        $example.append(template({
          curr: initData[label],
          next: initData[label],
          label: label
        }));
      }
    });
    // Starts the countdown
    $example.countdown(nextYear, function (event) {
      var newDate = event.strftime('%w:%d:%H:%M:%S'),
        data;
      if (newDate !== nextDate) {
        currDate = nextDate;
        nextDate = newDate;
        // Setup the data
        data = {
          'curr': strfobj(currDate),
          'next': strfobj(nextDate)
        };
        // Apply the new values to each node that changed
        diff(data.curr, data.next).forEach(function (label) {
          var selector = '.%s'.replace(/%s/, label),
            $node = $example.find(selector);
          // Update the node
          $node.removeClass('flip');
          $node.find('.curr').text(data.curr[label]);
          $node.find('.next').text(data.next[label]);
          // Wait for a repaint to then flip
          _.delay(function ($node) {
            $node.addClass('flip');
          }, 50, $node);
        });
      }
    });

    $example.on('finish.countdown', submitTest);
  });

});