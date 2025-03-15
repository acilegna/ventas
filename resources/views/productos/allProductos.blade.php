@extends("panel.panel")
@section("content")
	<div class="container-fluid">
		<h3 id="title-prod">Módulo de Productos</h3>
		<div class="contenido ">
			<div class="col-lg-6">
				<h5 id="subtitle-prod" class="izquierda">Consulta General de Productos</h5>
			</div>
			<div class="col-lg-2">
				<input type="text" name="inputSearch" id="search" class="derecha" placeholder="Busqueda.. " />
			</div>
			<div class="col-lg-4">
				<div class="btn-group derecha"><a href="{{ route("altaProd") }}" class="btn btn-primary"><i class="fa fa-cube"></i>
						Registrar Productos</a> </div>
			</div>
		</div>

		<div class="table-responsive-lg ">
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th>Codigo</th>
						<th>Descripcion</th>
						<th>Categoria</th>
						<th>Costo</th>
						<th>Existencia</th>
						<th id="mitable">Acciones</th>
					</tr>
				</thead>
				<tbody id="tbody">

				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<h5 class="izquierda">Registros encontrados: <span id="total_products"></span></h5>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
@endsection
