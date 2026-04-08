<?php
// ==========================================
// CONFIGURACIÓN - ESTUDIO ARIEL CASABENE
// ==========================================

// 1. TOKEN DE VERIFICACIÓN (El que pones en el panel de Meta para el Webhook)
$verify_token = 'estudio_casabene_2026'; 

// 2. TOKEN DE ACCESO (Copia el temporal o el permanente de Meta)
$access_token = 'EAAb2mASZCnz4BRAQDCN3GmBe0ED388mtcZAa8AzSQZCqjqx0WgkXPSSnaYPy9nfo97x0XgZBo0YZCEA90ADwLVhkBTOHl59mFZC6yFCVUZB4ZBtgmdIYf36CxaWOPPrcdsFMZB8uR1MF8NjelRrD0XnzZCp0ZAaFimu6QJ0XhGnFf0iNq5k5SN0X0fRNJv3ZAbEJKQ5h9UwunBTMZBmGoFEZB0mkrZCJc5TfuqdhsA1tzAWf5yxxY3haZAsGCtEayP00pJ9z3OZAutSSegb9nttBaeduHFso5';

// 3. ID DEL NÚMERO DE TELÉFONO (El nuevo Phone Number ID de esta App)
$phone_id = '1019236997942935';

// ==========================================
// LOG LOCAL
// ==========================================
function wlog($label, $data = null) {
    $line = '[' . date('Y-m-d H:i:s') . '] ' . $label;
    if ($data !== null) {
        $line .= ' | ' . (is_string($data) ? $data : json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    file_put_contents(__DIR__ . '/webhook.log', $line . PHP_EOL, FILE_APPEND);
}

wlog($_SERVER['REQUEST_METHOD'] . ' request', $_SERVER['REQUEST_URI'] ?? '');

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

    wlog('POST payload', $input);

    // Respondemos 200 OK rápido a Meta
    http_response_code(200);

    if (isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
        $message = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $from = normalizar_numero($message['from']);
        wlog('Número normalizado', $from);

        $type = $message['type'];

        wlog('Mensaje recibido', ['from' => $from, 'type' => $type, 'msg' => $message]);

        // Si recibimos un texto (ej: "Hola")
        if ($type === 'text') {
            wlog('Acción', 'enviando menu a ' . $from);
            enviar_menu_contable($from, $access_token, $phone_id);
        }
        
        // Si el usuario elige una opción del menú
        if ($type === 'interactive') {
            $option_id = $message['interactive']['list_reply']['id'];
            wlog('Acción', 'opcion seleccionada: ' . $option_id . ' por ' . $from);
            procesar_seleccion($from, $option_id, $access_token, $phone_id);
        }
    } else {
        wlog('Sin mensaje en payload', $data);
    }
}

// =====================================================
// FUNCIONES
// =====================================================

function normalizar_numero(string $numero): string
{
    // Limpiar todo lo que no sea dígito
    $numero = preg_replace('/\D+/', '', $numero);

    // Argentina: Meta devuelve 549XXXXXXXXX (13 dígitos)
    // Necesita enviarse como 54299XXXXXXXX (14 dígitos)
    // Ejemplo: 5492995743759 → 54299155743759
    if (strlen($numero) === 13 && str_starts_with($numero, '549')) {
        $area   = substr($numero, 3, 3); // ej: 299
        $resto  = substr($numero, 6);    // ej: 5743759
        $numero = '54' . $area . '15' . $resto;
    }

    return $numero;
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
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    wlog('API response HTTP ' . $httpCode, $response);
}