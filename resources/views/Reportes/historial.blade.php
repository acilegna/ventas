@extends("panel.panel")
@section("content")
	<div class="container-fluid  ">
		<div class="col-sm-6 col-md-6">
			<div class="row">
				<div class="col">
					<div class="input-group">
						<label class="txtlabel">Folio</label>
						<input type="text" class="form-control" name="searche" id="searche" placeholder="Busqueda..">
					</div>

					<div class="table-responsive-lg ">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Ticket</th>
									<th>Articulos</th>
									<th>Hora</th>
									<th>Total</th>
								</tr>
							</thead>
							<tbody id="tbody_re">
							</tbody>

						</table>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col">
					<div class="input-group">
						<label class="txtlabel">Del dia</label>

						<input type="text" class="form-control pull-right" name="datepicker_2" id="datepicker_2" readonly>
					</div>
					<div class="input-group">
						<label class="txtlabel">Cajero</label>

						<select id="user" name="user" class='form-control'>
							<option value="opc">Selecciona un Cajero </option>
							@foreach ($usuarios as $user)
								<option value="{{ $user->id_employee }}">{{ $user->lastname }}</option>
							@endforeach
							<option value="all">Todos</option>
						</select>
					</div>
					<div class="input-group">
						<label class="txtlabel">De la caja</label>
						<select id="caja" name="caja" class='form-control'>
							<option value="opc">Selecciona una caja </option>
							@foreach ($caja as $cajas)
								<option value="{{ $cajas->id }}">{{ $cajas->descripcion }}</option>
							@endforeach
							<option value="all">Todos</option>
						</select>
					</div>
				</div>

			</div>
		</div>
		<div class="col-1 col-md-1"></div>
		<div class="col-5 col-md-5">
			<div class="row">
				<div class="col">
					<div class="input-group">
						<label class="txtlabel">Folio: <a id="labelfolio"></a></label>
					</div>
					<div class="input-group">
						<label class="txtlabel">Cajero: <a id="labelcajero"></a> </label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<div class="table-responsive-lg">
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th>Cantidad</th>
									<th>Descripcion</th>
									<th>Importe</th>
								</tr>
							</thead>
							<tbody id="tbodytwo">
							</tbody>
							<tfoot>
								<tr>
									<th colspan="7">
										<button><a href="{{ route("gets") }}"></a>Devolver articulo seleccionado</button>
										<div class="btn-group fl-rigth"><a href="{{ route("gets") }}" class="btn btn-success"><span
													class="fa fa-mail-reply"></span> Regresar</a> </div>

										<div class="input-group">
											<label class="txtlabel">Pago con: <a id="labelpago"></a></label>
										</div>
										<div class="input-group">
											<label class="txtlabel">Total: <a id="labeltotal"></a></label>
										</div>
									</th>

								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
