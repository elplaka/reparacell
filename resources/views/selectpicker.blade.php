{{-- <!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selectpicker con Íconos</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
    
    <link href="{{ asset('css/sb-admin.min.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
</head>
<body>

    <select id="selectModoPago2" class="selectpicker select-picker" data-live-search="true">
        <option value="1" data-content="<i class='fa-solid fa-house'></i> &nbsp; HOME">Home</option>
        <option value="2" data-content="<i class='fa-solid fa-user'></i> &nbsp; USER">User</option>
        <option value="3" data-content="<i class='fa-solid fa-cog'></i> &nbsp; SETTINGS">Settings</option>
    </select>

    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/sb-admin.min.js') }}"></script>

 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#selectModoPago2').selectpicker();
            console.log('selectpicker inicializado');
        });
    </script>
</body>
</html> --}}

<!-- resources/views/taller/index.blade.php -->
@extends('layouts.main')

@section('title', 'Selectpicker Test')

@section('content')
    <!-- El selectpicker -->
    <select id="selectModoPago2" class="selectpicker" data-live-search="true">
        <option value="1" data-content="<i class='fa-solid fa-house'></i> &nbsp; HOME">Home</option>
        <option value="2" data-content="<i class='fa-solid fa-user'></i> &nbsp; USER">User</option>
        <option value="3" data-content="<i class='fa-solid fa-cog'></i> &nbsp; SETTINGS">Settings</option>
    </select>
@endsection

<script>
    $(document).ready(function() {
        $('#selectModoPago2').selectpicker();
        console.log('selectpicker inicializado');
    });
</script>


