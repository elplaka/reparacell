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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
    margin-bottom: 1px !important;
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

/* Estilo cuando el select está enfocado */
.selectpicker:focus {
    border-color: #007bff; /* Cambia el color del borde cuando está enfocado */
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); /* Cambia la sombra cuando está enfocado */
}

/* Estilo cuando el select está activo (haciendo clic) */
.selectpicker:active {
    border-color: #007bff; /* Cambia el color del borde cuando está activo */
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); /* Cambia la sombra cuando está activo */
}

/* Opcional: Estilo cuando el mouse está sobre el select */
.selectpicker:hover {
    border-color: #007bff; /* Cambia el color del borde cuando está en hover */
    box-shadow: 0 0 10px rgba(0, 123, 255, 0.5); /* Cambia la sombra cuando está en hover */
}



/* Estilos personalizados para el texto de la opción seleccionada */
.filter-option-inner-inner {
    /* Estilos para el texto de la opción seleccionada, puedes personalizar según tus necesidades */
    color: #495057; /* Color del texto de la opción seleccionada */
    height: 14px;
    line-height: 14px;
    font-size: 11pt;
}

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

.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

input[type="date"] {
    cursor: pointer;
    transition: background-color 0.1s ease;
}

input[type="date"]:hover {
    background-color: #dbdfee;
}

.hover-bg {
            cursor: pointer;
            transition: background-color 0.1s ease, color 0.1s ease;
        }

.hover-bg:hover {
    background-color: #dbdfee;
}

.select-hover {
    cursor: pointer;
    background-color: #ffffff; /* Color gris claro */
    transition: background-color 0.3s ease; /* Suavizar la transición */
}

.select-hover:hover {
    background-color: #dbdfee; /* Color al pasar el mouse por encima */
}

.select-hover option {
    background-color: #fff; /* Color de fondo blanco para los elementos de la lista */
    cursor: pointer;
}

.custom-option {
    cursor: pointer;
}

.custom-option:hover {
    background-color: #dbdfee;
}

.swal2-confirm {
    display: inline-block !important; /* Ensure it's displayed */
    visibility: visible !important; /* Ensure it's visible */
    opacity: 1 !important; /* Ensure it's opaque */
    color: white !important; /* Ensure the text color contrasts with blue */
    background-color: #1e40af !important; /* Force the blue background */
    /* Add other basic button styles if needed (padding, border, etc.) */
    padding: 0.4em 1em !important;
    border: none !important;
    border-radius: 0.25em !important;
    font-size: 1em !important;
}


</style>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav bg-gradient-navy sidebar sidebar-dark accordion" id="accordionSidebar">
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fa-solid fa-house"></i>
                    <span>SOCIO :: REPARACELL</span></a>
            </li>

            <hr class="sidebar-divider">
 
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReparaciones"
                    aria-expanded="true" aria-controls="collapseReparaciones" data-key="1">
                    <i class="fa-solid fa-toolbox"></i>
                    <span> Reparaciones </span>
                </a>
                <div id="collapseReparaciones" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('taller.index') }}" data-key="T"> <i class="fa-solid fa-screwdriver-wrench"></i> Taller <small>[ T ]</small></a>
                        <a class="collapse-item" href="{{ route('reparaciones.reportes') }}" data-key="S"> <i class="fa-solid fa-file-invoice"></i> Reportes <small>[ S ]</small> </a>
                        <a class="collapse-item" href="{{ route('taller.creditos') }}" data-key="O"> <i class="fa-solid fa-credit-card"></i> Créditos <small>[ O ]</small> </a>
                    </div>                    
                </div>
            </li>

            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVentas"
                    aria-expanded="true" aria-controls="collapseVentas" data-key="2">
                    <i class="fa-solid fa-dollar-sign"> </i>
                    <span> Ventas </span>
                </a>
                <div id="collapseVentas" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('caja.index') }}" data-key="C"> <i class="fa-solid fa-cash-register"></i> Caja <small>[ C ]</small></a>
                        <a class="collapse-item" href="{{ route('ventas.index') }}" data-key="G"> <i class="fa-solid fa-table"></i> Registros <small>[ G ]</small> </a>
                        <a class="collapse-item" href="{{ route('ventas.creditos') }}" data-key="D"> <i class="fa-solid fa-credit-card"></i> Créditos <small>[ D ]</small> </a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEquipos"
                    aria-expanded="true" aria-controls="collapseEquipos" data-key="3">
                    <i class="fa-solid fa-mobile-retro"></i>
                    <span> Equipos </small></span>
                </a>
                <div id="collapseEquipos" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('equipos.index') }}" data-key="E">                     <i class="fa-solid fa-rectangle-list"></i> Catálogo <small>[ E ]</small></a>
                        <a class="collapse-item" href="{{ route('equipos.tipos') }}" data-key="I">                     <i class="fa-solid fa-microchip"></i> Tipos <small>[ I ]</small></a>
                        <a class="collapse-item" href="{{ route('equipos.marcas') }}" data-key="M">                     <i class="fa-solid fa-splotch"></i> Marcas <small>[ M ]</small> </a>
                        <a class="collapse-item" href="{{ route('equipos.modelos') }}" data-key="L">                     <i class="fa-solid fa-bookmark"></i> Modelos <small>[ L ]</small> </a>
                       <a class="collapse-item" href="{{ route('equipos.fallas') }}" data-key="F">                     <i class="fa-solid fa-plug-circle-exclamation"></i> Fallas <small>[ F ]</small></a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProductos"
                    aria-expanded="true" aria-controls="collapseProductos" data-key="4">
                    <i class="fa-solid fa-kitchen-set"></i>
                    <span> Productos</span>
                </a>
                <div id="collapseProductos" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('productos.index') }}" data-key="P">                     
                        <i class="fa-solid fa-boxes-stacked"></i> Inventario <small>[ P ]</small> </a>
                        <a class="collapse-item" href="{{ route('productos.reportes') }}" data-key="R"> <i class="fa-solid fa-file-invoice"></i> Reportes <small>[ R ]</small></a>
                        </a> 
                        <a class="collapse-item" href="{{ route('productos.departamentos') }}" data-key="A">                     <i class="fa-solid fa-layer-group"></i> Departamentos <small>[ A ]</small></a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseClientes"
                    aria-expanded="true" aria-controls="collapseClientes" data-key="5">
                    <i class="fa-solid fa-address-book"></i>
                    <span> Clientes</span>
                </a>
                <div id="collapseClientes" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('clientes.index') }}" data-key="K">                     <i class="fa-solid fa-elevator"></i> Índice <small>[ K ]</small></a>
                        <a class="collapse-item" href="{{ route('clientes.historial') }}" data-key="H">                     <i class="fa-solid fa-clock-rotate-left"></i> Historial <small>[ H ]</small></a>
                    </div>
                </div>
            </li>

            <hr class="sidebar-divider">

            @can('configurar-usuarios')
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUser"
                    aria-expanded="true" aria-controls="collapseUser" data-key="6">
                    <i class="fas fa-fw fa-cog"></i>
                    <span> Configuración</span>
                </a>
                <div id="collapseUser" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('usuarios.index') }}" data-key="U"> <i class="fas fa-fw fa-user"></i> Usuarios <small>[ U ]</small></a>
                    </div>
                </div>
            </li>
            @endcan
        </ul>

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar static-top">

                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                            </a>
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

            <div class="container-fluid">
                    @yield('content')
                </div>
            </div>

            <livewire:modal-inicializacion-caja />

            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistema de Organización y Control de Inventarios y Operaciones </span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="{{ asset('js/sb-admin.min.js') }}"></script> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            Livewire.on('cerrarModalCreditosTallerHistorial', () => {
            document.getElementById('btnCerrarCreditosTallerHistorialModal').click();
                })
        });
        </script>

    <script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalCreditosVentasHistorial', () => {
        document.getElementById('btnCerrarCreditosVentasHistorialModal').click();
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

            Livewire.on('abreCobroCreditoTallerModal', () => {
                $('#cobroCreditoTallerModal').modal('show');
            })
    });

    document.addEventListener('abreCobroCreditoTallerModal2', function () {
        $('#cobroCreditoTallerModal').modal('show');
    });

   
    document.addEventListener('abreCobroCreditoVentasModal2', function () {
        $('#ventaCreditoModal').modal('show');
    });
</script>


    <script>
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('mostrarToast', (attr) => {
                Swal.fire({
                    position: 'bottom-end',
                    width: 300,
                    icon: 'success',
                    text: attr[0],
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
                    width: 400,
                    icon: 'error',
                    title: attr[0],
                    showConfirmButton: true,
                    showCancelButton: false,
                    customClass: {
                            title: 'swal2-title-custom', // Clase CSS personalizada para el título
                            content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                            icon: 'fa-xs',
                        },
                    })
            });
        });

        document.addEventListener('livewire:initialized', function () {
            Livewire.on('mostrarToastErrorRoute', (attr) => {
                Swal.fire({
                    position: 'center',
                    width: 400,
                    icon: 'error',
                    title: attr[0],
                    // confirmButtonText: "Save",
                    showConfirmButton: true,
                    confirmButtonColor: "#3085d6",
                    // timer: 6000, // Duración del Toast en milisegundos
                    // timerProgressBar: true,
                    customClass: {
                        title: 'swal2-title-custom', // Clase CSS personalizada para el título
                        content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                        icon: 'fa-xs'
                        },
                    willClose: () => {
                        // Redirecciona después de que el Toast se cierra
                        window.location.href = attr[1];
                    }
                });
            });
        });

        document.addEventListener('livewire:initialized', function () {
            Livewire.on('mostrarToastAceptarCancelar', (attr) => {
                Swal.fire({
                    title: attr[0],
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(attr[1]);
                    }
                });
            });
        });

        document.addEventListener('livewire:initialized', function () {
            Livewire.on('mostrarToastAceptarCancelarParam', attr => {
                Swal.fire({
                    customClass: {
                        title: 'swal2-title-custom', // Clase CSS personalizada para el título
                        content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                        icon: 'fa-xs'
                        },
                    title: attr[0],
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        Livewire.dispatch(attr[1], {'idTipo' : attr[2], 'idMarca' : attr[3], 'idModelo' : attr[4], 'idCliente' : attr[5] });
                    }
                    
                });
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
            denyButtonText: '<i class="fa-solid fa-boxes-stacked"></i> Ir a inventario',
            denyButtonText: attr[1],
            confirmButtonColor: "#3085d6",
            denyButtonColor: "#FF0000",
            customClass: {
                title: 'swal2-title-custom', // Clase CSS personalizada para el título
                // content: 'swal2-content-custom', // Clase CSS personalizada para el contenido
                icon: 'fa-xs'
                },
            }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isDenied) {
                window.location.href = '{{ route('productos.index') }}';
            }
            });
        });
    });
</script>

<script>
    document.addEventListener('keydown', function(event) {
        if (event.altKey) {
            const key = event.key.toUpperCase();
            const link = document.querySelector(`a[data-key="${key}"]`);
            if (link) {
                event.preventDefault();
                link.click();
            }
        }
    });    
</script>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('cerrarModalInicializarCaja', () => {
            $('#inicializarCajaModal').modal('hide');
        });
    });
</script>



</body>
</html>
