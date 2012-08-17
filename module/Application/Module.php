<?php

namespace Application;

use Zend\Mvc\ModuleRouteListener;

class Module
{
    public function onBootstrap($e)
    {
        $session = $e->getApplication()->getServiceManager()->get('session');
        if (isset($session->lang)) {
            $translator = $e->getApplication()->getServiceManager()->get('translator');
            $translator->setLocale($session->lang);
            $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
            $viewModel->lang = str_replace('_', '-', $session->lang);
        }
        $eventManager = $e->getApplication()->getEventManager();

        $eventManager->attach('route', function ($e) {
            $lang = $e->getRouteMatch()->getParam('lang');
            if (!empty($lang)) {
                $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
                $viewModel->lang = $lang;
                $lang = str_replace('-', '_', $lang);
                $translator = $e->getApplication()->getServiceManager()->get('translator');
                $translator->setLocale($lang);
                $session = $e->getApplication()->getServiceManager()->get('session');
                $session->lang = $lang;
            }
        }, -10);
        
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
