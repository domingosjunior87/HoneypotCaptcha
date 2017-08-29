<?php

class Honeypot
{

    private $sessionKey = 'Honeypot.validate';

    /**
     * Honeypot constructor.
     */
    public function __construct()
    {
        // Initialize session or attach to existing
        if (session_id() == '') {
            // no session has been started yet, which is needed for validation
            session_start();
        }
    }

    public function sessionKey($sessionKey = null)
    {
        if (!is_null($sessionKey) && !empty($sessionKey)) {
            $this->sessionKey = $sessionKey;
        }

        return $this->sessionKey;
    }

    public function sessionKeyText()
    {
        return $this->sessionKey . '.text';
    }

    public function sessionKeyCheckbox()
    {
        return $this->sessionKey . '.checkbox';
    }

    public function sessionKeyHidden()
    {
        return $this->sessionKey . '.hidden';
    }

    /**
     * Gerar um novo campo text e retorna o HTML
     *
     * @param string $inputName Nome do campo
     * @param bool $useDiv Se é para usar uma tag DIV
     * @param string $value Valor do campo
     * @return string Código HTML do input text
     */
    public function generateText($inputName, $useDiv = true, $value = '')
    {
        $html = "";

        if ($useDiv) {
            $html .= '<div id="' . $inputName . '_wrap" style="display:none;">' . "\r\n";
        }

        $html .= '<input ';
        $html .= 'name="' . $inputName . '" ';
        $html .= 'type="text" ';
        $html .= 'value="' . $value . '" ';
        $html .= 'id="' . $inputName . '" ';
        $html .= 'style="display:none !important" ';
        $html .= 'tabindex="-1" ';
        $html .= 'autocomplete="off" ';
        $html .= '/>';
        $html .= "\r\n";

        if ($useDiv) {
            $html .= '</div>';
        }

//        CakeSession::write($this->sessionKey . '.text.' . $inputName, $value);

        return $html;
    }

    /**
     * Gerar um novo campo checkbox e retorna o HTML
     *
     * @param string $inputName Nome do campo
     * @param bool $useDiv Se é para usar uma tag DIV
     * @param string $value Valor do campo
     * @param bool $checked Se é para o campo está checado
     * @return string Código HTML do input checkbox
     */
    public function generateCheckbox($inputName, $useDiv = true, $value = '', $checked = true)
    {
        $html = "";

        if ($useDiv) {
            $html .= '<div id="' . $inputName . '_wrap" style="display:none;">' . "\r\n";
        }

        $html .= '<input ';
        $html .= 'name="' . $inputName . '" ';
        $html .= 'type="checkbox" ';
        $html .= 'value="' . $value . '" ';
        $html .= 'id="' . $inputName . '" ';

        if ($checked) {
            $html .= 'checked="checked" ';
        }

        $html .= 'style="display:none !important" ';
        $html .= 'tabindex="-1" ';
        $html .= 'autocomplete="off" ';
        $html .= '/>';
        $html .= "\r\n";

        if ($useDiv) {
            $html .= '</div>';
        }

//        CakeSession::write($this->sessionKey . '.checkbox.' . $inputName, $value);

        return $html;
    }

    /**
     * Gerar um novo campo hidden e retorna o HTML
     *
     * @param string $inputName Nome do campo
     * @param string $value Valor do campo
     * @return string Código HTML do input hidden
     */
    public function generateHidden($inputName, $value = '')
    {
        $html = '<input ';
        $html .= 'name="' . $inputName . '" ';
        $html .= 'type="hidden" ';
        $html .= 'value="' . $value . '" ';
        $html .= 'id="' . $inputName . '" ';
        $html .= 'style="display:none !important" ';
        $html .= 'tabindex="-1" ';
        $html .= 'autocomplete="off" ';
        $html .= '/>';
        $html .= "\r\n";

//        CakeSession::write($this->sessionKey . '.hidden.' . $inputName, $value);

        return $html;
    }

    /**
     * Validate honeypot is empty
     *
     * @param string $inputName Nome do campo a ser validado
     * @param mixed $value Valor a ser validado
     * @return bool True on success
     */
    public function validateText($inputName, $value)
    {
        return CakeSession::read($this->sessionKeyText() . '.' . $inputName) == $value;
    }

    public function validateCheckbox($inputName, $value)
    {
        return CakeSession::read($this->sessionKeyCheckbox() . '.' . $inputName) == $value;
    }

    public function validateHidden($inputName, $value)
    {
        return CakeSession::read($this->sessionKeyHidden() . '.' . $inputName) == $value;
    }

    public function check($data = array())
    {
        $success = 0;

        if (!empty($data)) {
            $texts = CakeSession::read($this->sessionKeyText());
            if (!is_null($texts)) {
                $texts = array_keys($texts);
                foreach ($texts as $textInput) {
                    if (!$this->validateText($textInput, $data[$textInput])) {
                        return false;
                    }

                    $success++;
                }
            }

            $checkboxes = CakeSession::read($this->sessionKeyCheckbox());
            if (!is_null($checkboxes)) {
                $checkboxes = array_keys($checkboxes);
                foreach ($checkboxes as $checkboxInput) {
                    if (empty(CakeSession::read($this->sessionKeyCheckbox() . '.' . $checkboxInput))) {
                        if (isset($data[$checkboxInput])) {
                            return false;
                        }

                        $success++;
                        continue;
                    }

                    if (!$this->validateCheckbox($checkboxInput, $data[$checkboxInput])) {
                        return false;
                    }

                    $success++;
                }
            }

            // TODO: Validate hidden inputs
        }

        return ($success > 0);
    }
}
