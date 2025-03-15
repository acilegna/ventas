$(document).ready(function () {
    fetch_customer_data();

    function fetch_customer_data(query = "") {
        $.ajax({
            url: "./actions",
            method: "GET",
            data: {
                query: query,
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
                            registross[i]["ticket"]["id"] +
                            "</td> <td>" +
                            registross[i]["venta"]["cantProducts"] +
                            "</td> <td>" +
                            registross[i]["venta"]["hora"] +
                            "</td> <td>" +
                            "$" +
                            registross[i]["venta"]["total"] +
                            "</td></tr>";

                        /*  resultado2 +=
                            "<tr> <td>" +
                            registross[i]["cantidad"] +
                            "</td> <td>" +
                            registross[i]["producto"]["descripcion"] +
                            "</td> <td>" +
                            registross[i]["producto"]["p_venta"] +
                            "</td></tr>"; */
                    }
                    $("#tbody_re").html(resultado);

                    // console.log(registross);

                    $("#labelfolio").text(registross[0]["ticket"]["id"]);
                    //   $("#labelcajero").text(registross[0]["usuario"]["id_employee"]);
                    $("#labelpago").text(registross[0]["venta"]["pago"]);
                    $("#labeltotal").text(registross[0]["venta"]["total"]);
                } else {
                }
            },
        });
    }

    $(document).on("keyup", "#searche", function () {
        var query = $(this).val();
        fetch_customer_data(query);
        if ($("#searche").val().length < 4) {
            $("#tbody_re").html("");
            $("#labelfolio").text("");
        }
    });
});
