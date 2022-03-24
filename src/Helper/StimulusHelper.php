<?php

namespace Allsoftware\SymfonyKernelTabler\Helper;


class StimulusHelper
{
    const CST_Path_ControllersFolder = __DIR__ . '/../../assets/controllers';

    public ?string $controllerPath = null;

    public function __construct(
        public string $controllerName,
        public string $controllerSubPath = "",
        public bool $isModule = false,
    ) {
        $this->controllerName = str_replace(['_controller.js', '_controller'], '', $this->controllerName);

        $path                 = self::CST_Path_ControllersFolder . DIRECTORY_SEPARATOR . $this->controllerSubPath;
        $guessedFileName      = $this->controllerName . ($this->isModule ? "" : "_controller") . ".js";
        $guessedPath          = $path . DIRECTORY_SEPARATOR . $guessedFileName;
        $this->controllerPath = $guessedPath;

        if (is_file($this->controllerPath) === false) {
            throw new \LogicException(
                "Controller Stimulus '{$this->controllerName}' doesn't exist at path {$this->controllerPath}"
            );
        }

        $this->controllerPath = realpath($this->controllerPath);
    }

    /**
     * Retourne attrs pour lier un controller stimulus
     */
    public function stimulusController(array $options = []): array
    {
        $attr = ['data-controller' => $this->_controllerKebabCase()];

        foreach ($options as $name => $value) {
            $attr["data-{$this->_controllerKebabCase()}-{$this->_strKebabCase($name)}-value"] = $value;
        }

        return $attr;
    }

    /**
     * Retourne attr pour une Target d'un controller stimulus
     */
    public function stimulusTarget(string $target, bool $detailed = false): array
    {
        if ($detailed) {
            return ['name' => "data-{$this->_controllerKebabCase()}-target", 'value' => $target];
        } else {
            return ["data-{$this->_controllerKebabCase()}-target" => $target];
        }
    }

    /**
     * Retourne attr d'une action pour un controller Stimulus
     */
    public function stimulusAction(string $actionName = null, string $eventName = null): array
    {

        $action = $this->_controllerKebabCase() . '#' . $actionName;

        if (\is_string($eventName)) {
            $action = $eventName . '->' . $action;
        }

        return ["data-action" => $action];
    }

    /**
     * Retourne attr pour un param d'un controller Stimulus
     */
    public function stimulusParam(string $paramName, string $value): array
    {
        return ["data-{$this->_controllerKebabCase()}-{$this->_strKebabCase($paramName)}-param" => $value];
    }


    private function _controllerKebabCase(): string
    {
        return $this->_strKebabCase($this->controllerName);
    }

    private function _strKebabCase(string $str): string
    {
        return GlobalHelper::camelCase_To_KebabCase($str);
    }
}
