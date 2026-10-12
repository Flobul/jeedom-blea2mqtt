"use strict";

/* Indique les champs de configuration modifiés mais pas encore enregistrés. */
(function () {
  "use strict";

  function initUnsavedConfigurationIndicator() {
    var btnSave = document.getElementById("bt_savePluginConfig");
    if (!btnSave || document.querySelector(".modificationWithoutSave")) return;

    var configInputs = document.querySelectorAll(".configKey");
    if (!configInputs.length) return;

    var initialValues = new Map();
    var baselineReady = false;
    var message = document.createElement("i");
    message.className = "modificationWithoutSave label label-warning pull-right";
    message.textContent = "{{Modification en cours...}}";
    message.style.display = "none";
    btnSave.parentNode.insertBefore(message, btnSave.nextSibling);

    function valueOf(input) {
      if (input.type === "checkbox" || input.type === "radio") return input.checked;
      if (input.multiple) return Array.from(input.selectedOptions).map(function (option) { return option.value; }).join("\u0000");
      return input.value;
    }

    function refresh() {
      if (!baselineReady) return;
      var modified = false;
      configInputs.forEach(function (input) {
        var changed = valueOf(input) !== initialValues.get(input);
        input.classList.toggle("configKey--modified", changed);
        input.style.setProperty("background-color", changed ? "var(--al-warning-color)" : "", "important");
        input.style.setProperty("color", changed ? "var(--sc-lightTxt-color)" : "", "important");
        modified = modified || changed;
      });
      message.style.display = modified ? "inline-block" : "none";
    }

    configInputs.forEach(function (input) {
      input.addEventListener("input", refresh);
      input.addEventListener("change", refresh);
    });

    window.setTimeout(function () {
      configInputs.forEach(function (input) { initialValues.set(input, valueOf(input)); });
      baselineReady = true;
      refresh();
    }, 500);

    btnSave.addEventListener("click", function () {
      window.setTimeout(function () {
        configInputs.forEach(function (input) { initialValues.set(input, valueOf(input)); });
        refresh();
      }, 0);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initUnsavedConfigurationIndicator);
  } else {
    window.setTimeout(initUnsavedConfigurationIndicator, 0);
  }
})();

(function () {
  "use strict";

  var mqttProtocol = document.querySelector('.configKey[data-l1key="mqttProto"]');
  var mqttPort = document.querySelector('.configKey[data-l1key="mqttPort"]');
  var wsFields = document.querySelectorAll('.jmqttWsUrl');

  function updateMqttProtocol() {
    if (!mqttProtocol || !mqttPort) return;
    var isWebSocket = mqttProtocol.value === 'ws' || mqttProtocol.value === 'wss';
    mqttPort.classList.toggle('roundedRight', !isWebSocket);
    mqttPort.placeholder = { mqtt: '1883', mqtts: '8883', ws: '1884', wss: '8884' }[mqttProtocol.value] || '1883';
    wsFields.forEach(function (element) { element.style.display = isWebSocket ? '' : 'none'; });
  }

  function updateBrokerMode() {
    var broker = document.getElementById('sel_mqttBroker');
    var manualBroker = document.getElementById('manualBroker');
    if (broker && manualBroker) manualBroker.style.display = broker.value === '' ? '' : 'none';
  }

  if (mqttProtocol) mqttProtocol.addEventListener('change', updateMqttProtocol);
  var broker = document.getElementById('sel_mqttBroker');
  if (broker) broker.addEventListener('change', updateBrokerMode);
  updateMqttProtocol();
  updateBrokerMode();
})();
