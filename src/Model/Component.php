<?php

namespace App\Model;

class Component
{
    protected $name = '';
    protected $label = '';
    protected $data;
    protected $options = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Component
     */
    public function setName(string $name): Component
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Component
     */
    public function setLabel(string $label): Component
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     *
     * @return Component
     */
    public function setData($data): Component
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return Component
     */
    public function setOptions(array $options): Component
    {
        $this->options = $options;
        return $this;
    }
}
