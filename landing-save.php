<?php
$s_check1 = isset($_POST['consenso']) ? 1 : 0;
echo $s_check1 . "<br>";
$s_check2 = isset($_POST['newsletter']) ? 1 : 0;
echo $s_check2 . "<br>";
$s_check3 = isset($_POST['marketing']) ? 1 : 0;
echo $s_check3 . "<br>";
$acquisition_channel = 'landing';
$adminMessage = "
  <html>
    <head>
      <title>Contatto dal sito web</title>
    </head>
    <body>
      <h1>Contatto dal sito web</h1>
      <ul>
        <li>Modello: {$_POST['modello']}</li>
        <li>Nome: {$_POST['name']}</li>
        <li>Cognome: {$_POST['surname']}</li>
        <li>Numero: {$_POST['phone']}</li>
        <li>Email: {$_POST['email']}</li>
        <li>CAP: {$_POST['CAP']}</li>
		<li>Consenso: {$s_check1}</li>
		<li>Newsletter: {$s_check2}</li>
		<li>Marketing: {$s_check3}</li>
      </ul>
    </body>
  </html>
";

// Carica le variabili di ambiente
loadEnv(__DIR__ . '/.env');

// Recupera le credenziali del database dalle variabili di ambiente
$servername = getenv('DB_HOST');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = new mysqli($servername, $username, $password, $dbname);

// Controlla connessione
if ($conn->connect_error) {
	die("Connessione fallita: " . $conn->connect_error);
} else {
	echo "Connessione riuscita<br>";
}

// Inserisci nella tabella centrale
$stmt = $conn->prepare("INSERT INTO cac_leads (first_name, last_name, email, phone, cap, consenso, newsletter, marketing, brand, modello, acquisition_channel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
if ($stmt === false) {
	die("Preparazione dello statement fallita: " . $conn->error);
}
$stmt->bind_param("sssssiiisss", $_POST['name'], $_POST['surname'], $_POST['email'], $_POST['phone'], $_POST['CAP'], $s_check1, $s_check2, $s_check3, $_POST['brand'], $_POST['modello'], $acquisition_channel);
echo "Statement preparato<br>";

if ($stmt->execute()) {
    echo "Inserimento nella tabella leads riuscito<br>";
    $lead_id = $stmt->insert_id;
    echo "ID del lead inserito: $lead_id<br>";
} else {
    echo "Errore durante l'inserimento nella tabella leads: " . $stmt->error . "<br>";
}/*
if ($stmt->execute()) {
 	echo "Inserimento nella tabella leads riuscito<br>";
 	// Ottieni l'ID del lead inserito
	 $lead_id = $stmt->insert_id;
 	echo "ID del lead inserito: $lead_id<br>";
	$stmt_channel->close();
} else {
	echo "<p>Si è verificato un errore durante l'inserimento nella tabella leads. Riprova più tardi.</p>";
}
*/

$stmt->close();
// Chiusura della connessione al database
$conn->close();

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
header('Location: https://landing.carvizio.it/thankyou.html');
?>
