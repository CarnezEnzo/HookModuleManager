<?php

namespace HookModuleManager\Form;

use HookModuleManager\HookModuleManager;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Thelia\Core\Translation\Translator;
use Thelia\Form\BaseForm;

class Configuration extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder->add(
            "text",
            TextareaType::class,
            array(
                "label" => Translator::getInstance()->trans('Text', [], HookModuleManager::DOMAIN_NAME),
                "required" => true,
            )
        );

        $this->formBuilder->add(
            "id",
            TextareaType::class,
            array(
                "label" => Translator::getInstance()->trans('ID', [], HookModuleManager::DOMAIN_NAME),
                "required" => true,
                "constraints" => [
                    new NotBlank(),
                ]
            )
        );

        $this->formBuilder->add(
            "locale",
            TextareaType::class,
            array(
                "label" => Translator::getInstance()->trans('Locale', [], HookModuleManager::DOMAIN_NAME),
                "required" => true,
                "constraints" => [
                    new NotBlank(),
                ]
            )
        );

    }

    public function getName()
    {
        return "hookmodulemanagerconfigform";
    }
}