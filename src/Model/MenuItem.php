<?php

namespace App\Model;

class MenuItem
{
    /** @var string */
    private $label;

    /** @var string|null */
    private $icon;

    /** @var string */
    private $url;

    /** @var bool */
    private $enabled;

    /** @var bool */
    private $active;

    /** @var MenuItem[] */
    private $children = [];

    /** @var array */
    private $parameters = [];

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
     * @return MenuItem
     */
    public function setLabel(string $label): MenuItem
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIcon(): ?string
    {
        return $this->icon;
    }

    /**
     * @param string|null $icon
     *
     * @return MenuItem
     */
    public function setIcon(?string $icon): MenuItem
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     *
     * @return MenuItem
     */
    public function setUrl(string $url): MenuItem
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return MenuItem
     */
    public function setEnabled(bool $enabled): MenuItem
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return MenuItem
     */
    public function setActive(bool $active): MenuItem
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return MenuItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * @param MenuItem[] $children
     *
     * @return MenuItem
     */
    public function setChildren(array $children): MenuItem
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     *
     * @return MenuItem
     */
    public function setParameters(array $parameters): MenuItem
    {
        $this->parameters = $parameters;
        return $this;
    }
}
