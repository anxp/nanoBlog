<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Admin Panel</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-4">
        </div>
        <div class="col-md-4">
            <br><br>
            <div class="border border-primary rounded p-3" id="loginFormDiv">
            <form role="form" onsubmit="return sendForm()">
                <div class="form-group">

                    <label for="inputLogin">
                        Login:
                    </label>
                    <input type="text" name="username" class="form-control" id="inputLogin" placeholder="login" required>
                </div>
                <div class="form-group">

                    <label for="inputPassword">
                        Password:
                    </label>
                    <input type="password" name="userpass" class="form-control" id="inputPassword" placeholder="password" required>
                </div>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="checkbox" id="chkBox"> Check me out
                    </label>
                </div>
                <button type="submit" class="btn btn-primary">
                    Enter Admin Panel
                </button>
            </form>
            </div>
        </div>
        <div class="col-md-4">
        </div>
    </div>
</div>
<script src="../libjs/shakeobject.js"></script>
<script>
    // TODO: replace with something more serious
    var hash = function(s) {
        /* Simple hash function. */
        var a = 1, c = 0, h, o;
        if (s) {
            a = 0;
            /*jshint plusplus:false bitwise:false*/
            for (h = s.length - 1; h >= 0; h--) {
                o = s.charCodeAt(h);
                a = (a<<6&268435455) + o + (o<<14);
                c = a & 266338304;
                a = c!==0?a^c>>21:a;
            }
        }
        return String(a);
    };

    //function creates XMLHttpRequest object if it is possible, else (maybe old Internet Explorer) it creates ActiveXObject
    function getXMLHttpRequest() {
        if (window.XMLHttpRequest) {
            return new XMLHttpRequest();
        }

        return new ActiveXObject('Microsoft.XMLHTTP');
    }

    function sendForm() {
            var userName = (document.getElementById('inputLogin')).value; //get username
            var passHash = hash((document.getElementById('inputPassword')).value); //get password hash
            var checkBox = (document.getElementById('chkBox')).checked; //get checkbox status
            checkBox = checkBox ? 1 : 0; //convert boolean to 1 or 0

            //create object, which will contain all info from form:
            var formObject = {
                username:userName,
                userpass:passHash,
                checkbox:checkBox,
            };

            //convert object with form data to json-string:
            var jsonEncodedFormData = JSON.stringify(formObject);

            request = getXMLHttpRequest();
            request.open('POST', 'loginchecker.php', true); //Prepare our request
            request.setRequestHeader('Content-type', 'application/json'); //because we'll send JSON as raw data to php://input
            request.onreadystatechange = function() {
                if (request.readyState === 4)  {
                    var serverResponse = JSON.parse(request.responseText);
                    if(serverResponse['message'] === 'LoginError') {

                        //if authentication not passed, apply some visual effect and reset fields
                        shakeElement(document.getElementById('loginFormDiv'), 20, false);
                        document.getElementById('inputLogin').value = '';
                        document.getElementById('inputPassword').value = '';
                        document.getElementById('chkBox').checked = false;
                    } else if (serverResponse['message'] === 'Continue') {

                        //if authentication passed, we get URL (in server JSON-reply) to redirect to
                        window.location.href = serverResponse['url'];
                    } else {

                        //in any other case just show the message
                        alert('Error, try to reload the page');
                    }
                }
            };
            request.send(jsonEncodedFormData);
            return false;
    }

</script>
</body>
</html>