<?php
/* Auction actions (cancel, sell, bid) of hero_auction.php, shared by the classic and the Legends look. */
if ($_GET['action'] == 'sell' && $_GET['abort']) {
    // only the seller may cancel, and only while nobody has bid
    $abortData = $database->getBidData((int)$_GET['abort']);
    if ($abortData && (int)$abortData['owner'] === (int)$session->uid && (int)$abortData['finish'] === 0 && (int)$abortData['bids'] === 0) {
        $database->delAuction((int)$_GET['abort']);
    }
}

if ($_GET['action'] == 'sell' && $_POST['a'] == 'e45') {
    $sql = $database->query("SELECT id FROM auction WHERE `finish` = '0' and owner = '" . sql_esc($session->uid) . "'");
    $query = safe_count($sql);
    if ($query < 5) {
        $data = $database->getItemData($_POST['id']);
        if ($session->uid != $data['uid']) {
            echo "пахнет чем-то хакерским";
            exit;
        }
        if ($data['num'] < $_POST['amount'] && $_POST['amount'] <= 0) {
            exit;
        }
        if (php7_is_zero($data['proc'])) {
            $itemid = $_POST['id'];
            if ($data['num'] == 1 || ($data['num'] - $data['type']) == $_POST['amount']) {
                $database->query("DELETE FROM heroitems WHERE `id`='" . sql_esc($data['id']) . "'");

            }
            if ($data['num'] == 1 || ($data['num'] - $data['type']) >= $_POST['amount']) {
                // quest
                if ($session->qArrayB1[0] == 9 && php7_is_zero($session->qArrayB1[1])) {
                    $database->query("UPDATE quests SET battle1='9,1' WHERE userid = " . sql_num($session->uid) . "");
                }
                $database->addAuction($session->uid, $itemid, $data['btype'], $data['type'], $_POST['amount']);
            }
        }
    }
}

if (isset($_POST['a']) && $_POST['action'] == 'buy' || isset($_POST['a']) && $_POST['action'] == 'bids') {
    $bidError = '';
    $getBidData = $database->getBidData($_POST['a']);
    if (php7_is_zero($getBidData['newsilver'])) {
        $getBidData['newsilver'] = $getBidData['silver'];
    }
    $total_silver = $getBidData['newsilver'] + $session->silver;
    if ($_POST['maxBid'] <= $getBidData['newsilver']) {
        $bidError .= "Too low bid. You need to offer at least " . ($getBidData['newsilver'] + 1) . " silver.";
    } elseif ($_POST['maxBid'] > $session->silver || ($_POST['uid'] == $session->uid && $_POST['maxBid'] > $total_silver)) {
        $bidError .= "You haven't enough silver for this bid.";
    } else {
        if ($database->checkBid($_POST['a'], $_POST['maxBid'])) { //точно хinатит ли серы на покупку
            if (!php7_is_zero($getBidData['uid'])) {
                $database->setSilver($getBidData['uid'], $getBidData['newsilver'], 1);
            }
            $database->setSilver($session->uid, $_POST['maxBid'], 0);
            $database->addBid($_POST['a'], $session->uid, $_POST['maxBid']);
            $database->setNewSilver($_POST['a'], $_POST['maxBid']);

            if (isset($_POST['page'])) {
                $page = "&page=" . $_POST['page'];
            } else {
                $page = "";
            }
            if ($_POST['action'] == 'bids') {
                $ssss = 'bids';
            } elseif ($_POST['action'] == 'buy') {
                $ssss = 'buy';
            }

            // quest
            if ($session->qArrayB1[0] == 9 && php7_is_zero($session->qArrayB1[1])) {
                $database->query("UPDATE quests SET battle1='9,1' WHERE userid = " . sql_num($session->uid) . "");
            }

            header("Location: ?action=" . $ssss . "" . $page . "&a=" . $_POST['a']);
        } else {
            $bidError .= "Your bid is lower than the other player's one.";

            if (isset($_POST['page'])) {
                $page = "&page=" . $_POST['page'];
            } else {
                $page = "";
            }
            if ($_POST['action'] == 'bids') {
                $ssss = 'bids';
            } elseif ($_POST['action'] == 'buy') {
                $ssss = 'buy';
            }
            header("Location: ?action=" . $ssss . "" . $page . "&a=" . $_POST['a']);
        }
    }

}

