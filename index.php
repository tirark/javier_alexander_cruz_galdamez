<?php
require("core/conn.php");

    $arreglo = array("success"=>false,
                 "status"=>array("status_code"=>400, "status_text"=>"Bad Request"),
                 "data"=>"", "message"=>"","cant"=>0);
    if($_SERVER["REQUEST_METHOD"]=== "GET")
    {
        if(isset($_GET["type"]) && $_GET["type"]!="")
        {
            $Conexion = new conexion();
            $conn = $Conexion->conectar();
            $datos = $conn->query('SELECT * FROM empleado');
            $resultados = $datos->fetchAll();
            $cantidad =  sizeof($resultados);
            switch($_GET["type"])
            {
                case "json":
                    result_json($resultados);
                break;
                case "xml":
                    result_xml($resultados);
                break;
                default:
                    echo("POR FAVOR DEFINA EL FORMATO DE RESULTADO QYE ESPERA");
                break;
            }
        }
        else
        {
            $contenttype = "Content-Type: application/json";
            $arreglo = array("success"=>false,
                            "status"=>array("status_code"=>412,
                                "status_text"=> "Precondition Failed"
                                ),
                            "data"=>"",
                            "message"=> "SE ESPERABA EL PARAMETRO 'type', CON EL TIPO DE RESULTADO ESPERADO",
                            "cant"=>0
                            );
        }
        echo(json_encode($arreglo));
    }
    else
    {
        $contenttype = "Content-Type: application/json";
        $arreglo = array("success"=>false,
                            "status"=>array("status_code"=>405,
                                "status_text"=> "METHOD NOT ALLOWED"
                                ),
                            "data"=>"",
                            "message"=> "no se acepta un metodo diferente de GET",
                            "cant"=>0
                            );
        echo(json_encode($arreglo));

    }
    
    header($contenttype);
    header("HTTP/1.1 ".$arreglo["status"]["status_code"]." ".$arreglo["status"]["status_text"]);


    function result_json($resultados)
    {
        global $arreglo, $contenttype;
        $contenttype = "Content-Type: application/json";
        $arreglo = array("success"=>true,
                            "status"=>array("status_code"=>200,
                                "status_text"=> "Ok"
                                ),
                            "data"=>$resultados,
                            "message"=> "",
                            "cant"=>sizeof($resultados)
                            );
    }

    function result_xml($resultados)
    {
        global $arreglo, $contenttype;
        $contenttype = "Content-Type: text/xml";
        $xml = new SimpleXMLElement('<empleados/>');
        foreach($resultados as $i => $v){
            $subnodo = $xml->addChild("empleado");
            foreach($v as $clave => $valor){
                $subnodo->addChild($clave, htmlspecialchars((string)$valor));
            }
        }
        header($contenttype);
        echo($xml->asXML());
        $arreglo = array("success"=>true,
                            "status"=>array("status_code"=>200,
                                "status_text"=> "Ok"
                                ),
                            "data"=>"",
                            "message"=> "",
                            "cant"=>sizeof($resultados)
                            );
    }
?>