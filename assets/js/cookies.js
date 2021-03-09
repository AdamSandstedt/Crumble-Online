// Returns the value of the given cookie on the current page
// Arguments: name of the cookie to find
function getCookie(cname) {
  var name = cname + "=";
  var decodedCookie = decodeURIComponent(document.cookie);
  var ca = decodedCookie.split(';');
  for(var i = 0; i <ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

// Creates a javascript cookie
// Arguments: cookie name, cookie value, number of hours until it expires, path the cookie should belong to
// if exhours isn't provided, the cookie will expire when the browser is closed
// path defaults to "/" which means it is available in every page
function setCookie(cname, cvalue, exhours, path = "/") {
  var cookie = cname + "=" + cvalue;
  if(exhours) {
    var d = new Date();
    d.setTime(d.getTime() + (exhours * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    cookie += ";" + expires;
  }
  cookie += ";path=" + path;
  document.cookie = cookie;
}
