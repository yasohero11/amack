<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>
        @yield('title')
    </title>
    <!--     Fonts and icons     -->

    <link
        href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|Noto+Sans:300,400,500,600,700,800|PT+Mono:300,400,500,600,700"
        rel="stylesheet"/>
    <!-- Nucleo Icons -->
    <link href="{{asset('css/nucleo-icons.css')}}" rel="stylesheet" type="text/css"/>
    <link href="{{asset('css/nucleo-icons.css')}}" rel="stylesheet"/>
    <link href="{{asset('css/nucleo-svg.css')}}" rel="stylesheet"/>
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/349ee9c857.js" crossorigin="anonymous"></script>
    <link href="{{asset('css/nucleo-svg.css')}}" rel="stylesheet"/>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{asset('css/corporate-ui-dashboard.css')}}" rel="stylesheet"/>
    <style>
        .nav-item .nav-link.active i{
            color: #774dd3 !important;
        }

    </style>
    @yield('links')
</head>

<body class="g-sidenav-show  bg-gray-100">
@include('General.parts.dashboard.side-nav')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    @include('General.parts.dashboard.top-nav')
    <!-- End Navbar -->
    @yield("content")

</main>

<!--   Core JS Files   -->
<script src="{{asset('js/core/popper.min.js')}}"></script>
<script src="{{asset('js/core/bootstrap.min.js')}}"></script>
<script src="{{asset('js/plugins/perfect-scrollbar.min.js')}}"></script>
<script src="{{asset('js/plugins/smooth-scrollbar.min.js')}}"></script>
<script src="{{asset('js/plugins/chartjs.min.js')}}"></script>
<script src="{{asset('js/plugins/swiper-bundle.min.js')}}" type="text/javascript"></script>
@yield("js")
<script>


    const dialog = document.querySelector('dialog');
    document.querySelectorAll(".dialog-range").forEach(e=>{
        e.addEventListener("input" ,evt=>{
            document.querySelector(`#p-${evt.target.name}`).innerText = evt.target.value
        })
    })
    if(dialog) {
        dialog.addEventListener('keydown', (event) => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
        dialog.addEventListener('click', (event) => {
            if (event.target === dialog) {
                try{
                    if(closeModel)
                        closeModel()
                    else
                        dialog.close();
                }catch (e) {
                    dialog.close();
                }

            }
        });
    }


</script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Corporate UI Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{asset('js/corporate-ui-dashboard.min.js?v=1.0.0')}}"></script>
</body>

</html>
