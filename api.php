<?php

header("Content-Type: application/json;charset=utf-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

require "config.php";

$rota = $_GET["rota"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];
$corpo = json_decode(file_get_contents("php://input"), true) ?? [];

function teste(){
    echo json_encode(["mensagem" => "API do Instagram funcionando!"]);
    exit;
}
function createUsuario($con, $dados){
    $stmt = $con->prepare(
        "INSERT INTO usuarios (nome, username, email, senha) VALUES (?,?,?,?)"
    );

    $stmt->execute([
        $dados["nome"],
        $dados["username"],
        $dados["email"],
        password_hash($dados["senha"], PASSWORD_DEFAULT)
    ]);

    http_response_code(201);
    echo json_encode(["mensagem" => "Usuario criado com sucesso", "id" => $con->lastInsertId()]);
}
function readUsuario($con){
    $stmt = $con->query("SELECT id_usuario, nome, username, email, data_cadastro FROM usuarios");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function updateUsuario($con, $id, $dados){
    $stmt = $con->prepare(
        "UPDATE usuarios SET nome = ?, username = ?, email = ? WHERE id_usuario = ?"
    );

    $stmt->execute([
        $dados["nome"],
        $dados["username"],
        $dados["email"],
        $id
    ]);

    echo json_encode(["mensagem" => "Usuario atualizado com sucesso!"]);
}
function deleteUsuario($con, $id){
    $stmt = $con->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->execute([$id]);

    echo json_encode(["mensagem" => "Usuario removido com sucesso!"]);
}
function createPublicacoes($con, $dados){
    $stmt = $con->prepare(
        "INSERT INTO publicacoes (id_usuario, legenda, imagem_url) VALUES (?,?,?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["legenda"] ?? null,
        $dados["imagem_url"] ?? null
    ]);

    http_response_code(201);
    echo json_encode(["mensagem" => "Publicacao criada com sucesso", "id" => $con->lastInsertId()]);
}
function readPublicacoes($con){
    $stmt = $con->query("SELECT * FROM publicacoes");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function updatePublicacoes($con, $id, $dados){
    $stmt = $con->prepare(
        "UPDATE publicacoes SET id_usuario = ?, legenda = ?, imagem_url = ? WHERE id_publicacao = ?"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["legenda"] ?? null,
        $dados["imagem_url"] ?? null,
        $id
    ]);

    echo json_encode(["mensagem" => "Publicacao atualizada com sucesso!"]);
}
function deletePublicacoes($con, $id){
    $stmt = $con->prepare("DELETE FROM publicacoes WHERE id_publicacao = ?");
    $stmt->execute([$id]);

    echo json_encode(["mensagem" => "Publicacao removida com sucesso!"]);
}
function createCurtidas($con, $dados){
    $stmt = $con->prepare(
        "INSERT INTO curtidas (id_usuario, id_publicacao) VALUES (?,?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);

    http_response_code(201);
    echo json_encode(["mensagem" => "Curtida enviada", "id" => $con->lastInsertId()]);
}
function readCurtidas($con){
    $stmt = $con->query("SELECT * FROM curtidas");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
function updateCurtidas($con, $id, $dados){
    $stmt = $con->prepare(
        "UPDATE curtidas SET id_usuario = ?, id_publicacao = ? WHERE id_curtida = ?"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"],
        $id
    ]);

    echo json_encode(["mensagem" => "Curtida atualizada com sucesso!"]);
}
function deleteCurtidas($con, $id){
    $stmt = $con->prepare("DELETE FROM curtidas WHERE id_curtida = ?");
    $stmt->execute([$id]);

    echo json_encode(["mensagem" => "Curtida removida com sucesso!"]);
}
if ($rota == "usuarios"){
    switch($metodo){
        case "GET":
            readUsuario($con);
            break;
        case "POST":
            createUsuario($con, $corpo);
            break;
        case "PUT":
            updateUsuario($con, $_GET["id"], $corpo);
            break;
        case "DELETE":
            deleteUsuario($con, $_GET["id"]);
            break;
        default:
            teste();
    }
}
else if ($rota == "publicacoes"){
    switch($metodo){
        case "GET":
            readPublicacoes($con);
            break;
        case "POST":
            createPublicacoes($con, $corpo);
            break;
        case "PUT":
            updatePublicacoes($con, $_GET["id"], $corpo);
            break;
        case "DELETE":
            deletePublicacoes($con, $_GET["id"]);
            break;
        default:
            teste();
    }
}
else if ($rota == "curtidas"){
    switch($metodo){
        case "GET":
            readCurtidas($con);
            break;
        case "POST":
            createCurtidas($con, $corpo);
            break;
        case "PUT":
            updateCurtidas($con, $_GET["id"], $corpo);
            break;
        case "DELETE":
            deleteCurtidas($con, $_GET["id"]);
            break;
        default:
            teste();
    }
}
else {
    http_response_code(404);
    echo json_encode(["erro" => "Rota nao encontrada"]);
}
?>