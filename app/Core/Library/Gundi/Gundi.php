<?php
namespace {
    /**
     * @return \Core\Library\Gundi\Gundi;
     */
    function Gundi()
    {
        return $GLOBALS['gundi_instance'];
    }
}

namespace Core\Library\Gundi {

    use Illuminate\Container\Container;
    use Illuminate\Support\Arr;
    use Illuminate\Support\ServiceProvider;


    /**
     * @property \Core\library\Router\Router Router
     * @property \Core\Library\View\Html\Extension\Block Block
     * @property \Core\Library\Setting\Setting $config
     * @property \Core\Library\View\Html\Extension\Asset Asset
     * @property \Core\Library\Event\Dispatcher EventDispatcher
     * @property \Core\Library\Session\Session $Session
     * @method void resolving($abstract, \Closure $callback = null)
     */
    class Gundi extends Container
    {
        /**
         * Gundi Version : major.minor.maintenance
         */
        const VERSION = '1.0.1pre-alpha';
        const CODE_NAME = 'AdaptiveMeat';
        const BROWSER_AGENT = 'Gundi';
        const PRODUCT_BUILD = '1';
        const GUNDI_API = '';
        const GUNDI_PACKAGE = 'ultimate';

        /**
         * All of the registered service providers.
         *
         * @var array
         */
        protected $_aServiceProviders = [];

        /**
         * The names of the loaded service providers.
         *
         * @var array
         */
        protected $_aLoadedProviders = [];

        /**
         * The deferred services and their providers.
         *
         * @var array
         */
        protected $deferredServices = [];

        public function __construct()
        {
            $this->instance('app', $this);
            $this->instance('\Illuminate\Container\Container', $this);
            $this->instance('\Illuminate\Contracts\Container\Container', $this);
            $this->instance('\Core\Library\Gundi\Gundi', $this);
            $GLOBALS['gundi_instance'] = &$this;
        }

        /**
         * Register a service provider with the application.
         *
         * @param  \Illuminate\Support\ServiceProvider|string $mProvider
         * @param  array $aOptions
         * @param  bool $bBorce
         * @return \Illuminate\Support\ServiceProvider
         */
        public function register($mProvider, $aOptions = [], $bBorce = false)
        {
            if (($bRegistered = $this->getProvider($mProvider)) && !$bBorce) {
                return $bRegistered;
            }

            if (is_string($mProvider)) {
                $mProvider = $this->resolveProviderClass($mProvider);
            }

            $mProvider->register();

            foreach ($aOptions as $sKey => $mValue) {
                $this[$sKey] = $mValue;
            }

            $this->markAsRegistered($mProvider);

            $this->bootProvider($mProvider);

            return $mProvider;
        }

        /**
         * Get the registered service provider instance if it exists.
         *
         * @param  \Illuminate\Support\ServiceProvider|string $provider
         * @return \Illuminate\Support\ServiceProvider|null
         */
        public function getProvider($provider)
        {
            $sName = is_string($provider) ? $provider : get_class($provider);

            return Arr::first($this->_aServiceProviders, function ($key, $value) use ($sName) {
                return $value instanceof $sName;
            });
        }

        /**
         * Resolve a service provider instance from the class name.
         *
         * @param  string $oProvider
         * @return \Illuminate\Support\ServiceProvider
         */
        public function resolveProviderClass($oProvider)
        {
            return new $oProvider($this);
        }

        /**
         * Mark the given provider as registered.
         *
         * @param  \Illuminate\Support\ServiceProvider $oProvider
         * @return void
         */
        protected function markAsRegistered($oProvider)
        {
            $sClass = get_class($oProvider);
            if ($this->isAlias('events')) {
                $this['events']->fire($sClass, [$oProvider]);
            }

            $this->_aServiceProviders[] = $oProvider;

            $this->_aLoadedProviders[$sClass] = true;
        }

        /**
         * Boot the given service provider.
         *
         * @param  \Illuminate\Support\ServiceProvider $provider
         * @return mixed
         */
        protected function bootProvider(ServiceProvider $provider)
        {
            if (method_exists($provider, 'boot')) {
                return $this->call([$provider, 'boot']);
            }
        }

        private $_oDIContainer = null;

        /**
         * Get the current product version.
         *
         * @return string
         */
        public function getVersion()
        {
            return self::VERSION;
        }

        /**
         * Get the current product version ID.
         *
         * @return int
         */
        public function getId()
        {
            return self::getVersion();
        }

        /**
         * Get the products code name.
         *
         * @return string
         */
        public function getCodeName()
        {
            return self::CODE_NAME;
        }

        /**
         * Get the products build number.
         *
         * @return int
         */
        public function getBuild()
        {
            return self::PRODUCT_BUILD;
        }

        /**
         * Get the clean numerical value of the product version.
         *
         * @return int
         */
        public function getCleanVersion()
        {
            return str_replace('.', '', self::VERSION);
        }


        /**
         * Provide "powered by" link.
         *
         * @param bool $bLink TRUE to include a link to TekeNet.
         * @param bool $bVersion TRUE to include the version being used.
         * @return string Powered by TekeNet string returned.
         */
        public function link($bLink = true, $bVersion = true)
        {
            return 'Powered By ' . ($bVersion ? ' Version ' . $this->getVersion() : '');
        }
    }
}