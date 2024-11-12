$(document).ready(function () {
    fetch_customer_data();

    function fetch_customer_data(query = "", user = "") {
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
    $(document).on("change", "#user", function () {
        //referencia al elemento que se le está aplicando el evento.
        //var date1 = $(this).val();

        var query = $(this).val();
        var user = $(this).val();
        
        //pasar parametro a la funcion
        fetch_customer_data(query, user);
    });
    $(document).on("keyup", "#searche", function () {
        var query = $(this).val();
        var user = $("#user").val();
        fetch_customer_data(query, user);
        if ($("#searche").val().length < 4) {
            $("#tbodytwo").html("");
        }
    });
});
