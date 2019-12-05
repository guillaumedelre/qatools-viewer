<?php

namespace App\Model;

class Report
{
    private $view = '';
    private $components = [];

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $view
     *
     * @return Report
     */
    public function setView(string $view): Report
    {
        $this->view = $view;
        return $this;
    }

    /**
     * @return Component[]
     */
    public function getComponents(): array
    {
        return $this->components;
    }

    /**
     * @param Component $component
     *
     * @return Report
     */
    public function addComponent(Component $component): Report
    {
        $this->components[] = $component;
        return $this;
    }

    /**
     * @param Component $component
     *
     * @return Report
     */
    public function removeComponent(Component $component): Report
    {
        if (false !== $key = array_search($component, $this->components, true)) {
            array_splice($this->components, $key, 1);
        }
        return $this;
    }
}
