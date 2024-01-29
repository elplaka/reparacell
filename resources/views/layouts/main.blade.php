<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SOCIO :: REPARACELL</title>
    {{-- Sistema de Organización y Control de Inventarios y Operaciones --}}

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Bootstrap Select CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">

    <!-- Custom styles for this template-->
    <link href="{{ asset('css/sb-admin.min.css')}}" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<style>
    .bg-gradient-navy {
    background: linear-gradient(to bottom, navy, rgb(18, 43, 117)); /*Cambia los colores según tus preferencias*/
}

    /* CSS personalizado para botones extra pequeños */
    .btn-xs-verde {
        padding: 4px 8px; /* Ajusta el relleno según tus preferencias */
        font-size: 10px; /* Ajusta el tamaño de fuente según tus preferencias */
        background-color: darkgreen;
        color: white;
    }

    .select-picker {
    border: 1px solid rgb(93, 90, 90) !important;
    border-radius: inherit !important;
    font-size: 11pt !important;
    height: 30px !important;
    margin-bottom: 5px !important;
    display: flex !important;
    align-items: center !important;
    background-color: #f8f9fa;

    .modal {
            z-index: 1000;
        }

        .collapse {
            z-index: 999;
        }
 }

/* Puedes ajustar el color y otros estilos según tus necesidades */
/* .btn-light.bs-placeholder[title="--TODOS--"] {
  background-color: green !important; 
  color: white !important; 
} */


/* Estilos personalizados para el texto de la opción seleccionada */
.filter-option-inner-inner {
    /* Estilos para el texto de la opción seleccionada, puedes personalizar según tus necesidades */
    color: #495057; /* Color del texto de la opción seleccionada */
    height: 14px;
    line-height: 14px;
    font-size: 11pt;
}

/* .filter-option,
.filter-option:focus,
.filter-option.show,
.filter-option:hover {
    background-color: #f8f9fa;
} */

.filter-option-inner,
.filter-option-inner:show,
.filter-option-inner:focus,
{
    /* Establecer el estilo cuando el elemento tiene el foco (después de hacer clic) */
    color: #000000; /* Color del texto al estar clicado o con foco */
    background-color: #f8f9fa; Color de fondo al estar clicado o con foco
    border: 1px solid transparent; /* Borde transparente */
    /* Agregar otros estilos si es necesario */
}

/*.filter-option-inner-inner,
.filter-option-inner-inner:focus,
.filter-option-inner-inner.show,
.filter-option-inner-inner:hover {
    background-color: #f8f9fa;
} */

/* select::-ms-expand {
  display: none;
} */

</style>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-navy sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fa-solid fa-house"></i>
                    <span>SOCIO :: REPARACELL</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('taller.index') }}">
                    <i class="fa-solid fa-screwdriver-wrench"></i>
                    <span> Taller</span></a>
            </li>

            <hr class="sidebar-divider">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('caja.index') }}">
                    <i class="fa-solid fa-dollar-sign"></i>
                    <span> &nbsp; Caja</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Nav Item - Pages Collapse Menu -->
            @can('configurar-usuarios')
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUser"
                    aria-expanded="true" aria-controls="collapseUser">
                    <i class="fas fa-fw fa-cog"></i>
                    <span> Configuración</span>
                </a>
                <div id="collapseUser" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('usuarios.index') }}"> <i class="fas fa-fw fa-user"></i> Usuarios</a>
                        {{-- <a class="collapse-item" href="cards.html">Editar</a> --}}
                    </div>
                </div>
            </li>
            @endcan
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                {{-- <nav class="navbar navbar-expand navbar-light bg-white topbar static-top" style='margin-bottom: -50px !important;'> --}}
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">

   
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                                     <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                        {{ __('Cerrar Sesión') }}
                                    </a>
                                    
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>

                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('content')
                </div>
                
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistema de Organización y Control de Inventarios y Operaciones </span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    {{-- <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a> --}}

    <script src="{{ asset('js/jquery.js') }}"></script>
    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

<script>
        window.addEventListener('contentChanged', function () {
            $('.selectpicker').selectpicker();
        });
    </script>

    <script>
        document.addEventListener('livewire:initialized', function () {
                Livewire.on('abreModalEquiposCliente', () => {
                    $('#equiposClienteModal').modal('show');
                })
           });
    </script>

    <script>
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('cerrarModalBuscarCliente', () => {
            document.getElementById('btnCerrarBuscarClienteModal').click();
                })
        });
        </script>

    <script>
        document.addEventListener('livewire:initialized', function () {
                Livewire.on('cerrarModalEquiposCliente', () => {
            document.getElementById('btnCerrarEquiposClienteModal').click();
                })
        });
        </script>

    <script>
        document.addEventListener('livewire:initialized', function () {
                Livewire.on('lanzaAdvertenciaEquipoTaller', () => {
                    $('#warningEquipoTallerModal').modal('show');
                })
        });
    </script>

<script> //Abre la ventana modal y habilita la visualización de los selectpicker
    document.addEventListener('livewire:initialized', function () {
            Livewire.on('lanzaCobroModal', () => {
                $('#cobroTallerModal').modal('show');
            })

            Livewire.on('cierraCobroModal', () => {
                $('#cobroTallerModal').modal('hide');
            })
    });
</script>
    <script>
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('mostrarToast', (attr) => {
                Swal.fire({
                    position: 'bottom-end',
                    width: 300,
                    icon: 'success',
                    title: attr[0],
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        title: 'swal2-title-custom', // Clase CSS personalizada para el título
                        content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                        icon: 'fa-xs'
                        },
                    })
            });
            
        });
    </script>

    <script>
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('mostrarToastError', (attr) => {
                Swal.fire({
                    position: 'center',
                    width: 300,
                    icon: 'error',
                    title: attr[0],
                    showConfirmButton: false,
                    timer: 2000,
                    customClass: {
                        title: 'swal2-title-custom', // Clase CSS personalizada para el título
                        content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                        icon: 'fa-xs'
                        },
                    })
            });
        });
    </script>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('mostrarToastErrorRouteInventario', (attr) => {
            Swal.fire({
            title: attr[0],
            icon: 'error',
            showDenyButton: true,
            confirmButtonText: "Aceptar",
            // denyButtonText: '<i class="fa-solid fa-boxes-stacked"></i> Ir a inventario',
            denyButtonText: attr[1],
            customClass: {
                title: 'swal2-title-custom', // Clase CSS personalizada para el título
                content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                icon: 'fa-xs'
                },
            }).then((result) => {
            // /* Read more about isConfirmed, isDenied below */
            if (result.isDenied) {
                window.location.href = '{{ route('taller.index') }}';
            }
            });
        });
    });
</script>
</body>
</html>
