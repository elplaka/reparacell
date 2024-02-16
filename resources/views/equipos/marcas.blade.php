@extends('layouts.main')

<style>

.input-height {
        height: 2em !important;
    }

.select-height {
    height: 2em !important; 
    padding-top: 3px !important; 
    padding-bottom: 5px !important; 
    padding-left: 10px !important; 
    padding-right: 10px !important;
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
    @livewire('equipoMarcas')
@endsection