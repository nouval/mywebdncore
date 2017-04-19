(function () {
  var retrieve = document.getElementById('retrieve'),
    results = document.getElementById('results'),
    toReadyStateDescription = function (state) {
      switch (state) {
      case 0:
        return 'UNSENT';
      case 1:
        return 'OPENED';
      case 2:
        return 'HEADERS_RECEIVED';
      case 3:
        return 'LOADING';
      case 4:
        return 'DONE';
      default:
        return '';
      }
    };

    retrieve.addEventListener('click', function (e) {
        var oReq = new XMLHttpRequest();
        oReq.onload = function (e) {
            var xhr = e.target;
            if (xhr.responseType === 'json') {
                results.innerHTML = xhr.response.message;
            } else {
                console.log(xhr);
                results.innerHTML = JSON.parse(xhr.responseText).message;
            }        
            console.log('Inside the onload event');
        };
        oReq.onreadystatechange = function () {
          console.log('Inside the onreadystatechange event with readyState: ' +
            toReadyStateDescription(oReq.readyState));
        };
        oReq.open('GET', e.target.dataset.url, true);
        oReq.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        oReq.responseType = 'json';
        oReq.send();
    });
}());