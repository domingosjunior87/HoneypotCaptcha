<?php

App::uses('Component', 'Controller');
App::uses('Honeypot', 'HoneypotCaptcha.Lib');

class HoneypotComponent extends Component
{

    private $Honeypot = null;

    private $Controller = null;

    public $text = null;

    public $checkbox = null;

    public $hidden = null;

    public $components = array('Session', 'Flash');

    public $errorMessage = "Validation failed to the data sent. Make sure your browser is not infected.";

    private function randomValue()
    {
        // Length of the value
        $length = mt_rand(0, 100);

        $value = "";

        // Generate a value
        for ($i = 0; $i < $length; $i++) {
            do {
                $asciiValue = mt_rand(32, 126);
            } while ($asciiValue == 34); // 34 is "

            $value .= chr($asciiValue);
        }

        return $value;
    }

    /**
     * Método para inicialização do componente
     *
     * @param Controller $controller Controlador que chamou este componente
     * @return void
     */
    public function initialize(Controller $controller)
    {
        $this->Honeypot = new Honeypot();
        $this->Controller = $controller;

        if (isset($this->settings['sessionKey'])) {
            $this->sessionKey($this->settings['sessionKey']);
        }
    }

    /**
     * Is called after the controller executes the requested action’s logic, but before the controller’s renders views and layout.
     *
     * @param Controller $controller Controlador que chamou este componente
     * @return void
     */
    public function beforeRender(Controller $controller)
    {
        if ($this->Session->check($this->Honeypot->sessionKey())) {
            $this->Session->delete($this->Honeypot->sessionKey());
        }

        if (!is_null($this->text)) {
            $texts = array();

            foreach ($this->text as $key => $textInput) {
                $value = '';

                if (($key % 2) == 1) {
                    $value = $this->randomValue();
                }

                $texts[$textInput] = $value;
            }

            $this->Session->write($this->Honeypot->sessionKeyText(), $texts);
        }

        if (!is_null($this->checkbox)) {
            $checkboxes = array();

            foreach ($this->checkbox as $key => $checkboxInput) {
                $value = '';

                if (($key % 2) == 1) {
                    $value = $this->randomValue();
                }

                $checkboxes[$checkboxInput] = $value;
            }

            $this->Session->write($this->Honeypot->sessionKeyCheckbox(), $checkboxes);
        }

        if (!is_null($this->hidden)) {
            $hiddens = array();

            foreach ($this->hidden as $key => $hiddenInput) {
                $value = '';

                if (($key % 2) == 1) {
                    $value = $this->randomValue();
                }

                $hiddens[$hiddenInput] = $value;
            }

            $this->Session->write($this->Honeypot->sessionKeyHidden(), $hiddens);
        }
    }

    public function sessionKey($sessionKey = null)
    {
        return $this->Honeypot->sessionKey($sessionKey);
    }

    public function validate($showError = true)
    {
        $validated = $this->Honeypot->check($this->Controller->request->data);

        if (!$validated && $showError) {
            $this->Flash->error($this->errorMessage);
        }

        return $validated;
    }
}
