$(document).ready(function () {
    fetch_customer_data();

    function fetch_customer_data(query = "", user = "") {
        $.ajax({
            url: "./gets",
            method: "GET",
            data: {
                query: query,
            },
            dataType: "json",
            success: function (data) {
                var registross = eval(data.table_data);
                var totalH = eval(data.total_data);
                resultado = "";
                resultado2 = "";
                console.log(registross);

                if (totalH >= 1) {
                    for (var i = 0; i < registross.length; i++) {
                        resultado2 +=
                            "<tr> <td>" +
                            registross[i]["cantidad"] +
                            "</td> <td>" +
                            registross[i]["producto"]["descripcion"] +
                            "</td> <td>" +
                            registross[i]["producto"]["p_venta"] +
                            "</td></tr>";

                        $("#labeltotal").text(registross[i]["venta"]["total"]);
                        $("#labelpago").text(registross[i]["venta"]["pago"]);
                        $("#labelcajero").text(
                            registross[i]["ticket"]["user"]["lastname"]
                        );
                    }
                    resultado +=
                        "<tr> <td>" +
                        registross[0]["ticket"]["id"] +
                        "</td> <td>" +
                        registross[0]["venta"]["cantProducts"] +
                        "</td> <td>" +
                        registross[0]["venta"]["hora"] +
                        "</td> <td>" +
                        "$" +
                        registross[0]["venta"]["total"] +
                        "</td></tr>";

                    $("#tbody_re").html(resultado);
                    $("#tbodytwo").html(resultado2);

                    $("#labelfolio").text(registross[0]["ticket"]["id"]);
                } else {
                }
            },
        });
    }
    $(document).on("change", "#user", function () {
        //referencia al elemento que se le está aplicando el evento.
        var user = $(this).val();
        var query = $("#searche").val();

        fetch_customer_data(query, user);
    });

    $(document).on("keyup", "#searche", function () {
        var query = $(this).val();
        var user = $("#user").val();
        fetch_customer_data(query, user);
        // console.log($("#searche").val().length);
        if ($("#searche").val().length < 2) {
            $("#tbody_re").html("");
            $("#tbodytwo").html("");
            $("#labelfolio").text("");
            $("#labeltotal").text("");
            $("#labelpago").text("");
            $("#labelcajero").text("");
            //$("#user").val($("#user option:first").val());
        }
    });
});
