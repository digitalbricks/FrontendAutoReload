<?php namespace ProcessWire;
/**
 *                  _          _                  _ _ _                  _
 *                 | |        | |                | | (_)                | |
 *   __ _ _ __   __| |_ __ ___| |__   ___ _ __ __| | |_ _ __   __ _   __| | ___
 *  / _` | '_ \ / _` | '__/ _ \ '_ \ / _ \ '__/ _` | | | '_ \ / _` | / _` |/ _ \
 * | (_| | | | | (_| | | |  __/ | | |  __/ | | (_| | | | | | | (_| || (_| |  __/
 *  \__,_|_| |_|\__,_|_|  \___|_| |_|\___|_|  \__,_|_|_|_| |_|\__, (_)__,_|\___|
 *                                                             __/ |
 *                                                            |___/
 *
 *
 * Visionen & Kreationen – FrontendAutoReload
 *
 * This module is a simple helper for automatically reloading the browser window when a file in the
 * /site/templates/ directory is changed. This is useful for development purposes.
 */

class FrontendAutoReload extends WireData implements Module {
    /**
     * getModuleInfo is a module required by all modules to tell ProcessWire about them
     *
     * @return array
     *
     */
    public static function getModuleInfo() {

        return array(
            'title' => 'FrontendAutoReload',
            'version' => 001,
            'summary' => 'A module for automatically reloading the browser window when a file in the /site/templates/ directory is changed.',
            'author' => 'André Herdling – Visionen & Kreationen',
            'icon' => 'refresh',
            'autoload' => true,
            'singular' => true,
        );
    }

    private string $endpoint = '/frontendautoreload/latest';
    private array $excludedDirectories = ['/images'];
    private array $excludedExtensions = ['jpeg', 'jpg', 'png', 'svg', 'gif'];
    private int $interval = 5;
    private string $watchedDir = '';

    public function __construct()
    {
        parent::__construct();
        $this->watchedDir = $this->wire('config')->paths->templates;
    }

    public function init() {
        if(!$this->isAllowed()) return ''; // Only add the hook if the user is allowed
        // add URL endpoint for JS polling
        // URL hooks where introduced in ProcessWire 3.0.173
        // @source: https://processwire.com/blog/posts/pw-3.0.173/
        $this->wire->addHook($this->endpoint, function($event) {
            header('Content-Type: application/json; charset=utf-8');
            $timestamp = $this->getLatestModificationTime();
            return json_encode($timestamp);
        });

        // get config from POST
        $this->getConfigFromPost();
    }


    /**
     * Returns the desired polling interval in seconds
     *
     * @return int
     */
    public function getInterval(): int
    {
        return $this->interval;
    }


    /**
     * @return array|string[]
     */
    public function getExcludedDirectories(): array
    {
        return $this->excludedDirectories;
    }


    /**
     * @return array|string[]
     */
    public function getExcludedExtensions(): array
    {
        return $this->excludedExtensions;
    }


    /**
     * @param array $excludedDirectories
     * @return void
     */
    public function setExcludedDirectories(array $excludedDirectories): void
    {
        $this->excludedDirectories = $excludedDirectories;
    }


    /**
     * @param array $excludedExtensions
     * @return void
     */
    public function setExcludedExtensions(array $excludedExtensions): void
    {
        $this->excludedExtensions = $excludedExtensions;
    }


    /**
     * @param int $interval
     * @return void
     */
    public function setInterval(int $interval): void
    {
        $this->interval = $interval;
    }


    /**
     * Returns true if these conditions are met:
     * - Debug mode is enabled
     * - User is logged in
     * - User is superuser
     *
     * @return bool
     * @throws WireException
     */
    private function isAllowed(){
        if ($this->wire('config')->debug === false) return false;
        if ($this->wire('user')->isLoggedin() === false) return false;
        if ($this->wire('user')->isSuperuser() === false) return false;
        return true;
    }





    /**
     * Iterates over watched directory and subdirectories to find the latest modification time
     *
     * @return int timestamp of the latest modification in the watched directory
     */
    private function getLatestModificationTime() {
        $latestTime = 0;
        $directory = $this->watchedDir;

        // Define the image extensions to exclude
        $excludedExtensions = ['jpeg', 'jpg', 'png', 'svg', 'gif'];

        // Create a new FilesystemIterator
        // @source: https://www.php.net/manual/en/class.recursivedirectoryiterator.php#85805
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory), \RecursiveIteratorIterator::SELF_FIRST);


        // Iterate through each file in the directory and subdirectories
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                // Check if the directory is in the excluded list

                $folderRelativePath = $this->getStrippedPath($file->getPath());
                if(in_array($folderRelativePath, $this->excludedDirectories)) {
                    continue;
                }
            }

            // Check if it's a file (not a directory)
            if ($file->isFile()) {
                // Get the file extension
                $fileExtension = strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION));

                // Check if the file extension is in the excluded list
                if(in_array($fileExtension, $this->excludedExtensions)) {
                    continue;
                }

                // Get the modification time of the file
                $fileTime = $file->getMTime();
                // Update the latest modification time if this file is newer
                if ($fileTime > $latestTime) {
                    $latestTime = $fileTime;
                }
            }
        }
        return $latestTime;
    }


    /**
     * Strips the watched directory from the path
     * e.g. /var/www/html/site/templates/images/icons -> /images/icons
     *
     * @param string $path
     * @return string
     */
    private function getStrippedPath(string $path):string {
        $search = rtrim($this->watchedDir,"/");
        return str_replace($search, '', $path);
    }

    /**
     * Renders the frontend script element
     *
     * @return string
     * @throws WireException
     */
    public function renderScript() {
        if(!$this->isAllowed()) return ''; // Only render the script if the user is allowed
        return "\n\n".$this->wire('files')->render(__DIR__ . '/components/frontend-js.php', ['far' => $this]);
    }

    /**
     * Returns the (relative) URL of the endpoint
     *
     * @return string
     */
    public function getEndpointUrl(): string
    {
        $endpoint = ltrim($this->endpoint,"/");
        return $this->wire('config')->urls->root . $endpoint;
    }


    /**
     * Returns the configuration as an array
     *
     * @return array
     */
    public function getConfig():array
    {
        return [
            'excludedDirectories' => $this->excludedDirectories,
            'excludedExtensions' => $this->excludedExtensions,
            'interval' => $this->interval
        ];
    }


    /**
     * Sets the configuration from the POST request
     * (JSON encoded)
     *
     * @return void
     * @throws WireException
     */
    private function getConfigFromPost() {
        if(!$this->wire('input')->post) return;
        $post = file_get_contents('php://input');
        if(!$post) return;
        $data = json_decode($post, true);
        if(array_key_exists('excludedDirectories', $data)) {
            $this->setExcludedDirectories($data['excludedDirectories']);
        }
        if(array_key_exists('excludedExtensions', $data)) {
            $this->setExcludedExtensions($data['excludedExtensions']);
        }
        if(array_key_exists('interval', $data)) {
            $this->setInterval($data['interval']);
        }
    }
}
