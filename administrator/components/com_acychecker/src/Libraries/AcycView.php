<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2026 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php


namespace AcyChecker\Libraries;


use AcyChecker\Services\DebugService;
use AcyCheckerCmsServices\Extension;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Message;
use AcyCheckerCmsServices\Miscellaneous;
use AcyCheckerCmsServices\Router;
use AcyCheckerCmsServices\Security;
use AcyCheckerCmsServices\Url;

class AcycView extends AcycObject
{
    private $controllerName;
    private $view;
    private $data;

    public function __construct($controllerName, $view, $data)
    {
        parent::__construct();

        $this->controllerName = $controllerName;
        $this->view = $view;
        $this->data = $data;
        $this->includeStyles();
        $this->includeScripts();

        $this->wrapperStart();
        $this->includeMenu();
        $this->includeHeader();
        $this->containerStart();
        $this->includeView();
        $this->containerEnd();
        $this->menuEnd();
        $this->wrapperEnd();
    }

    private function includeStyles()
    {
        Router::addStyle(false, ACYC_CSS.'style.min.css?time='.time());

        // Add the controller style if exists
        if (file_exists(ACYC_MEDIA.'css'.DS.'controllers'.DS.strtolower($this->controllerName).'.min.css')) {
            Router::addStyle(false, ACYC_CSS.'controllers/'.strtolower($this->controllerName).'.min.css?time='.time());
        }

        // Add the layout style if exists
        if (file_exists(ACYC_MEDIA.'css'.DS.'layouts'.DS.strtolower($this->controllerName).DS.strtolower($this->view).'.min.css')) {
            Router::addStyle(false, ACYC_CSS.'layouts/'.strtolower($this->controllerName).'/'.strtolower($this->view).'.min.css?time='.time());
        }
    }

    private function includeScripts()
    {
        $this->includeLanguagesJavascript();
        $this->loadAssets();
        Router::initView();
        Router::addScript(false, ACYC_JS.'index.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/pagination.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/status.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/tooltip.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/notice.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/database.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/modal.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/form.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/fields.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/listing.min.js?time='.time());
        Router::addScript(false, ACYC_JS.'services/ajax.min.js?time='.time());
        if (Miscellaneous::isLeftMenuNecessary()) {
            Router::addScript(false, ACYC_JS.'services/cookie.min.js?time='.time());
            Router::addScript(false, ACYC_JS.'services/joomlaMenu.min.js?time='.time());
        }

        if (strtolower($this->controllerName) === 'dashboard') {
            Router::addScript(false, ACYC_JS.'libraries/apexchart.min.js');
        }

        Router::addScript(false, ACYC_JS.'libraries/select2.min.js');
        Router::addStyle(false, ACYC_CSS.'libraries/select2.min.css');

        // Add the controller script if exists
        if (file_exists(ACYC_MEDIA.'js'.DS.'controllers'.DS.strtolower($this->controllerName).'.min.js')) {
            Router::addScript(false, ACYC_JS.'controllers/'.strtolower($this->controllerName).'.min.js?time='.time());
        }

        // Add the layout script if exists
        if (file_exists(ACYC_MEDIA.'js'.DS.'layouts'.DS.strtolower($this->controllerName).DS.strtolower($this->view).'.min.js')) {
            Router::addScript(false, ACYC_JS.'layouts/'.strtolower($this->controllerName).'/'.strtolower($this->view).'.min.js?time='.time());
        }
    }

    private function includeMenu()
    {
        if (Miscellaneous::isLeftMenuNecessary()) echo Miscellaneous::getLeftMenu(strtolower($this->controllerName)).'<div id="acyc_content">';
    }

    private function menuEnd()
    {
        if (Miscellaneous::isLeftMenuNecessary()) echo '</div>';
    }

    private function includeView()
    {
        $filePath = ACYC_CORE_VIEW.$this->controllerName.DS.$this->view.'.php';
        if (!file_exists($filePath)) Router::redirect(Url::completeLink('dashboard', false, true));

        include_once $filePath;
    }

    private function wrapperStart()
    {
        echo '<div id="acyc_wrapper">';
    }

    private function wrapperEnd()
    {
        echo '</div>';
    }

    private function includeHeader()
    {
        if (!empty($this->data['header'])) echo $this->data['header'];
        Message::displayMessages();
    }

    private function containerStart()
    {
        echo '<div class="cell grid-x">';
    }

    private function containerEnd()
    {
        echo '</div>';
    }

    private function includeLanguagesJavascript()
    {
        $languages = [
            'ACYC_SAVE' => Language::translation('ACYC_SAVE'),
            'ACYC_PROCESS' => Language::translation('ACYC_PROCESS'),
            'ACYC_DISPOSABLE' => Language::translation('ACYC_DISPOSABLE'),
            'ACYC_FREE' => Language::translation('ACYC_FREE'),
            'ACYC_ACCEPT_ALL' => Language::translation('ACYC_ACCEPT_ALL'),
            'ACYC_ROLE_EMAIL' => Language::translation('ACYC_ROLE_EMAIL'),
            'ACYC_ROLE_BASED' => Language::translation('ACYC_ROLE_BASED'),
            'ACYC_FREE_DOMAIN' => Language::translation('ACYC_FREE_DOMAIN'),
            'ACYC_INVALID_SMTP' => Language::translation('ACYC_INVALID_SMTP'),
            'ACYC_DOMAIN_NOT_EXISTS' => Language::translation('ACYC_DOMAIN_NOT_EXISTS'),
            'ACYC_TOTAL' => Language::translation('ACYC_TOTAL'),
            'ACYC_ARE_YOUR_SURE_FREE_DOMAINS_CONFIRM' => Language::translation('ACYC_ARE_YOUR_SURE_FREE_DOMAINS_CONFIRM'),
            'ACYC_CONFIRM_DELETE_ALL_TESTS' => Language::translation('ACYC_CONFIRM_DELETE_ALL_TESTS'),
            'ACYC_NO_RESULTS_FOUND' => Language::translation('ACYC_NO_RESULTS_FOUND'),
            'ACYC_SELECT2_SEARCHING' => Language::translation('ACYC_SELECT2_SEARCHING'),
            'ACYC_SELECT2_LIMIT_X_ITEMS' => Language::translation('ACYC_SELECT2_LIMIT_X_ITEMS'),
            'ACYC_SELECT2_LOADING_MORE_RESULTS' => Language::translation('ACYC_SELECT2_LOADING_MORE_RESULTS'),
            'ACYC_SELECT2_ENTER_X_CHARACTERS' => Language::translation('ACYC_SELECT2_ENTER_X_CHARACTERS'),
            'ACYC_SELECT2_DELETE_X_CHARACTERS' => Language::translation('ACYC_SELECT2_DELETE_X_CHARACTERS'),
            'ACYC_SELECT2_RESULTS_NOT_LOADED' => Language::translation('ACYC_SELECT2_RESULTS_NOT_LOADED'),
            'ACYC_CONFIRM_CANCEL_TESTS' => Language::translation('ACYC_CONFIRM_CANCEL_TESTS'),
            'ACYC_BLACKLISTED' => Language::translation('ACYC_BLACKLISTED'),
            'ACYC_ARE_YOU_SURE' => Language::translation('ACYC_ARE_YOU_SURE'),
            'ACYC_UNBLOCK_USERS_CONFIRM' => Language::translation('ACYC_UNBLOCK_USERS_CONFIRM'),
            'ACYC_BLOCK_USERS_CONFIRM' => Language::translation('ACYC_BLOCK_USERS_CONFIRM'),
            'ACYC_DELETE_USERS_CONFIRM' => Language::translation('ACYC_DELETE_USERS_CONFIRM'),
            'ACYC_ARE_YOUR_SURE_DELETE_USERS_CONFIRM' => Language::translation('ACYC_ARE_YOUR_SURE_DELETE_USERS_CONFIRM'),
            'ACYC_MANUAL' => Language::translation('ACYC_MANUAL'),
            'ACYC_PLEASE_SELECT_A_CONDITION' => Language::translation('ACYC_PLEASE_SELECT_A_CONDITION'),
            'ACYC_ARE_YOU_SURE_DELETE' => Language::translation('ACYC_ARE_YOU_SURE_DELETE'),
            'ACYC_USERS_BLOCKED' => Language::translation('ACYC_USERS_BLOCKED'),
            'ACYC_USERS_DELETED' => Language::translation('ACYC_USERS_DELETED'),
            'ACYC_SELECT_FINISHED_TESTS' => Language::translation('ACYC_SELECT_FINISHED_TESTS'),
            'ACYC_NO_USER_TABLE_SELECTED' => Language::translation('ACYC_NO_USER_TABLE_SELECTED'),
            'ACYC_LAST_CONFIRMATION' => Language::translation('ACYC_LAST_CONFIRMATION'),
            'ACYC_ACTIONS_EXECUTED_X_MATCHING' => Language::translation('ACYC_ACTIONS_EXECUTED_X_MATCHING'),
            'ACYC_ACTIONS_EXECUTED_X_MATCHING_AMONG_SELECTED' => Language::translation('ACYC_ACTIONS_EXECUTED_X_MATCHING_AMONG_SELECTED'),
            'ACYC_INVALID_EMAIL_FORMAT' => Language::translation('ACYC_INVALID_EMAIL_FORMAT'),
            'ACYC_ERROR_OCCURRED' => Language::translation('ACYC_ERROR_OCCURRED'),
        ];

        $javascript = 'var ACYC_LANGUAGES = {';

        foreach ($languages as $key => $translation) {
            $javascript .= $key.': "'.Security::escape($translation).'",';
        }
        $javascript = rtrim($javascript, ',');

        $javascript .= '};';

        Router::addScript(true, $javascript);
    }

    private function loadAssets()
    {
        $javascript = '
        var ACYC_CMS = "'.ACYC_CMS.'";';

        if ('joomla' == ACYC_CMS) {
            $javascript .= '
        var ACYC_J40 = "'.ACYC_J40.'";';
        }

        Router::addScript(true, $javascript);
        Router::addScript(
            false,
            ACYC_JS.'libraries/foundation.min.js?v='.filemtime(ACYC_MEDIA.'js'.DS.'libraries'.DS.'foundation.min.js')
        );
    }
}
