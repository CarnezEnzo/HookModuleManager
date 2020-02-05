<?php
/*************************************************************************************/
/*      This file is part of the Thelia package.                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*      email : dev@thelia.net                                                       */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      For the full copyright and license information, please view the LICENSE.txt  */
/*      file that was distributed with this source code.                             */
/*************************************************************************************/

namespace HookModuleManager\Controller;

use HookModuleManager\HookModuleManager;
use Symfony\Component\Intl\Locale\Locale;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Form\Exception\FormValidationException;
use Thelia\Model\Base\ModuleConfigI18nQuery;
use Thelia\Model\LangQuery;
use Thelia\Model\ModuleConfig;
use Thelia\Model\ModuleQuery;
use Thelia\Model\ModuleConfigQuery;

/**
 * Class Configuration
 * @package HookModuleManager\Controller
 * @author Enzo CARNEZ <ecarnez.sio@gmail.com>
 */
class Configuration extends BaseAdminController
{
    public function defaultAction()
    {
        /** @var array $list */
        $list = array();

        /** @var array $modules */
        $modules = ModuleQuery::create()
            ->useModuleConfigQuery()
            ->endUse()
            ->find();

        foreach ($modules as $module) {
            array_push($list, $module->getId());
        }

        $list = implode(',', $list);
        return $this->render('main', array('list' => $list));
    }

    public function getOptionsFromModuleAction()
    {
        /** @var string $code */
        $code = $this->getRequest()->query->get('code');

        /** @var array $textList */
        $textList = array();

        /** @var array $moduleConfigs */
        $moduleConfigs = ModuleConfigQuery::create()
            ->filterByModuleId($code)
            ->find();

        foreach ($moduleConfigs as $moduleConfig) {
            if (null != $moduleConfig) {

                /** @var int $codeConfig */
                $codeConfig = $moduleConfig->getId();

                /** @var ModuleConfigI18nQuery $avLangs */
                $avLanguages = LangQuery::create()
                    ->filterByActive(1)
                    ->find();

                foreach ($avLanguages as $avLanguage) {

                    /** @var string $locale */
                    $locale = $avLanguage->getLocale();

                    /** @var ModuleConfigI18nQuery $moduleText */
                    $moduleText = ModuleConfigI18nQuery::create()
                        ->filterById($codeConfig)
                        ->filterByLocale($locale)
                        ->findOne();

                    if (null != $moduleText) {

                        /** @var string $codeConfig */
                        $moduleValue = $moduleText
                            ->getValue();

                        $textList[$codeConfig . $locale] = $moduleValue;
                    }
                }
            }
        }

        return $this->render('details', array('options' => $moduleConfigs, "translations" => $textList));
    }

    public function saveAction()
    {
        $form = $this->createForm('hookmodulemanager.config.form');
        $error_message = null;

        try {
            $validateForm = $this->validateForm($form);
            $data = $validateForm->getData();
            $id = $data['id'];
            $locale = $data['locale'];
            $moduleConfig = ModuleConfigQuery::create()->findPk($id);

            if ($moduleConfig == null) {
                $moduleConfig = new ModuleConfig();
                $moduleConfig->setId($id);
            }

            $moduleConfig
                ->setLocale($locale)
                ->setValue($data['text'])
                ->save();

            return $this->generateSuccessRedirect($form);

        } catch (FormValidationException $e) {
            $error_message = $this->createStandardFormValidationErrorMessage($e);
        }

        $form->setErrorMessage($error_message);

        $this->getParserContext()
            ->addForm($form)
            ->setGeneralError($error_message)
        ;

        // Redirect to error URL if defined
        if ($form->hasErrorUrl()) {
            return $this->generateErrorRedirect($form);
        }

        return $this->getOptionsFromModuleAction();
    }

    public function getLocale() {
        $request = $this->getRequest();

        /** @var array $lang */
        $lang = $request->getSession()->getLang();

        /** @var string $locale */
        return $lang->getLocale();
    }


    public function getLanguage() {
        $request = $this->getRequest();

        /** @var array $lang */
        return $request->getSession()->getLang();

    }
}
