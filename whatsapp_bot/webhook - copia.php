<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================
// CONFIGURACIÓN DESDE config.php
// ==========================================
$config       = require __DIR__ . '/config.php';
$verify_token = $config['verify_token'];
$access_token = $config['access_token'];
$phone_id     = $config['phone_id'];

// Archivos de log
$log_file   = __DIR__ . '/send_log.txt';
$debug_file = __DIR__ . '/debug_from.txt';


// =====================================================
// VERIFICACIÓN WEBHOOK (GET)
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $token     = $_GET['hub_verify_token'] ?? '';
    $challenge = $_GET['hub_challenge']    ?? '';
    $mode      = $_GET['hub_mode']         ?? '';

    if ($mode === 'subscribe' && $token === $verify_token) {
        http_response_code(200);
        echo $challenge;
        exit;
    }

    http_response_code(403);
    echo 'Error de verificación';
    exit;
}


// =====================================================
// RECEPCIÓN DE MENSAJES (POST)
// =====================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $input = file_get_contents('php://input');
    $data  = json_decode($input, true);

    // Log entrada completa
    file_put_contents(
        $debug_file,
        date('Y-m-d H:i:s') . " RAW JSON: " . $input . PHP_EOL,
        FILE_APPEND
    );

    // Responder 200 a Meta inmediatamente
    http_response_code(200);
    echo json_encode(["status" => "ok"]);

    if (!isset($data['entry'][0]['changes'][0]['value']['messages'])) {
        exit;
    }

    $messages = $data['entry'][0]['changes'][0]['value']['messages'];

    foreach ($messages as $message) {

        $from_raw = $message['from'] ?? '';
        $type     = $message['type'] ?? '';
        $from     = normalizar_numero($from_raw);

        // Log número recibido
        file_put_contents(
            $debug_file,
            date('Y-m-d H:i:s') . " FROM RAW: [$from_raw] NORMALIZADO: [$from] TIPO: [$type]" . PHP_EOL,
            FILE_APPEND
        );

        // Mensaje de texto
        if ($type === 'text') {
            $body  = $message['text']['body'] ?? '';
            $texto = trim(mb_strtolower($body, 'UTF-8'));

            file_put_contents(
                $debug_file,
                date('Y-m-d H:i:s') . " TEXTO RECIBIDO: [$texto]" . PHP_EOL,
                FILE_APPEND
            );

            procesarMensaje($texto, $from, $access_token, $phone_id, $log_file);
        }

        // Respuesta de botón interactivo
        if ($type === 'interactive') {
            $button_id = $message['interactive']['button_reply']['id'] ?? '';

            file_put_contents(
                $debug_file,
                date('Y-m-d H:i:s') . " BOTON PRESIONADO: [$button_id]" . PHP_EOL,
                FILE_APPEND
            );

            procesarMensaje($button_id, $from, $access_token, $phone_id, $log_file);
        }
    }

    exit;
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


function procesarMensaje(string $texto, string $from, string $access_token, string $phone_id, string $log_file): void
{
    $texto = trim(mb_strtolower($texto, 'UTF-8'));

    // Volver al menú
    if ($texto === 'menu' || $texto === 'menú' || $texto === 'inicio') {
        enviarMenu($from, $access_token, $phone_id, $log_file);
        return;
    }

    switch ($texto) {

        case '1':
        case 'reuniones':
            enviarTexto(
                $from,
                "📅 *Información de Reuniones*\n\n" .
                "🗓 Domingos: 10:00hs y 19:00hs\n" .
                "🗓 Miércoles: 20:00hs (reunión de oración)\n\n" .
                "📍 Dirección: Calle Ejemplo 1234, Neuquén\n\n" .
                "Escribí *menu* para volver al inicio.",
                $access_token, $phone_id, $log_file
            );
            break;

        case '2':
        case 'discipulado':
            enviarTexto(
                $from,
                "✝️ *Discipulado - Vida en Cristo*\n\n" .
                "Nuestro programa de discipulado está pensado para crecer en la fe.\n\n" .
                "📖 Grupos: Martes y Jueves 19:00hs\n" .
                "👤 Para inscribirte escribí a: info@iglesia.com\n\n" .
                "Escribí *menu* para volver al inicio.",
                $access_token, $phone_id, $log_file
            );
            break;

        case '3':
        case 'especiales':
            enviarTexto(
                $from,
                "🎉 *Reuniones Especiales*\n\n" .
                "Próximos eventos:\n" .
                "⭐ Semana Santa - 15 al 20 de Abril\n" .
                "⭐ Retiro juvenil - 10 de Mayo\n" .
                "⭐ Culto de adoración - Último domingo de cada mes\n\n" .
                "Escribí *menu* para volver al inicio.",
                $access_token, $phone_id, $log_file
            );
            break;

        default:
            // Cualquier otro mensaje → mostrar menú
            enviarMenu($from, $access_token, $phone_id, $log_file);
            break;
    }
}


function enviarMenu(string $to, string $token, string $phone_id, string $log_file): void
{
    $url = "https://graph.facebook.com/v20.0/" . $phone_id . "/messages";

    $data = array(
        "messaging_product" => "whatsapp",
        "recipient_type"    => "individual",
        "to"                => $to,
        "type"              => "interactive",
        "interactive"       => array(
            "type"   => "button",
            "header" => array(
                "type" => "text",
                "text" => "Bienvenido 👋"
            ),
            "body"   => array(
                "text" => "¿En qué te podemos ayudar hoy?\nElegí una opción:"
            ),
            "footer" => array(
                "text" => "Respondé con el número o tocá el botón"
            ),
            "action" => array(
                "buttons" => array(
                    array(
                        "type"  => "reply",
                        "reply" => array(
                            "id"    => "1",
                            "title" => "📅 Reuniones"
                        )
                    ),
                    array(
                        "type"  => "reply",
                        "reply" => array(
                            "id"    => "2",
                            "title" => "✝️ Discipulado"
                        )
                    ),
                    array(
                        "type"  => "reply",
                        "reply" => array(
                            "id"    => "3",
                            "title" => "🎉 Especiales"
                        )
                    )
                )
            )
        )
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents(
        $log_file,
        date('Y-m-d H:i:s') . " MENU ENVIADO A: [$to] HTTP: {$http_code} - {$response}" . PHP_EOL,
        FILE_APPEND
    );
}


function enviarTexto(string $to, string $texto, string $token, string $phone_id, string $log_file): void
{
    $url = "https://graph.facebook.com/v20.0/" . $phone_id . "/messages";

    $data = array(
        "messaging_product" => "whatsapp",
        "recipient_type"    => "individual",
        "to"                => $to,
        "type"              => "text",
        "text"              => array(
            "body" => $texto
        )
    );

    file_put_contents(
        $log_file,
        date('Y-m-d H:i:s') . " ENVIANDO A: [$to] MENSAJE: [$texto]" . PHP_EOL,
        FILE_APPEND
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response  = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error     = curl_error($ch);
    curl_close($ch);

    // Detectar errores de la API
    $resp_decoded = json_decode($response, true);
    if ($http_code !== 200 || isset($resp_decoded['error'])) {
        $error_msg  = $resp_decoded['error']['message'] ?? 'Error desconocido';
        $error_code = $resp_decoded['error']['code']    ?? 0;
        file_put_contents(
            $log_file,
            date('Y-m-d H:i:s') . " ERROR API: [Codigo: {$error_code}] {$error_msg}" . PHP_EOL,
            FILE_APPEND
        );
        return;
    }

    file_put_contents(
        $log_file,
        date('Y-m-d H:i:s') . " RESPUESTA OK: HTTP {$http_code} - {$response}" . PHP_EOL,
        FILE_APPEND
    );
}