<?php
// ==========================================
// CONFIGURACIÓN - ESTUDIO ARIEL CASABENE
// ==========================================

// 1. TOKEN DE VERIFICACIÓN (El que pones en el panel de Meta para el Webhook)
$verify_token = 'estudio_casabene_2026'; 

// 2. TOKEN DE ACCESO (Copia el temporal o el permanente de Meta)
$access_token = 'EAAb2mASZCnz4BRCG25tFDsU9M3gj7HNm42WFJUDDvHTiFfRAv8jNrUb6QsJMZBfv8qYvpZAwukm0Mv5ZCHdT76nbgzleGFJ0EMq8kf2fUZBi64HPUTsc9ZALY7wjyggO3xaCMKTkFN1EUyVUPfgSQXRhhvv9sNf53FNB7AG9v47gw66IIQHh5ZBRz6pyZBL6zAZDZD';

// 3. ID DEL NÚMERO DE TELÉFONO (El nuevo Phone Number ID de esta App)
$phone_id = '1093325610527041';

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
// DB CONNECTION (usa mismas env vars que la app)
// ==========================================
function get_db(): ?PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    try {
        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $name = getenv('DB_DATABASE') ?: 'estudiocontable';
        $user = getenv('DB_USERNAME') ?: 'root';
        $pass = getenv('DB_PASSWORD') ?: '';
        $pdo = new PDO("mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4", $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Exception $e) {
        wlog('DB error', $e->getMessage());
    }
    return $pdo;
}

function guardar_mensaje(string $from, string $tipo, ?string $body, ?string $opcionId, ?string $contactName, array $payload): void {
    $pdo = get_db();
    if (!$pdo) return;
    try {
        $stmt = $pdo->prepare("INSERT INTO whatsapp_mensajes (from_number, contact_name, tipo, body, opcion_id, payload) VALUES (:from, :name, :tipo, :body, :opcion, :payload)");
        $stmt->execute([
            'from'    => $from,
            'name'    => $contactName,
            'tipo'    => $tipo,
            'body'    => $body,
            'opcion'  => $opcionId,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
        ]);
    } catch (Exception $e) {
        wlog('DB insert error', $e->getMessage());
    }
}

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
        $contactName = $data['entry'][0]['changes'][0]['value']['contacts'][0]['profile']['name'] ?? null;

        wlog('Mensaje recibido', ['from' => $from, 'type' => $type, 'msg' => $message]);

        // Si recibimos un texto (ej: "Hola")
        if ($type === 'text') {
            $body = $message['text']['body'] ?? null;
            guardar_mensaje($from, 'text', $body, null, $contactName, $message);
            wlog('Acción', 'enviando menu a ' . $from);
            enviar_menu_contable($from, $access_token, $phone_id);
        }
        
        // Si el usuario elige una opción del menú
        if ($type === 'interactive') {
            $option_id  = $message['interactive']['list_reply']['id'] ?? null;
            $option_title = $message['interactive']['list_reply']['title'] ?? $option_id;
            guardar_mensaje($from, 'interactive', $option_title, $option_id, $contactName, $message);
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
                "sections" => [
                    [
                        "title" => "Consultas Frecuentes",
                        "rows" => [
                            ["id" => "btn_vencimientos", "title" => "Ver Vencimientos", "description" => "Próximas fechas AFIP/Rentas"],
                            ["id" => "btn_factura", "title" => "Solicitar Factura", "description" => "Pedido de honorarios o servicios"],
                            ["id" => "btn_doc", "title" => "Enviar Documentos", "description" => "Subir fotos de facturas o tickets"],
                            ["id" => "btn_humano", "title" => "Hablar con Ariel", "description" => "Atención personalizada"]
                        ]
                    ],
                    [
                        "title" => "Trámites Online",
                        "rows" => [
                            ["id" => "btn_constancia_arca", "title" => "Constancia ARCA", "description" => "Consultar constancia en AFIP/ARCA"],
                            ["id" => "btn_constancia_rentas", "title" => "Inscripción Rentas", "description" => "Consultar inscripción en Rentas Nqn"]
                        ]
                    ]
                ]
            ]
        ]
    ];
    enviar_peticion($url, $token, $json);
}

function procesar_seleccion($to, $id, $token, $pid) {
    $respuestas = [
        "btn_vencimientos"      => "Próximamente te enviaremos el calendario de vencimientos actualizado.",
        "btn_factura"           => "Perfecto. Por favor, indícanos el concepto y te enviaremos la factura a la brevedad.",
        "btn_doc"               => "Puedes enviar las fotos o PDFs directamente por aquí. Los procesaremos en el día.",
        "btn_humano"            => "Ariel ha sido notificado. Se pondrá en contacto contigo pronto.",
        "btn_constancia_arca"   => "Podés consultar y descargar tu Constancia de Inscripción en ARCA/AFIP desde el siguiente link:\n\nhttps://seti.afip.gob.ar/padron-puc-constancia-internet/ConsultaConstanciaAction.do",
        "btn_constancia_rentas" => "Podés consultar tu Constancia de Inscripción en Rentas de Neuquén desde el siguiente link:\n\nhttps://rentasneuquenweb.gob.ar/nqn/SCF/cons_inscripcion.php"
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