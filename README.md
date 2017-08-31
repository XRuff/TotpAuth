TotpAuth
======

Nette extension for Time-Based One-Time Password Algorithm


Requirements
------------

Package requires PHP 7.0 or higher

- [tracy/tracy](https://github.com/tracy/tracy)
- [xruff/basedbmodel](https://github.com/xruff/basedbmodel)
- [oops/totp-authenticator](https://github.com/oops/totp-authenticator)
- [guzzlehttp/guzzle](https://github.com/oops/totp-authenticator)

Installation
------------

The best way to install XRuff/TotpAuth is using  [Composer](http://getcomposer.org/):

```sh
$ composer require xruff/totpAuth
```

Scenario
------------


* logged user activate 2FA in account settings:
  * see QR core
  * scan it with [mobile application](https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=cs)
  * and click "Confirm Code" button
* next login to your application:
  * user log in standard way (login + password...) and see second login page with form with one field
  * provide code from Authenticator mobile aplication
  * pass through if provided code is right


Documentation
------------

Assumptions:

* create table `rq` in database, use schema from file `sql/qr.sql`
* `$user->indentity` have to contain properties `id` and `username`

Configuration in config.neon.


```yml
extensions:
    socialTags: XRuff\TotpAuth\DI\TotpAuthExtension

totpAuth:
    issuer: NameOfMyApp  # mandatory
    timeWindow: 1        # optional - time tolerance
    codeSize: '300x300'  # optional - size ofgenerated QR code
```

Presenter:

```php

use XRuff\TotpAuth\Auth;
use Nette\Application\UI;

class HomepagePresenter extends Nette\Application\UI\Presenter
{
    /** @var Auth $auth @inject */
    public $auth;

    public function renderDefault() {
        $this->template->qrCode = $this->auth->getQrBase64();
    }

    public function handleSaveUrl()
    {
        $this->auth->saveSecret();
        $this->redirect('this');
    }

    public function handleResetUrl()
    {
        $this->auth->resetSecret();
        $this->redirect('this');
    }

    protected function createComponentCodeForm()
    {
        $form = new UI\Form;
        $form->addText('code', 'Code');
        $form->addSubmit('submit', 'Auth me');
        $form->onSuccess[] = [$this, 'codeFormSucceeded'];
        return $form;
    }

    public function codeFormSucceeded(UI\Form $form, $values)
    {
        if ($this->auth->verify($values->code)) {
            $this->flashMessage('Success!');
        } else {
            $this->flashMessage('Wrong code.');
        }
        $this->redirect('this');
    }
}
```

default.latte:

```smarty
    ...
    {if $qrCode}
        <img src="{$qrCode|nocheck}" alt="">
        <br>
        <a n:href="saveUrl!" class="btn btn-success">Confirm Code (have been added to Mobile Authenticator App)</a>
    {else}
        {control codeForm}
        <a n:href="resetUrl!" class="btn btn-success">Reset auth code</a>
    {/if}
    ...
```

-----

Repository [https://github.com/XRuff/TotpAuth](https://github.com/XRuff/TotpAuth).
