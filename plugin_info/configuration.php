<?php
/* This file is part of Jeedom. */
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect('admin')) {
    include_file('desktop', '404', 'php');
    die();
}

$return = blea2mqtt::getBrokerFromJeedom();
$plugin = plugin::byId('blea2mqtt');
$update = $plugin->getUpdate();
?>
<?php include_file('desktop', 'configuration', 'css', 'blea2mqtt'); ?>

<form class="form-horizontal" id="configuration_plugin_blea2mqtt">
    <div class="blea2mqtt-config-page">
        <header class="blea2mqtt-config-hero">
            <div class="blea2mqtt-config-identity">
                <div class="blea2mqtt-config-logo"><img src="plugins/blea2mqtt/plugin_info/blea2mqtt_icon.png" alt="BLEA2MQTT"></div>
                <div>
                    <span class="blea2mqtt-config-eyebrow">{{Configuration du plugin}}</span>
                    <h2>BLEA2MQTT</h2>
                    <p>{{Configurez la publication des antennes Bluetooth vers votre broker MQTT.}}</p>
                </div>
            </div>
            <div class="blea2mqtt-config-meta">
                <span><i class="fas fa-code-branch"></i> {{Version}} <strong>v<?php echo htmlspecialchars((string) ($update ? $update->getLocalVersion() : ''), ENT_QUOTES, 'UTF-8'); ?></strong></span>
                <?php if (is_object($update)) { ?>
                    <span><i class="fas fa-stream"></i> <?php echo htmlspecialchars((string) $update->getConfiguration('version', 'stable'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><i class="fas fa-cloud-download-alt"></i> <?php echo htmlspecialchars((string) $update->getSource(), ENT_QUOTES, 'UTF-8'); ?></span>
                <?php } ?>
            </div>
            <div class="blea2mqtt-config-links">
                <a class="btn btn-sm blea2mqtt-config-link-documentation" target="_blank" rel="noopener noreferrer" href="<?php echo htmlspecialchars((string) $plugin->getDocumentation(), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-book"></i> {{Documentation}}</a>
                <a class="btn btn-sm blea2mqtt-config-link-changelog" target="_blank" rel="noopener noreferrer" href="<?php echo htmlspecialchars((string) $plugin->getChangelog(), ENT_QUOTES, 'UTF-8'); ?>"><i class="fas fa-list"></i> {{Changelog}}</a>
                <a class="btn btn-sm blea2mqtt-config-link-community" target="_blank" rel="noopener noreferrer" href="https://community.jeedom.com/tag/plugin-blea2mqtt"><i class="fas fa-comments"></i> {{Communauté}}</a>
            </div>
        </header>

        <section class="blea2mqtt-config-card">
            <div class="blea2mqtt-config-card-header">
                <i class="fas fa-broadcast-tower"></i>
                <div><h3>{{Publication MQTT}}</h3><p>{{Définissez le format utilisé pour publier les événements des antennes.}}</p></div>
            </div>
            <div class="blea2mqtt-config-card-body">
                <div class="form-group">
                    <label class="col-md-4 control-label">{{Template de publication}}
                        <sup><i class="fas fa-question-circle tooltips" title="{{Template de publication des évènements blea2mqtt}}"></i></sup>
                    </label>
                    <div class="col-md-7"><input class="configKey form-control" data-l1key="publish_template"></div>
                </div>
            </div>
        </section>

        <section class="blea2mqtt-config-card">
            <div class="blea2mqtt-config-card-header">
                <i class="fas fa-server"></i>
                <div><h3>{{Broker MQTT}}</h3><p>{{Utilisez un broker Jeedom existant ou renseignez une connexion manuelle.}}</p></div>
            </div>
            <div class="blea2mqtt-config-card-body">
                <div class="form-group">
                    <label class="col-md-4 control-label">{{Broker MQTT}}
                        <sup><i class="fas fa-question-circle tooltips" title="{{Broker MQTT sur lequel seront transmises les données de blea2mqtt}}"></i></sup>
                    </label>
                    <div class="col-md-7">
                        <select id="sel_mqttBroker" class="configKey form-control" data-l1key="broker_id">
                            <option value="">{{Aucun}}</option>
                            <?php if (is_array($return)) {
                                foreach ($return as $mqttPlugin) {
                                    $eqId = isset($mqttPlugin['eq_id']) ? $mqttPlugin['eq_id'] : '';
                                    $value = (string) $mqttPlugin['plugin'] . '::' . (string) $eqId;
                                    $label = (string) $mqttPlugin['plugin'] . ' > [' . (string) $mqttPlugin['ip'] . ':' . (string) $mqttPlugin['port'] . ']';
                                    echo '<option value="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                            } ?>
                        </select>
                    </div>
                </div>

                <div id="manualBroker">
                    <div class="form-group">
                        <label class="col-lg-4 control-label">{{Adresse du broker}}
                            <sup><i class="fas fa-question-circle tooltips" title="{{Paramètres d'accès au Broker.}}"></i></sup>
                        </label>
                        <div class="col-lg-7 input-group">
                            <span class="input-group-btn">
                                <select class="configKey form-control roundedLeft tooltips" data-l1key="mqttProto" title="{{Choisir quel protocole attend le Broker pour la communication}}.">
                                    <option>mqtt</option><option>mqtts</option><option>ws</option><option>wss</option>
                                </select>
                            </span>
                            <span class="input-group-addon">:</span>
                            <input class="configKey form-control tooltips" data-l1key="mqttAddress" placeholder="localhost" title="{{Adresse IP ou nom de domaine du Broker}}.">
                            <span class="input-group-addon">:</span>
                            <input class="configKey form-control tooltips jmqttPort roundedRight" data-l1key="mqttPort" type="number" min="1" max="65535" placeholder="port" title="{{Port réseau sur lequel écoute le Broker}}.">
                            <span class="input-group-addon jmqttWsUrl" style="display:none">/</span>
                            <input class="configKey form-control tooltips roundedRight jmqttWsUrl" data-l1key="mqttWsUrl" style="display:none" placeholder="mqtt" title="{{URL de la connexion Web Sockets du serveur distant, sans slash initial}}.">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">{{Authentification}}
                            <sup><i class="fas fa-question-circle tooltips" title="{{Nom d'utilisateur et mot de passe permettant de se connecter au Broker.}}"></i></sup>
                        </label>
                        <div class="col-lg-7 input-group">
                            <input class="configKey form-control roundedLeft" data-l1key="mqttUser" autocomplete="off" placeholder="{{Nom d'utilisateur}}">
                            <span class="input-group-addon">:</span>
                            <input class="configKey form-control roundedRight" data-l1key="mqttPass" type="password" autocomplete="off" placeholder="{{Mot de passe}}">
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</form>

<?php include_file('desktop', 'configuration', 'js', 'blea2mqtt'); ?>