<?php
// ==========================================
// CONFIGURACIÓN - ESTUDIO ARIEL CASABENE
// ==========================================

// 1. TOKEN DE VERIFICACIÓN (El que pones en el panel de Meta para el Webhook)
$verify_token = 'estudio_casabene_2026'; 

// 2. TOKEN DE ACCESO (Copia el temporal o el permanente de Meta)
$access_token = 'TU_NUEVO_ACCESS_TOKEN_AQUÍ';

// 3. ID DEL NÚMERO DE TELÉFONO (El nuevo Phone Number ID de esta App)
$phone_id = 'TU_NUEVO_PHONE_NUMBER_ID_AQUÍ';

// ==========================================
// 1. VERIFICACIÓN DEL WEBHOOK (Método GET)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge'] ?? '';
    $mode = $_GET['hub_mode'] ?? '';

    if ($mode === 'subscribe' && $token === $verify_token) {
        http_response_code(200);
        echo $challenge;
        exit;
    }
}

// ==========================================
// 2. RECEPCIÓN Y RESPUESTA (Método POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    // Respondemos 200 OK rápido a Meta
    http_response_code(200);

    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $from = $message['from']; 
        $type = $message['type'];

        // Si recibimos un texto (ej: "Hola")
        if ($type === 'text') {
            enviar_menu_contable($from, $access_token, $phone_id);
        }
        
        // Si el usuario elige una opción del menú
        if ($type === 'interactive') {
            $option_id = $message['interactive']['list_reply']['id'];
            procesar_seleccion($from, $option_id, $access_token, $phone_id);
        }
    }
}

// ==========================================
// 3. FUNCIONES DE ENVÍO
// ==========================================

function enviar_menu_contable($to, $token, $pid) {
    $url = "https://graph.facebook.com/v20.0/{$pid}/messages";
    $json = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "interactive",
        "interactive" => [
            "type" => "list",
            "header" => ["type" => "text", "text" => "Estudio Ariel Casabene"],
            "body" => ["text" => "¡Hola! Bienvenido al asistente virtual del estudio. ¿En qué podemos ayudarte?"],
            "footer" => ["text" => "Neuquén, Argentina"],
            "action" => [
                "button" => "Ver servicios",
                "sections" => [[
                    "title" => "Consultas Frecuentes",
                    "rows" => [
                        ["id" => "btn_vencimientos", "title" => "Ver Vencimientos", "description" => "Próximas fechas AFIP/Rentas"],
                        ["id" => "btn_factura", "title" => "Solicitar Factura", "description" => "Pedido de honorarios o servicios"],
                        ["id" => "btn_doc", "title" => "Enviar Documentos", "description" => "Subir fotos de facturas o tickets"],
                        ["id" => "btn_humano", "title" => "Hablar con Ariel", "description" => "Atención personalizada"]
                    ]
                ]]
            ]
        ]
    ];
    enviar_peticion($url, $token, $json);
}

function procesar_seleccion($to, $id, $token, $pid) {
    $respuestas = [
        "btn_vencimientos" => "Próximamente te enviaremos el calendario de vencimientos actualizado.",
        "btn_factura" => "Perfecto. Por favor, indícanos el concepto y te enviaremos la factura a la brevedad.",
        "btn_doc" => "Puedes enviar las fotos o PDFs directamente por aquí. Los procesaremos en el día.",
        "btn_humano" => "Ariel ha sido notificado. Se pondrá en contacto contigo pronto."
    ];
    $texto = $respuestas[$id] ?? "Opción no válida.";
    enviar_texto($to, $token, $pid, $texto);
}

function enviar_texto($to, $token, $pid, $text) {
    $url = "https://graph.facebook.com/v20.0/{$pid}/messages";
    $json = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "text",
        "text" => ["body" => $text]
    ];
    enviar_peticion($url, $token, $json);
}

function enviar_peticion($url, $token, $json) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token", "Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}