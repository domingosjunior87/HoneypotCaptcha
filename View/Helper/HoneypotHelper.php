<?php

App::uses('AppHelper', 'View/Helper');
App::uses('Honeypot', 'HoneypotCaptcha.Lib');

class HoneypotHelper extends AppHelper
{

    private $Honeypot = null;

    public function beforeRender($viewFile)
    {
        $this->Honeypot = new Honeypot();

        if (isset($this->settings['sessionKey'])) {
            $this->Honeypot->sessionKey($this->settings['sessionKey']);
        }
    }

    public function render()
    {
        $return = "";

        if (CakeSession::check($this->Honeypot->sessionKeyText())) {
            $texts = CakeSession::read($this->Honeypot->sessionKeyText());

            foreach ($texts as $campo => $valor) {
                $return .= $this->Honeypot->generateText($campo, true, $valor);
            }
        }

        if (CakeSession::check($this->Honeypot->sessionKeyCheckbox())) {
            $checkboxes = CakeSession::read($this->Honeypot->sessionKeyCheckbox());

            foreach ($checkboxes as $campo => $valor) {
                $return .= $this->Honeypot->generateCheckbox($campo, true, $valor, !(empty($valor) || is_null($valor)));
            }
        }

        if (CakeSession::check($this->Honeypot->sessionKeyHidden())) {
            $hiddens = CakeSession::read($this->Honeypot->sessionKeyHidden());

            foreach ($hiddens as $campo => $valor) {
                $return .= $this->Honeypot->generateHidden($campo, $valor);
            }
        }

        return $return;
    }
}
