<?php
/*
 * Isakram · Fødevarecentralen · billedgenkendelse
 *
 * Modtager et billede af en vare fra appen, spørger Claude, hvad det er,
 * og sender navn, enhed, hylde og beskrivelse tilbage som JSON.
 * API-nøglen ligger i genkend-config.php og når aldrig browseren.
 *
 * Læg filen i samme mappe som fodevarecentralen.html.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function svar($status, $data) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/* ---------- indstillinger ---------- */
$cfgFiler = [dirname(__DIR__) . '/genkend-config.php', __DIR__ . '/genkend-config.php'];
foreach ($cfgFiler as $f) { if (is_file($f)) { require $f; break; } }

$apiKey     = defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : '';
$model      = defined('GENKEND_MODEL') ? GENKEND_MODEL : 'claude-haiku-4-5-20251001';
$prTime     = defined('GENKEND_PR_TIME') ? GENKEND_PR_TIME : 20;     // billeder pr. IP-adresse pr. time
$prDoegn    = defined('GENKEND_PR_DOEGN') ? GENKEND_PR_DOEGN : 300;  // billeder i alt pr. døgn
$domaener   = defined('GENKEND_DOMAENER') ? GENKEND_DOMAENER : [];    // fx ['isakram.dk','www.isakram.dk']
$workspace  = defined('ANTHROPIC_WORKSPACE_ID') ? ANTHROPIC_WORKSPACE_ID : '';  // kun nødvendig for nøgler til flere workspaces
$apiUrl     = defined('GENKEND_API_URL') ? GENKEND_API_URL : 'https://api.anthropic.com/v1/messages';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') svar(405, ['error' => 'method']);
if ($apiKey === '' || strpos($apiKey, 'INDSÆT') !== false) svar(503, ['error' => 'not_configured']);

/* Kun kald fra jeres egen hjemmeside */
if ($domaener) {
    $kilde = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
    $vaert = strtolower((string) parse_url($kilde, PHP_URL_HOST));
    if (!in_array($vaert, array_map('strtolower', $domaener), true)) svar(403, ['error' => 'forbidden']);
}

/* ---------- loft over forbrug ---------- */
$mappe = rtrim(sys_get_temp_dir(), '/') . '/isakram-genkend';
if (!is_dir($mappe)) @mkdir($mappe, 0700, true);
function taeller($fil, $vindue, $max) {
    $fh = @fopen($fil, 'c+');
    if (!$fh) return true;                       // kan vi ikke tælle, lader vi kaldet gå igennem
    flock($fh, LOCK_EX);
    $nu = time();
    $liste = json_decode(stream_get_contents($fh) ?: '[]', true) ?: [];
    $liste = array_values(array_filter($liste, function ($t) use ($nu, $vindue) { return $t > $nu - $vindue; }));
    $ok = count($liste) < $max;
    if ($ok) $liste[] = $nu;
    ftruncate($fh, 0); rewind($fh); fwrite($fh, json_encode($liste));
    flock($fh, LOCK_UN); fclose($fh);
    return $ok;
}
$ip = $_SERVER['REMOTE_ADDR'] ?? 'ukendt';
if (!taeller($mappe . '/ip-' . hash('sha256', $ip) . '.json', 3600, $prTime)) svar(429, ['error' => 'rate_limited']);
if (!taeller($mappe . '/alle.json', 86400, $prDoegn)) svar(429, ['error' => 'rate_limited']);

/* ---------- billedet ---------- */
if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) svar(400, ['error' => 'invalid_image']);
if ($_FILES['image']['size'] > 5 * 1024 * 1024) svar(413, ['error' => 'invalid_image']);
$info = @getimagesize($_FILES['image']['tmp_name']);
$typer = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
if (!$info || !in_array($info['mime'], $typer, true)) svar(400, ['error' => 'invalid_image']);
$data = base64_encode(file_get_contents($_FILES['image']['tmp_name']));

$navn = mb_substr(trim((string) ($_POST['producer'] ?? '')), 0, 80);
$kat  = mb_substr(trim((string) ($_POST['cats'] ?? '')), 0, 120);

/* ---------- spørg Claude ---------- */
$hylder = ['1 Lokal', '1 Basis', '1 Hjem', '1 Krop'];
$prompt = 'Billedet er taget af en lokal fødevareproducent'
    . ($navn !== '' ? ' ("' . $navn . '"' . ($kat !== '' ? ', kategori: ' . $kat : '') . ')' : '')
    . ", som vil lægge varen i sin webshop.\n"
    . "Identificér varen på billedet, og foreslå produktdata på dansk til shoppen.\n"
    . 'Svar KUN med JSON: {"name": kort produktnavn som kunden forstår (fx "Gule løg", "Gulerødder", "Rugbrød med solsikke", "Lynghonning"), '
    . '"unit": salgsenhed hvis den kan ses eller er oplagt (fx "1 kg", "500 g", "pr. stk.", "10 stk.") ellers "", '
    . '"shelf": én af ' . json_encode($hylder, JSON_UNESCAPED_UNICODE) . ' (1 Lokal = lokale råvarer og fødevarer; 1 Basis = kolonial; 1 Hjem = husholdning; 1 Krop = personlig pleje), '
    . '"description": én kort, saglig sætning om varen, kun ud fra det der kan ses, '
    . '"confidence": "høj", "middel" eller "lav", "note": kort bemærkning hvis noget er uklart, ellers ""}.' . "\n"
    . 'Find ikke på mærker, oprindelse, priser eller certificeringer, som ikke tydeligt står på billedet. '
    . 'Hvis billedet ikke viser en vare, så sæt "name" til "" og forklar kort i "note".';

$body = json_encode([
    'model'      => $model,
    'max_tokens' => 400,
    'messages'   => [[
        'role'    => 'user',
        'content' => [
            ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $info['mime'], 'data' => $data]],
            ['type' => 'text', 'text' => $prompt],
        ],
    ]],
], JSON_UNESCAPED_UNICODE);

$ch = curl_init($apiUrl);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 45,
    CURLOPT_HTTPHEADER     => array_merge([
        'content-type: application/json',
        'authorization: Bearer ' . $apiKey,
        'anthropic-version: 2023-06-01',
    ], $workspace !== '' ? ['anthropic-workspace-id: ' . $workspace] : []),
    CURLOPT_POSTFIELDS     => $body,
]);
$res  = curl_exec($ch);
$kode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($res === false) svar(502, ['error' => 'upstream']);
if ($kode === 429 || $kode === 529) svar(429, ['error' => 'rate_limited']);
if ($kode < 200 || $kode >= 300) { error_log('genkend.php: Claude svarede ' . $kode . ' ' . substr($res, 0, 300)); svar(502, ['error' => 'upstream']); }

$svarData = json_decode($res, true);
$tekst = '';
foreach (($svarData['content'] ?? []) as $del) { if (($del['type'] ?? '') === 'text') $tekst .= $del['text']; }
if (!preg_match('/\{.*\}/s', $tekst, $m)) svar(502, ['error' => 'invalid_json']);
$ud = json_decode($m[0], true);
if (!is_array($ud) || empty($ud['name'])) svar(422, ['error' => 'not_recognized', 'note' => mb_substr((string) ($ud['note'] ?? ''), 0, 160)]);

svar(200, [
    'name'        => mb_substr((string) $ud['name'], 0, 80),
    'unit'        => mb_substr((string) ($ud['unit'] ?? ''), 0, 20),
    'shelf'       => in_array($ud['shelf'] ?? '', $hylder, true) ? $ud['shelf'] : '1 Lokal',
    'description' => mb_substr((string) ($ud['description'] ?? ''), 0, 300),
    'confidence'  => in_array($ud['confidence'] ?? '', ['høj', 'middel', 'lav'], true) ? $ud['confidence'] : 'middel',
    'note'        => mb_substr((string) ($ud['note'] ?? ''), 0, 160),
]);
