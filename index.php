<?php
require_once("core/core.php");
$objController = new buscador_controller($arrRolUser);
$objController->runAjax();
$objController->drawContentController();

class buscador_controller{
    private $objModel;
    private $objView;

    public function __construct($arrRolUser)
    {
        $this->objModel = new buscador_model();
        $this->objView = new buscador_view($arrRolUser);
        $this->arrRolUser = $arrRolUser;
    }

    public function drawContentController()
    {
        $this->objView->drawContent();
    }

    public function runAjax()
    {
        $this->searchPrestamo();
    }

    public function searchPrestamo(){
        if (isset($_GET["search"])) {
            $term = trim($_GET["search"]);
            $data = $this->objModel->getDataPrestamo($term);
            print json_encode($data);
            exit();
        }
    }
}

class buscador_model{

    public function getDataPrestamo($term){
        if($term!=''){
            $conn = getConexion();
            $strQuery = " SELECT prestamo.id,
                                 asociado.nombres AS nombre_asociado,
                                 prestamo.numero,
                                 prestamo.estado_prestamo,
                                 prestamo.dias_mora_capital,
                                 prestamo.capital_vencido
                            FROM prestamo
                                 INNER JOIN asociado ON prestamo.asociado = asociado.id
                           WHERE prestamo.numero LIKE '%$term%' 
                           ORDER BY prestamo.numero"; 
            $result = mysqli_query($conn, $strQuery);
            if (!empty($result)) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $resultado[]=$row;
                }
            }
            return $resultado;
        }
    }
}

class buscador_view{
    private $objModel;

    public function __construct(){
        $this->objModel = new buscador_model();
    }

    public function drawContent(){
        ?>
        <!doctype html>
        <html lang="en">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <meta name="description" content="">
                <meta name="author" content="Jorge Tunchez">
                <meta name="generator" content="Hugo 0.88.1">
                <title>Consulta de Préstamos</title>
                <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/navbar-fixed/">

                <!-- Bootstrap core CSS -->
                <link href="css/bootstrap.min.css" rel="stylesheet">
                <link href="css/jquery-ui.css" rel="stylesheet">

                <!-- Favicons -->
                <link rel="icon" href="img/logox64.png">
                <meta name="theme-color" content="#7952b3">
                <style>
                    .bd-placeholder-img {
                        font-size: 1.125rem;
                        text-anchor: middle;
                        -webkit-user-select: none;
                        -moz-user-select: none;
                        user-select: none;
                    }

                    @media (min-width: 768px) {
                        .bd-placeholder-img-lg {
                            font-size: 3.5rem;
                        }
                    }

                    /* Show it is fixed to the top */
                    body {
                        min-height: 75rem;
                        padding-top: 4.5rem;
                    }
                </style>
                <script src="js/jquery-3.6.0.js"></script>
                <script src="js/jquery-ui.js"></script>
                <script src="js/bootstrap.bundle.min.js"></script>
            </head>
            <body> 
                <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#">Consulta de Préstamos</a>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarCollapse"></div>
                    </div>
                </nav>
                <main class="container">
                    <div class="bg-light p-5 rounded">
                        <h1>Consulta de Préstamos</h1>
                        <p class="lead">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="input-group mb-3 ui-widget">
                                        <input type="text" id="detalle_auto" class="form-control" placeholder="Ingrese un numero de préstamo..." aria-label="Ingrese numero de prestamo" aria-describedby="button-addon1">
                                    </div>
                                </div>
                            </div>
                            <div class="row container" id="div_resultado" style="display: none; margin-top:3%">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Número Préstamo</th>
                                            <th>Asociado</th>
                                            <th>Estado</th>
                                            <th>Dias Mora</th>
                                            <th>Capital Vencido</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div id="div_id_numero"></div>
                                            </td>
                                            <td>
                                                <div id="div_id_asociado"></div>
                                            </td>
                                            <td>
                                                <div id="div_id_estado"></div>
                                            </td>
                                            <td>
                                                <div id="div_id_dias"></div>
                                            </td>
                                            <td>
                                                <div id="div_id_capital"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </p>
                    </div>
                </main>
                <script>
                    $( function() {
                        $("#detalle_auto").autocomplete({
                            source:function(request,response){
                                $.ajax({
                                    url:"index.php",
                                    type:"GET",
                                    dataType:"json",
                                    data:{
                                        search: request.term
                                    },
                                    success:function(data){
                                        if(data != ''){
                                            $("#div_resultado").show();
                                            response($.map(data, function (item) {
                                                return {
                                                    label: item.numero+' - '+item.nombre_asociado,
                                                    value: item.numero,
                                                    nombre_asociado: item.nombre_asociado,
                                                    numero: item.numero,
                                                    estado_prestamo: item.estado_prestamo,
                                                    dias_mora_capital: item.dias_mora_capital,
                                                    capital_vencido: item.capital_vencido
                                                }
                                            }));
                                        }else{
                                            $("#div_resultado").hide();
                                        }
                                    }
                                })
                            },
                            minLength: 5,
                            select: function (event, ui) {
                                $("#detalle_auto").val("");
                                $("#div_id_asociado").text(ui.item.nombre_asociado.toUpperCase());
                                $("#div_id_numero").text(ui.item.numero);
                                $("#div_id_estado").text(ui.item.estado_prestamo);
                                $("#div_id_dias").text(ui.item.dias_mora_capital);
                                $("#div_id_capital").text('Q.'+ui.item.capital_vencido);
                                return false;
                            },
                            open: function() {
                                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
                            },
                            close: function() {
                                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
                            }
                        });
                    } );
                </script>
            </body>
        </html>
        <?php
    }
}
?>