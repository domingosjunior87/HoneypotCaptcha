# HoneypotCaptcha
Plugin for CakePHP 2.x to use a Honeypot Captcha

## Requirements
* PHP 5.3+
* CakePHP 2.X

## Installation
In your app directory type

```
composer require domingosjunior87/HoneypotCaptcha
```

## Setup
In app/Config/bootstrap.php add:

```php
CakePlugin::load('HoneypotCaptcha');
```

## Usage
In controller, import the component and the helper, like this:

```php
public $components = array(
    'HoneypotCaptcha.Honeypot' => array(
        'text' => array('email_user', 'email_admin'),
        'checkbox' => array('validate_submit', 'validate_form'),
		'hidden' => array('phone_number'),
        'errorMessage' => "Error message"
    )
);

public $helpers = array('HoneypotCaptcha.Honeypot');
```

In view, just put this, inside a form tag:

```php
echo $this->Honeypot->render();
```

When submit the form, validate data:

```php
if (!$this->Honeypot->validate()) {
	return $this->redirect(array('action' => 'index'));
}
```