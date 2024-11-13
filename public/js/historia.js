$(document).ready(function () {
    get_products();

    function get_products(query = "", user = "") {
        $.ajax({
            url: "./actio",
            method: "GET",
            data: {
                query: query,
                user: user,
            },
            dataType: "json",
            success: function (data) {
                var registross = eval(data.table_data);
                var totalH = eval(data.total_data);
                resultado = "";
                //   alert(registross);
                if (totalH >= 1) {
                    for (var i = 0; i < registross.length; i++) {
                        resultado +=
                            "<tr> <td>" +
                            registross[i]["cantidad"] +
                            "</td> <td>" +
                            registross[i]["producto"]["descripcion"] +
                            "</td> <td>" +
                            registross[i]["producto"]["p_venta"] +
                            "</td></tr>";
                        console.log(registross);
                    }
                    $("#tbodytwo").html(resultado);
                    $("#labelcajero").text(
                        registross[0]["usuario"]["lastname"]
                    );
                } else {
                }
            },
        });
    }
    $(document).on("keyup", "#searche", function () {
        var query = $(this).val();
        var user = $("#user").val();
        get_products(query, user);
        if ($("#searche").val().length < 4) {
            $("#tbodytwo").html("");
        }
    });

    $(document).on("change", "#user", function () {
        //referencia al elemento que se le está aplicando el evento.
        var user = $(this).val();
        var query = $("#searche").val();

        //pasar parametro a la funcion
        get_products(query, user);
    });
});
