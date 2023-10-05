<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mono Connect test</title>
    <style>
        .p-5 {
            padding: 5em;
        }
    </style>
    <script type="application/javascript" src="https://connect.withmono.com/connect.js"></script>
</head>
<body>
<div className="p-5">
    <p>Welcome to Mono Connect.</p>
    <br><br>
    check mono status
        <form action="{{route('mono.check.status')}}" method="get">
            @csrf                
                <input type="submit" value="Mono Status">
            </form>
    <br><br>

    <button id="launch-btn">Link a financial account</button>
    <br><br>
        <div style="display: none;" id="showform">
        Authenticate User
        <form action="{{route('mono.auth')}}" method="post">
            @csrf

                <input type="text" name="auth_code" value="">
                <input type="submit" value="Authenticate User">
            </form>
        </div>
        <br><br>
          Check if banks Match
        <form action="{{route('mono.verify.bank')}}" method="post">
            @csrf

                <input type="text" name="id" value="">
                <input type="submit" value="Check if Banks match">
            </form>
        <br><br>
        <div>
        <form action="{{route('mono.statement')}}" method="post">
            @csrf
                <label for="">Statemnt Retrieval</label>
                <input type="text" name="monoId" value="">
                <input type="submit" value="Retrieve Statement">
            </form>
        </div>
    
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="application/javascript">
  const copyToClipboard = text => {
    const elm = document.createElement('textarea');
    elm.value = text;
    document.body.appendChild(elm);
    elm.select();
    document.execCommand('copy');
    document.body.removeChild(elm);
  };
  var connect;
  var config = {
    key: "test_pk_HTZPginrdstquRoyYwGY",
    onSuccess: function (response) {
      copyToClipboard(response.code);
      console.log(JSON.stringify(response));
      alert(JSON.stringify(response));
      $("#showform").show();
      $("#launch-btn").hide();
      /**
       response : { "code": "code_xyz" }
       you can send this code back to your server to get this
       authenticated account and start making requests.
       */
    },
    onClose: function () {
      //console.log('user closed the widget.')
    }
  };
  connect = new Connect(config);
  connect.setup();
  var launch = document.getElementById('launch-btn');
  launch.onclick = function (e) {
    connect.open();
  };
</script>
</body>
</html>