<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form method="POST" action="{{ route('login.author') }}">
    @csrf

    <input type="text" name="email">

    <input type="text" name="password">
    <button type="submit"> test</button>

</form>

</body>
</html>
