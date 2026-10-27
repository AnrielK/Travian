<?php
/**
 * Image of a Travian Legends hero (application/HeroLegends.php).
 *   hero_legends.php?uid=<player>&size=head_81|sideinfo|head_79x91|editor|body_inventory|body_profile
 */
require_once("application/Account.php");
if (($_POST['cmd'] ?? '') === 'save') {
    // Save the look from the appearance editor (own hero only, not as a sitter).
    while (ob_get_level() > 0) ob_end_clean();
    header('Content-Type: application/json');
    $config = json_decode((string)($_POST['config'] ?? ''), true);
    if (!$session->uid || !empty($session->sit) || !HeroLegends::available() || !is_array($config) || !HeroLegends::isValid($config)) {
        http_response_code(400);
        echo json_encode(['saved' => false]);
        exit;
    }
    HeroLegends::save((int)$session->uid, $config);
    echo json_encode(['saved' => true]);
    exit;
}
$uid = isset($_GET['uid']) ? (int)$_GET['uid'] : (int)$session->uid;
$size = isset($_GET['size'], HeroLegends::SIZES[$_GET['size']]) ? $_GET['size'] : 'head_81';
if ($uid <= 0 || !HeroLegends::available()) {
    http_response_code(404);
    exit;
}
$png = HeroLegends::png($uid, $size);
while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');
header('Content-Length: ' . strlen($png));
echo $png;
