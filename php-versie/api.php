<?php
// Centrale opslag voor de Reis door het Land van Thee.
// Zet hier je eigen beheer-wachtwoord in voordat je uploadt!
$ADMIN_WACHTWOORD = 'thee-practor';

define('DATA_DIR', __DIR__ . '/data');
define('DATA_FILE', DATA_DIR . '/thee-reis.json');
define('UPLOAD_DIR', __DIR__ . '/uploads');

if (!function_exists('mb_substr')) { function mb_substr($s, $a, $b = null) { return $b === null ? substr($s, $a) : substr($s, $a, $b); } }
if (!function_exists('mb_strlen')) { function mb_strlen($s) { return strlen($s); } }

function laad_data() {
    if (!file_exists(DATA_FILE)) return ['ideeen' => [], 'planning' => []];
    $d = json_decode(file_get_contents(DATA_FILE), true);
    return is_array($d) ? $d : ['ideeen' => [], 'planning' => []];
}

function bewaar_data($data) {
    if (!is_dir(DATA_DIR)) mkdir(DATA_DIR, 0775, true);
    file_put_contents(DATA_FILE, json_encode($data, JSON_UNESCAPED_UNICODE), LOCK_EX);
}

header('Content-Type: application/json; charset=utf-8');
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$action = isset($_GET['action']) ? $_GET['action'] : (isset($in['action']) ? $in['action'] : '');
$admin_ok = isset($in['wachtwoord']) && hash_equals($ADMIN_WACHTWOORD, (string)$in['wachtwoord']);

function antwoord($ok, $extra = []) {
    echo json_encode(array_merge(['ok' => $ok], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($action) {
    case 'load':
        antwoord(true, laad_data());

    case 'add':
        $tekst = trim(isset($in['tekst']) ? $in['tekst'] : '');
        if ($tekst === '' || mb_strlen($tekst) > 4000) antwoord(false);
        $d = laad_data();
        $d['ideeen'][] = [
            'id' => time() . '-' . substr(bin2hex(random_bytes(3)), 0, 6),
            'station' => (string)(isset($in['station']) ? $in['station'] : ''),
            'type' => 'tekst',
            'tekst' => $tekst,
            'naam' => mb_substr(trim(isset($in['naam']) ? $in['naam'] : ''), 0, 40),
            'tijd' => date('d M'),
            'gekozen' => false,
            'commentaar' => '',
        ];
        bewaar_data($d);
        antwoord(true, laad_data());

    case 'foto':
        $b64 = isset($in['data']) ? $in['data'] : '';
        $b64 = preg_replace('/^data:image\/[a-z+]+;base64,/', '', $b64);
        $bin = base64_decode($b64, true);
        if ($bin === false || strlen($bin) > 2 * 1024 * 1024) antwoord(false);
        $info = getimagesizefromstring($bin);
        if (!$info) antwoord(false);
        $ext = image_type_to_extension($info[2], false);
        if (!in_array($ext, ['jpeg', 'png', 'gif', 'webp'], true)) antwoord(false);
        if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0775, true);
        $naam = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        file_put_contents(UPLOAD_DIR . '/' . $naam, $bin, LOCK_EX);
        $d = laad_data();
        $d['ideeen'][] = [
            'id' => time() . '-' . substr(bin2hex(random_bytes(3)), 0, 6),
            'station' => (string)(isset($in['station']) ? $in['station'] : ''),
            'type' => 'foto',
            'url' => 'uploads/' . $naam,
            'tekst' => mb_substr(trim(isset($in['tekst']) ? $in['tekst'] : ''), 0, 300),
            'naam' => mb_substr(trim(isset($in['naam']) ? $in['naam'] : ''), 0, 40),
            'tijd' => date('d M'),
            'gekozen' => false,
            'commentaar' => '',
        ];
        bewaar_data($d);
        antwoord(true, laad_data());

    case 'star':
        $d = laad_data();
        foreach ($d['ideeen'] as &$i) {
            if ($i['id'] === $in['id']) { $i['gekozen'] = !$i['gekozen']; break; }
        }
        unset($i);
        bewaar_data($d);
        antwoord(true, laad_data());

    case 'del':
        $d = laad_data();
        $verwijderd = array_values(array_filter($d['ideeen'], function($i) use ($in) { return $i['id'] === $in['id']; }));
        foreach ($verwijderd as $v) {
            if (isset($v['url']) && strpos($v['url'], 'uploads/') === 0) {
                $f = __DIR__ . '/' . $v['url'];
            if (is_file($f)) @unlink($f);
            }
        }
        $d['ideeen'] = array_values(array_filter($d['ideeen'], function($i) use ($in) { return $i['id'] !== $in['id']; }));
        bewaar_data($d);
        antwoord(true, laad_data());

    case 'comment':
        if (!$admin_ok) antwoord(false);
        $d = laad_data();
        foreach ($d['ideeen'] as &$i) {
            if ($i['id'] === $in['id']) { $i['commentaar'] = mb_substr(trim($in['tekst']), 0, 500); break; }
        }
        unset($i);
        bewaar_data($d);
        antwoord(true, laad_data());

    case 'planning':
        $rows = isset($in['rows']) && is_array($in['rows']) ? $in['rows'] : [];
        $schoon = [];
        foreach ($rows as $r) {
            if (!is_array($r)) continue;
            $schoon[] = [mb_substr(trim($r[0] ?? ''), 0, 60), mb_substr(trim($r[1] ?? ''), 0, 200), mb_substr(trim($r[2] ?? ''), 0, 60)];
        }
        $d = laad_data();
        $d['planning'] = $schoon;
        bewaar_data($d);
        antwoord(true, laad_data());

    case 'reset':
        if (!$admin_ok) antwoord(false);
        bewaar_data(['ideeen' => [], 'planning' => []]);
        antwoord(true, laad_data());

    default:
        antwoord(false);
}
