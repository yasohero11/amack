<html>
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name','test') }} - @yield('title')</title>
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    @yield('links')
    <script src="https://kit.fontawesome.com/44fb2704eb.js" crossorigin="anonymous"></script>


</head>
<body>
@include('General.parts.admin_nav')
@yield('content')
</body>

@yield('scripts')

</html>
