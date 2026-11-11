
@extends('voyager::master')

@section('page_title', 'Editor de lugares')

@section('content')
<div class="container-fluid">

    <h1 class="page-title">
        <i class="voyager-map"></i> Editor de lugares
    </h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="panel panel-bordered">
        <div class="panel-body">

            <form action="{{ route('voyager.lugares.guardar') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                <div class="form-group">
                    <label>Nombre del lugar</label>
                    <input type="text"
                           name="nombre"
                           class="form-control"
                           required>
                </div>

                <div class="form-group">
                    <label>Imagen del mapa</label>
                    <input type="file"
                           id="imagen"
                           name="imagen"
                           class="form-control"
                           accept="image/png,image/jpeg,image/webp"
                           required>
                </div>

                <div id="editor"
                     style="position:relative; width:100%; display:none;
                            overflow:hidden; cursor:crosshair;
                            background:#171717;">

                    <img id="mapa"
                         style="width:100%; display:block;
                                pointer-events:none; user-select:none;">

                    <div id="rectangulos"
                         style="position:absolute; inset:0;">
                    </div>

                </div>

                <input type="hidden" name="puntos" id="puntos">

                <div class="clearfix" style="margin-top:20px;">
                    <h4>Puntos de interés</h4>
                    <div id="listaPuntos">
                        <p class="text-muted">Todavía no hay puntos.</p>
                    </div>
                </div>

                <button type="submit" class="btn btn-success"
                        style="margin-top:20px;">
                    <i class="voyager-check"></i> Guardar lugar
                </button>

            </form>
        </div>
    </div>

    

</div>


@stop
