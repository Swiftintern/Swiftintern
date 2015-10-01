var current = new Date();
current.setSeconds(current.getSeconds() + 1800);

var date = current.toLocaleDateString(), // output => "dd/mm/yy"
    time = current.toLocaleTimeString(); //   "hh:mm:ss AM/PM"


    var arr = date.split("/");
    var stamp = arr[2] + "/" + arr[1] + "/" + arr[0];
    stamp += " ";

    var t = time.split(" ");
    
    if (t[1] == "PM" || t[1] == "pm") {
        var ch = t[0].split(":");
        ch[0] = Number(ch[0]) + 12;
        t[0] = ch.join(":");
    }
    stamp += t[0];

jQuery(document).ready(function($) {
  $(window).on('load', function () {
    var labels = ['', '', 'hours', 'minutes', 'seconds'],
      nextYear = stamp,
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
  });

});