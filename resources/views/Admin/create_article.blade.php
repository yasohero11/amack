<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form method="POST"  enctype="multipart/form-data" action="{{ route('create.article') }}" >
    @csrf

    <input type="text" name="title">

    <input type="text" name="description">


    <input type="file"  name="image">
    <label>featured </label>
    <input type="checkbox" name="featured" value=1>
    <label>active </label>
    <input type="checkbox" name="active" value=1>
    <button type="submit"> test</button>

</form>

</body>
</html>
