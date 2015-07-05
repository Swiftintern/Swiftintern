(function () {
    try {
        var oldOnLoad = window.onload;
        window.onload = function () {
            var pph = document.createElement('iframe');
            var useSSL = 'https:' == document.location.protocol;
            var org = document.getElementById('swiftintern').getAttribute("data-org");
            pph.src = (useSSL ? 'https:' : 'http:') + '//swiftintern.com/employer/widget/'+ org +'?width=245&height=320';
            pph.setAttribute('width', '245');
            pph.setAttribute('height', '320');
            pph.setAttribute('frameborder', 0);
            pph.setAttribute('scrolling', 'no');
            pph.setAttribute('allowtransparency', 'true');
            pph.setAttribute('style', 'border: 0; overflow: hidden');
            document.getElementById('swiftintern').appendChild(pph);
            if (typeof oldOnLoad == 'function')
                oldOnLoad();
        }
    } catch (e) {
        if (typeof console !== undefined && console.log) {
            var stackTrace = e.stack || e.message;
            if (stackTrace !== undefined)
                console.log(e.stack);
        }
    }
})(document);