<?php

namespace Controllers;

use Core\Database;
use Core\Session;

class WhatsappController
{
    private string $accessToken;
    private string $phoneId;

    public function __construct()
    {
        $this->accessToken = 'EAAb2mASZCnz4BRCG25tFDsU9M3gj7HNm42WFJUDDvHTiFfRAv8jNrUb6QsJMZBfv8qYvpZAwukm0Mv5ZCHdT76nbgzleGFJ0EMq8kf2fUZBi64HPUTsc9ZALY7wjyggO3xaCMKTkFN1EUyVUPfgSQXRhhvv9sNf53FNB7AG9v47gw66IIQHh5ZBRz6pyZBL6zAZDZD';
        $this->phoneId     = '1093325610527041';
    }

    public function mensajes(): void
    {
        $pdo = Database::tenant();

        // Marcar todos como leídos al abrir el panel
        $pdo->exec("UPDATE whatsapp_mensajes SET leido = 1 WHERE leido = 0");

        $stmt = $pdo->query("SELECT * FROM whatsapp_mensajes ORDER BY created_at DESC LIMIT 500");
        $mensajes = $stmt->fetchAll();

        view('whatsapp.mensajes', [
            'pageTitle' => 'WhatsApp — Mensajes',
            'mensajes'  => $mensajes,
        ]);
    }

    public function reenviarMenu(int $id): void
    {
        $pdo = Database::tenant();
        $msg = $pdo->prepare("SELECT * FROM whatsapp_mensajes WHERE id = :id");
        $msg->execute(['id' => $id]);
        $row = $msg->fetch();

        if (!$row) {
            Session::flash('error', 'Mensaje no encontrado.');
            redirect(tenant_url('whatsapp/mensajes'));
            return;
        }

        $numero = $row['from_number'];
        $this->enviarMenu($numero);

        Session::flash('success', "Menú reenviado a {$numero}.");
        redirect(tenant_url('whatsapp/mensajes'));
    }

    public function enviarTextoManual(): void
    {
        $numero  = trim($_POST['numero'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        if (!$numero || !$mensaje) {
            Session::flash('error', 'Número y mensaje son obligatorios.');
            redirect(tenant_url('whatsapp/mensajes'));
            return;
        }

        $this->enviarTexto($numero, $mensaje);
        Session::flash('success', "Mensaje enviado a {$numero}.");
        redirect(tenant_url('whatsapp/mensajes'));
    }

    private function enviarMenu(string $to): void
    {
        $url  = "https://graph.facebook.com/v20.0/{$this->phoneId}/messages";
        $json = [
            "messaging_product" => "whatsapp",
            "to"   => $to,
            "type" => "interactive",
            "interactive" => [
                "type"   => "list",
                "header" => ["type" => "text", "text" => "Estudio Ariel Casabene"],
                "body"   => ["text" => "¡Hola! Bienvenido al asistente virtual del estudio. ¿En qué podemos ayudarte?"],
                "footer" => ["text" => "Neuquén, Argentina"],
                "action" => [
                    "button"   => "Ver servicios",
                    "sections" => [
                        [
                            "title" => "Consultas Frecuentes",
                            "rows"  => [
                                ["id" => "btn_vencimientos",      "title" => "Ver Vencimientos",   "description" => "Próximas fechas AFIP/Rentas"],
                                ["id" => "btn_factura",           "title" => "Solicitar Factura",  "description" => "Pedido de honorarios o servicios"],
                                ["id" => "btn_doc",               "title" => "Enviar Documentos",  "description" => "Subir fotos de facturas o tickets"],
                                ["id" => "btn_humano",            "title" => "Hablar con Ariel",   "description" => "Atención personalizada"],
                            ]
                        ],
                        [
                            "title" => "Trámites Online",
                            "rows"  => [
                                ["id" => "btn_constancia_arca",   "title" => "Constancia ARCA",    "description" => "Consultar constancia en AFIP/ARCA"],
                                ["id" => "btn_constancia_rentas", "title" => "Inscripción Rentas", "description" => "Consultar inscripción en Rentas Nqn"],
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $this->apiCall($url, $json);
    }

    private function enviarTexto(string $to, string $text): void
    {
        $url  = "https://graph.facebook.com/v20.0/{$this->phoneId}/messages";
        $json = [
            "messaging_product" => "whatsapp",
            "to"   => $to,
            "type" => "text",
            "text" => ["body" => $text],
        ];
        $this->apiCall($url, $json);
    }

    private function apiCall(string $url, array $payload): void
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->accessToken}",
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }
}
