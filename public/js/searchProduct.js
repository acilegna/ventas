$(document).ready(function () {
    $("#busquedas").click(function () {
        $("#modelHeading").html("Busqueda de Productos");
        $("#buscaProducto").val("");
        $("#tbodyBuscar").html("");
        $("#ajaxModel").modal("show");
    });

    fetch_customer_data();

    function fetch_customer_data(query = "") {
        $.ajax({
            url: "./producto",
            type: "GET",
            data: {
                query: query,
            },
            dataType: "json",
            success: function (data) {
                var registros = eval(data.table_data);
                var total = eval(data.total_data);
                resultado = "";
                if (total >= 1) {
                    for (var i = 0; i < registros.length; i++) {
                        resultado +=
                            "<tr> <td>" +
                            registros[i]["codigo"] +
                            "</td> <td>" +
                            registros[i]["descripcion"] +
                            "</td> <td>" +
                            registros[i]["p_venta"] +
                            "</td> <td>" +
                            registros[i]["existencia"] +
                            "</td> <td><input type = 'hidden' name = 'codigoProducto' value = " +
                            registros[i]["codigo"] +
                            "> " +
                            "<a data - toggle='tooltip' data - placement='bottom' title = 'Agregar a venta' > " +
                            "<button class='btn bordes' type='submit'  id='addOne' name='addOne' value='0'><i class='fa fa-cart-plus'></i> </button></a>" +
                            "<a data-toggle='tooltip' data-placement='bottom' title='Agregar Inventario' href='./viewInv/" +
                            registros[i]["id"] +
                            "'>" +
                            "<button class='btn bordes' type='button' name='accion' value='agrega'><i class='fa fa fa-plus-square'></i> </button></a>" +
                            "</td></tr>";
                    }

                    $("#tbodyBuscar").html(resultado);
                } else {
                    $("#mensaje").text(
                        "Registro no encontrado en la Base de Datos"
                    );
                    $("#mensaje").addClass("alert alert-danger");
                }

                // $("#tbodyBuscar").html(data.table_data);
                 //$( '#total_reco' ).text( data.total_data );
            },
        });
    }
    $(document).on("keyup", "#buscaProducto", function () {
        var query = $(this).val();
        fetch_customer_data(query);
        if ($("#buscaProducto").val().length < 4) {
            $("#tbodyBuscar").html("");
        }
    });
});
