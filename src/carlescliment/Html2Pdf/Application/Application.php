<?php

namespace carlescliment\Html2Pdf\Application;

use Silex\Application as SilexApplication;
use Symfony\Component\HttpFoundation\Request;

use carlescliment\Html2Pdf\Generator\PdfGenerator,
    carlescliment\Html2Pdf\Generator\NameGenerator;
use Knp\Snappy\Pdf;

class Application extends SilexApplication
{
    private $rootDir;


    public function __construct($root_dir, $debug = false)
    {
        parent::__construct();
        $this->rootDir = $root_dir;
        $this->setDebugMode($debug);
        $this->initializeDependencies();
    }

    private function setDebugMode($debug)
    {
        if ($debug) {
            $this['exception_handler']->disable();
        }
    }

    public function bindControllers()
    {

        $this->get('/', function (SilexApplication $app) {
            return $app->json(array('status' => 'ready'));
        });

        $this->post('/', function (SilexApplication $app, Request $request) {
            $content = $request->get('content');

            $resource_name = $this['pdf_generator']->generate($content);

            return $app->json(array('resource_name' => $resource_name));
        });

        return $this;
    }


    private function initializeDependencies()
    {
        $this['documents_dir'] = function(SilexApplication $app) {
            return $this->rootDir . 'documents';
        };
        $this['pdf_binary'] = function(SilexApplication $app) {
            return $this->rootDir . 'bin/wkhtmltopdf';
        };
        $this['pdf_generator'] = function(SilexApplication $app) {
            $name_generator = new NameGenerator;
            $pdf_maker = new Pdf($app['pdf_binary']);
            $documents_dir = $app['documents_dir'];
            return new PdfGenerator($pdf_maker, $name_generator, $documents_dir);
        };
    }
}
