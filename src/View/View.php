<?php

namespace Crudch\View;

/**
 * Class View
 *
 * @package Crudch
 */
class View
{
    private $extends;

    /**
     * @var array
     */
    private $blocks = [];

    /**
     * @var \SplStack
     */
    private $blockNames;

    /**
     * @var string
     */
    private $path;

    /**
     * View constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->blockNames = new \SplStack();
    }

    /**
     * @param string $name
     * @param array  $params
     *
     * @return string
     * @throws \Throwable
     */
    public function render(string $name, array $params = []): string
    {
        $level = ob_get_level();
        $this->extends = null;
        try {
            ob_start();
            extract($params, EXTR_OVERWRITE);
            /** @noinspection PhpIncludeInspection */
            require "{$this->path}/{$name}.php";
            $content = ob_get_clean();
        } catch (\Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }
        if (null === $this->extends) {
            return $content;
        }

        return $this->render($this->extends);
    }

    /**
     * @param string $view
     */
    public function extend(string $view): void
    {
        $this->extends = $view;
    }

    /**
     * @param string $name
     * @param mixed  $content
     */
    public function block(string $name, $content): void
    {
        if ($this->hasBlock($name)) {
            return;
        }

        $this->blocks[$name] = $content;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function ensure(string $name): bool
    {
        if ($this->hasBlock($name)) {
            return false;
        }
        $this->start($name);

        return true;
    }

    /**
     * @param string $name
     */
    public function start(string $name): void
    {
        $this->blockNames->push($name);
        ob_start();
    }

    public function stop(): void
    {
        $content = ob_get_clean();
        $name = $this->blockNames->pop();
        if ($this->hasBlock($name)) {
            return;
        }

        $this->blocks[$name] = $content;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function renderBlock(string $name): string
    {
        return $this->blocks[$name] ?? '';
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function hasBlock(string $name): bool
    {
        return array_key_exists($name, $this->blocks);
    }
}
