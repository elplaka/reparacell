@extends('layouts.main')

@php
    $azulPastel = '#1759DE';
    $naranjaPastel = '#E78903';
    $amarilloPastel = '#B2B409';
    $verdePastel = '#52C70B';
    $moradoPastel = '#8417DE';
    $rosaPastel = '#C71F0B';
@endphp


<style>

    /* Estilo base del botón */
    .boton-oxford {
        background-color: #333; /* Color de fondo gris oscuro */
        color: #fff; /* Color del texto blanco */
        border: none; /* Sin borde */
        cursor: pointer; /* Cambiar el cursor al pasar por encima */
        border-radius: 4px; /* Esquinas redondeadas */
        padding: 6px 6px; /* Padding reducido */
        margin: 4px; /* Margen reducido */
        transition: background-color 0.3s ease; /* Transición suave para el color de fondo */
    }

    /* Cambiar el color de fondo al pasar el mouse por encima */
    .boton-oxford:hover {
        background-color: #555; /* Color de fondo ligeramente más claro al pasar el mouse */
    }

    .icono-smartphone {
    font-size:9pt; /* Ajusta el tamaño de fuente según tus necesidades */
}

.input-height {
        height: 2em !important;
    }

.select-height {
    height: 30px !important; 
    padding-top: 3px !important; 
    padding-bottom: 5px !important; 
    padding-left: 10px !important; 
    padding-right: 10px !important;
    color: rgb(70, 70, 70);
    font-size: 11pt !important;
}

.boton-gris {
    background-color: #ccc; /* Color de fondo gris claro */
    border: none; /* Sin borde */
    cursor: pointer; /* Cambiar el cursor al pasar por encima */
    padding: 1px 6px; /* Ajusta el relleno según tus necesidades */
    border-radius: 4px; /* Esquinas redondeadas */
    display: inline-block;
    transition: background-color 0.3s ease; /* Transición suave para el color de fondo */
    text-align: center; /* Centrar el contenido horizontalmente */
}

/* Estilo del signo "+" */
.plus-sign {
    font-size: 16px; /* Tamaño del signo "+" (ajusta según tus necesidades) */
    font-weight: bold; /* Hace que el signo "+" sea más visible */
    color: #333; /* Color del signo "+" (puedes cambiarlo si lo deseas) */
}

.ocultar-ventana-modal {
    display: none;
}

.col-md-05 {
    width: 5%; /* Puedes ajustar el porcentaje según tu preferencia */
}

.col-md-15 {
    width: 10%; /* Puedes ajustar el porcentaje según tu preferencia */
}

/* Modifica el tamaño de la fuente del título */
.swal2-title-custom {
  font-size: 16px; /* Cambia el tamaño de fuente a tu preferencia */
}

/* Modifica el tamaño de la fuente del contenido */
.swal2-content-custom {
  font-size: 16px; /* Cambia el tamaño de fuente a tu preferencia */
}



    .custom-status-color-1 {
        border-left: 5px solid {{ $azulPastel }};
        border-right: 5px solid {{ $azulPastel }};
    }

    .custom-status-color-2 {
        border-left: 5px solid {{ $naranjaPastel }};
        border-right: 5px solid {{ $naranjaPastel }};
    }

    .custom-status-color-3 {
        border-left: 5px solid {{ $amarilloPastel }};
        border-right: 5px solid {{ $amarilloPastel }};
    }

    .custom-status-color-4 {
        border-left: 5px solid {{ $moradoPastel }};
        border-right: 5px solid {{ $moradoPastel }};
    }

    .custom-status-color-5 {
        border-left: 5px solid {{ $verdePastel }};
        border-right: 5px solid {{ $verdePastel }};
    }

    .custom-status-color-6 {
        border-left: 5px solid {{ $rosaPastel }};
        border-right: 5px solid {{ $rosaPastel }};
    }

    .custom-status-icon-color-1{
        color: {{ $azulPastel }};
    }

    .custom-status-icon-color-2{
        color: {{ $naranjaPastel }};
    }

    .custom-status-icon-color-3{
        color: {{ $amarilloPastel }};
    }

    .custom-status-icon-color-4{
        color: {{ $moradoPastel }};
    }

    .custom-status-icon-color-5{
        color: {{ $verdePastel }};
    }

    .custom-status-icon-color-6{ 
        color: {{ $rosaPastel }};
    }

    
    .custom-badge-color-1 {
        background-color: {{ $azulPastel }};
    }

    .custom-badge-color-2 {
        background-color: {{ $naranjaPastel }};
    }

    .custom-badge-color-3 {
        background-color: {{ $amarilloPastel }};
    }

    .custom-badge-color-4 {
        background-color: {{ $moradoPastel }};
    }

    .custom-badge-color-5 {
        background-color: {{ $verdePastel }};
    }

    .custom-badge-color-6 {
        background-color: {{ $rosaPastel }};
    }

.label-button {
    background: none;
    border: none;
    padding: 0;
    cursor: pointer;
}

.label-icon {
    font-size: 24px; /* Tamaño del icono de edición */
    transition: color 0.3s; /* Transición de color al pasar el mouse por encima */
}

</style>

@section('content')
    @livewire('productosReportes')
@endsection

