<?php
$s_check1 = isset($_POST['consenso']) ? 1 : 0;
$s_check2 = isset($_POST['newsletter']) ? 1 : 0;
$s_check3 = isset($_POST['marketing']) ? 1 : 0;
$acquisition_channel = 'landing';

// Recupera i dati dal form
$name = $_POST['name'] ?? '';
$surname = $_POST['surname'] ?? '';
$email = $_POST['email'] ?? ''; // ✅ Definito correttamente
$phone = $_POST['phone'] ?? '';
$cap = $_POST['CAP'] ?? '';
$brand = $_POST['brand'] ?? '';
$modello = $_POST['modello'] ?? '';

// Carica le variabili di ambiente
loadEnv(__DIR__ . '/.env');

// Recupera le credenziali del database
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

// Connessione al database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Inserisci nella tabella centrale
$stmt = $conn->prepare("INSERT INTO cac_leads (first_name, last_name, email, phone, cap, consenso, newsletter, marketing, brand, modello, acquisition_channel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssiiisss", $name, $surname, $email, $phone, $cap, $s_check1, $s_check2, $s_check3, $brand, $modello, $acquisition_channel);
$stmt->execute();
$stmt->close();
$conn->close();

// ✅ 1. Invia evento a TikTok
$tiktok_access_token = "c91d42c0153c78a41fbdccfc36cd01802cb0f817"; // Sostituiscilo con il token generato
$tiktok_api_url = "https://business-api.tiktok.com/open_api/v1.3/event/track/";

$user_ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$event_id = uniqid(); // Genera un ID univoco per l'evento

$data = [
    "event_source" => "web",
    "event_source_id" => "CUIDI0BC77UC0U8B57J0", // Sostituiscilo con il tuo event_source_id
    "data" => [
        [
            "event" => "SubmitForm",
            "event_time" => time(),
            "user" => [
                "email" => hash('sha256', $email), // Hash dell'email per la privacy
                "ip" => $user_ip,
                "user_agent" => $user_agent ?: null // Se user agent è vuoto, passiamo null
            ]
        ]
    ]
];

// Invia la richiesta a TikTok
$ch = curl_init($tiktok_api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Access-Token: $tiktok_access_token"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

// Logga la risposta per il debug (rimuovi in produzione)
error_log("TikTok API Response: " . $response);

// ✅ 2. Reindirizza alla Thank You Page
header('Location: https://landing.carvizio.it/thankyou-ac2.html');
exit();

// Funzione per caricare il file .env
function loadEnv($path) {
    if (!file_exists($path)) {
        throw new Exception("Il file .env non esiste.");
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        putenv(sprintf('%s=%s', $name, $value));
    }
}
?>
