<?php
/* This file is part of Jeedom. */
if (!isConnect('admin')) {
    throw new Exception(__('401 - Accès non autorisé', __FILE__));
}

function blea2mqttHealthEscape($_value)
{
    return htmlspecialchars((string) $_value, ENT_QUOTES, 'UTF-8');
}

$eqLogics = blea2mqtt::byType('blea2mqtt');
$enabledCount = 0;
$connectedCount = 0;
$libraries = [];
foreach ($eqLogics as $healthEqLogic) {
    if ($healthEqLogic->getIsEnable()) {
        $enabledCount++;
    }
    $lastReceived = (string) $healthEqLogic->getStatus('lastReceivedFrom');
    if ($lastReceived !== '' && strtotime($lastReceived) !== false && time() - strtotime($lastReceived) <= 720) {
        $connectedCount++;
    }
    $library = trim((string) $healthEqLogic->getConfiguration('library', ''));
    if ($library !== '') {
        $libraries[$library] = true;
    }
}
?>
<?php include_file('desktop', 'health', 'css', 'blea2mqtt'); ?>

<div id="blea2mqttHealth">
    <header class="blea2mqtt-health-hero">
        <span class="blea2mqtt-health-hero-icon"><i class="fas fa-heartbeat"></i></span>
        <div>
            <span class="blea2mqtt-health-eyebrow">{{Diagnostic du plugin}}</span>
            <h2>{{Santé BLEA2MQTT}}</h2>
            <p>{{État des antennes Bluetooth et de leur communication avec Jeedom.}}</p>
        </div>
    </header>

    <section class="blea2mqtt-health-summary" aria-label="{{Résumé de la santé BLEA2MQTT}}">
        <article><span class="blea2mqtt-health-summary-icon"><i class="fas fa-broadcast-tower"></i></span><div><span>{{Antennes}}</span><strong><?php echo count($eqLogics); ?></strong><small>{{configurée(s)}}</small></div></article>
        <article><span class="blea2mqtt-health-summary-icon"><i class="fas fa-power-off"></i></span><div><span>{{Actives}}</span><strong><?php echo (int) $enabledCount; ?></strong><small><?php echo max(0, count($eqLogics) - $enabledCount); ?> {{désactivée(s)}}</small></div></article>
        <article><span class="blea2mqtt-health-summary-icon"><i class="fas fa-wifi"></i></span><div><span>{{Connectées}}</span><strong><?php echo (int) $connectedCount; ?></strong><small>{{activité depuis moins de 12 min}}</small></div></article>
        <article><span class="blea2mqtt-health-summary-icon"><i class="fas fa-code"></i></span><div><span>{{Librairies}}</span><strong><?php echo count($libraries); ?></strong><small>{{variante(s) détectée(s)}}</small></div></article>
    </section>

    <section class="blea2mqtt-health-panel">
        <div class="blea2mqtt-health-panel-header"><i class="fas fa-satellite-dish"></i><h3>{{Antennes BLEA2MQTT}}</h3></div>
        <div class="table-responsive">
            <table class="table table-condensed table-striped tablesorter" id="table_healthblea2mqtt">
                <thead><tr><th>{{Appareil}}</th><th>{{Id}}</th><th>{{Système}}</th><th>{{Librairie}}</th><th>{{Connexion}}</th><th>{{Dernière communication}}</th><th>{{Date de création}}</th></tr></thead>
                <tbody>
                    <?php foreach ($eqLogics as $eqLogic) {
                        $lastReceived = (string) $eqLogic->getStatus('lastReceivedFrom');
                        $timestamp = strtotime($lastReceived);
                        $isConnected = $lastReceived !== '' && $timestamp !== false && time() - $timestamp <= 720;
                    ?>
                        <tr<?php echo $eqLogic->getIsEnable() ? '' : ' class="blea2mqtt-health-disabled"'; ?>>
                            <td><a href="<?php echo blea2mqttHealthEscape($eqLogic->getLinkToConfiguration()); ?>"><?php echo $eqLogic->getHumanName(true); ?></a></td>
                            <td><?php echo blea2mqttHealthEscape($eqLogic->getConfiguration('name')); ?></td>
                            <td><span class="label label-success"><?php echo blea2mqttHealthEscape($eqLogic->getConfiguration('system', 'N/A')); ?></span></td>
                            <td><span class="label label-info"><?php echo blea2mqttHealthEscape($eqLogic->getConfiguration('library', 'N/A')); ?></span></td>
                            <td><span class="label label-<?php echo $isConnected ? 'success' : 'danger'; ?>"><i class="fas fa-<?php echo $isConnected ? 'wifi' : 'times'; ?>"></i> <?php echo $isConnected ? '{{Connectée}}' : '{{Déconnectée}}'; ?></span><br><small><?php echo blea2mqttHealthEscape($lastReceived); ?></small></td>
                            <td><?php echo blea2mqttHealthEscape($eqLogic->getStatus('lastCommunication')); ?></td>
                            <td><?php echo blea2mqttHealthEscape($eqLogic->getConfiguration('createtime')); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (count($eqLogics) === 0) { ?><tr><td colspan="7" class="text-center text-muted">{{Aucune antenne BLEA2MQTT}}</td></tr><?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
