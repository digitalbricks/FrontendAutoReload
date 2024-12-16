<?php namespace ProcessWire;
/**
 * Visionen & Kreationen – FrontendAutoReload
 * https://github.com/digitalbricks/FrontendAutoReload
 *
 * This module is a simple helper for automatically reloading the browser window when a file in the
 * /site/templates/ directory is changed. This is useful for development purposes.
 *
 *
 *  TO BE HONEST:
 *  The content of this file is googled together and optimized using Github Copilot.
 *  At the time of writing, I have only little idea how this works in detail.
 *  It was actually trial and error – so if you have any suggestions for improvement, please let me know.
 *
 * @See: https://www.php.net/manual/en/class.recursivefilteriterator.php
 */
class ExcludeFilter extends \RecursiveFilterIterator {
    private $excludedDirectories;
    private $watchedDir;

    public function __construct(\RecursiveIterator $iterator, string $watchedDir ,array $excludedDirectories) {
        parent::__construct($iterator);
        $this->excludedDirectories = $excludedDirectories;
        $this->watchedDir = rtrim($watchedDir,"/");
    }

    public function accept(): bool
    {
        $current = $this->current();
        if ($current->isDir()) {
            $folderRelativePath = str_replace($this->watchedDir, '', $current->getPathname());
            return !in_array($folderRelativePath, $this->excludedDirectories);
        }
        return true;
    }

    public function getChildren(): ?\RecursiveFilterIterator
    {
        return new self($this->getInnerIterator()->getChildren(), $this->watchedDir, $this->excludedDirectories);
    }
}