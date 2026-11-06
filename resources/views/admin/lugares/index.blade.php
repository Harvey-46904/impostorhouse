
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

    <div class="panel panel-bordered">
        <div class="panel-body">
            <h4>Lugares registrados</h4>

            @forelse($lugares as $lugar)
                <div style="padding:10px; border-bottom:1px solid #ddd;">
                    <strong>{{ $lugar->nombre }}</strong>
                    <span class="label label-success">
                        {{ $lugar->puntos->count() }} puntos
                    </span>
                </div>
            @empty
                <p class="text-muted">No hay lugares registrados.</p>
            @endforelse
        </div>
    </div>

</div>

<style>
    .punto-mapa {
        position:absolute;
        border:2px solid #00ff55;
        background:rgba(0,255,85,.20);
        box-sizing:border-box;
        pointer-events:none;
    }

    .punto-temporal {
        position:absolute;
        border:2px dashed #fff;
        background:rgba(0,255,85,.30);
        pointer-events:none;
    }

    #listaPuntos .punto-item {
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap:10px;
        padding:10px;
        border-bottom:1px solid #ddd;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const inputImagen = document.getElementById('imagen');
    const editor = document.getElementById('editor');
    const mapa = document.getElementById('mapa');
    const rectangulos = document.getElementById('rectangulos');
    const lista = document.getElementById('listaPuntos');
    const inputPuntos = document.getElementById('puntos');

    let puntos = [];
    let dibujando = false;
    let inicioX = 0;
    let inicioY = 0;
    let temporal = null;

    inputImagen.addEventListener('change', function () {

        const archivo = this.files[0];

        if (!archivo) return;

        mapa.src = URL.createObjectURL(archivo);
        editor.style.display = 'block';

        puntos = [];
        actualizar();
    });

    editor.addEventListener('mousedown', function (e) {

        if (e.button !== 0 || !mapa.src) return;

        const rect = editor.getBoundingClientRect();

        inicioX = e.clientX - rect.left;
        inicioY = e.clientY - rect.top;

        dibujando = true;

        temporal = document.createElement('div');
        temporal.className = 'punto-temporal';
        rectangulos.appendChild(temporal);
    });

    editor.addEventListener('mousemove', function (e) {

        if (!dibujando) return;

        const rect = editor.getBoundingClientRect();

        const actualX = Math.max(0, Math.min(
            rect.width, e.clientX - rect.left
        ));

        const actualY = Math.max(0, Math.min(
            rect.height, e.clientY - rect.top
        ));

        const x = Math.min(inicioX, actualX);
        const y = Math.min(inicioY, actualY);
        const ancho = Math.abs(actualX - inicioX);
        const alto = Math.abs(actualY - inicioY);

        temporal.style.left = x + 'px';
        temporal.style.top = y + 'px';
        temporal.style.width = ancho + 'px';
        temporal.style.height = alto + 'px';
    });

    document.addEventListener('mouseup', function () {

        if (!dibujando) return;

        dibujando = false;

        const rect = editor.getBoundingClientRect();

        const x = parseFloat(temporal.style.left) || 0;
        const y = parseFloat(temporal.style.top) || 0;
        const ancho = parseFloat(temporal.style.width) || 0;
        const alto = parseFloat(temporal.style.height) || 0;

        temporal.remove();
        temporal = null;

        if (ancho < 5 || alto < 5) return;

        const nombre = prompt('Nombre del punto de interés:');

        if (!nombre || !nombre.trim()) return;

        puntos.push({
            nombre: nombre.trim(),
            x: x / rect.width * 100,
            y: y / rect.height * 100,
            ancho: ancho / rect.width * 100,
            alto: alto / rect.height * 100
        });

        actualizar();
    });

    function actualizar() {

        rectangulos.innerHTML = '';
        lista.innerHTML = '';

        puntos.forEach((punto, index) => {

            const cuadro = document.createElement('div');
            cuadro.className = 'punto-mapa';

            cuadro.style.left = punto.x + '%';
            cuadro.style.top = punto.y + '%';
            cuadro.style.width = punto.ancho + '%';
            cuadro.style.height = punto.alto + '%';

            rectangulos.appendChild(cuadro);

            const elemento = document.createElement('div');
            elemento.className = 'punto-item';

            const nombre = document.createElement('strong');
            nombre.textContent = (index + 1) + '. ' + punto.nombre;

            const eliminar = document.createElement('button');
            eliminar.type = 'button';
            eliminar.className = 'btn btn-danger btn-sm';
            eliminar.textContent = 'Eliminar';

            eliminar.addEventListener('click', function () {
                puntos.splice(index, 1);
                actualizar();
            });

            elemento.appendChild(nombre);
            elemento.appendChild(eliminar);
            lista.appendChild(elemento);
        });

        inputPuntos.value = JSON.stringify(puntos);

        if (puntos.length === 0) {
            lista.innerHTML =
                '<p class="text-muted">Todavía no hay puntos.</p>';
        }
    }

});
</script>
@stop
