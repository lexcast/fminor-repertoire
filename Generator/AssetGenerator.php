<?php
namespace Fminor\Repertoire\Generator;

use Fminor\Core\Generator\GeneratorAbstract;
use Fminor\Core\Config\ParametersManager;
use Fminor\Core\Templating\TwigEngine;
use Fminor\Repertoire\Request\AssetRequest;
use Fminor\Repertoire\Request\LibraryRequest;

class AssetGenerator extends GeneratorAbstract
{
    /* (non-PHPdoc)
     * @see \Fminor\Core\Generator\GeneratorAbstract::generate()
     */
    public function generate(array $requests, ParametersManager $parManager)
    {
        $twig = new TwigEngine(__DIR__);
        $assets = AssetRequest::filter($requests);
        $libraries = LibraryRequest::filter($requests);
        $libraries = $this->addBaseLibraries($libraries);
        $preferCdns = $parManager->getBaseParameter('prefer_cdn');
        $projectName = $parManager->getBaseParameter('project_name');
        $stylesReq = array();
        $scriptsReq = array();
        $styles = array();
        $scripts = array();
        $bower = array();
        $already = array();
        $libraries = array_filter($libraries, function ($library) use ($already) {
            if (isset($already[$library->getId()])) {
                return false;
            } else {
                $already[$library->getId()] = true;

                return true;
            }
        });
        foreach ($libraries as $library) {
            $bowerAlr = false;
            if ($preferCdns && $library->getStyleCdns() !== null) {
                $styles = array_merge($styles, $library->getStyleCdns());
            } elseif ($library->getStylePaths() !== null) {
                $styles = array_merge($styles, $library->getStylePaths());
                $bower[] = $library->getId();
                $bowerAlr = true;
            }
            if ($preferCdns && $library->getScriptCdns() !== null) {
                $scripts = array_merge($scripts, $library->getScriptCdns());
            } elseif ($library->getScriptPaths() !== null) {
                $scripts = array_merge($scripts, $library->getScriptPaths());
                if (!$bowerAlr) {
                    $bower[] = $library->getId();
                }
            }
        }
        foreach ($assets as $asset) {
            if ($asset->getType() === AssetRequest::STYLE) {
                $stylesReq[] = $asset;
            } elseif ($asset->getType() === AssetRequest::SCRIPT) {
                $scriptsReq[] = $asset;
            }
        }
        if (count($stylesReq) > 0) {
            $styles[] = '/css/style.css';
            $param = array('assets' => $stylesReq);
            $this->create('web/css/', 'style.css', $twig->render('asset.twig', $param));
        }
        if (count($scriptsReq) > 0) {
            $styles[] = '/js/scripts.js';
            $param = array('assets' => $scriptsReq);
            $this->create('web/js/', 'scripts.js', $twig->render('asset.twig', $param));
        }
        if (count($bower) > 0) {
            $param = array('libraries' => $bower, 'project_name' => $projectName);
            $this->create('/', 'bower.json', $twig->render('bower.json.twig', $param));
        }
        $param = array(
            'styles' => $styles,
            'scripts' => $scripts,
            'project_name' => $projectName, );
        $this->create('src/Resources/views/', 'base.php', $twig->render('base.php.twig', $param));
    }
    private function addBaseLibraries(array $requests)
    {
        $jquery = new LibraryRequest();
        $jquery
            ->setId('jquery')
            ->setScriptCdns(array('//code.jquery.com/jquery-1.11.2.min.js'))
            ->setScriptPaths(array('/js/jquery/jquery.min.js'));
        $bootstrap = new LibraryRequest();
        $bootstrap
                ->setId('bootstrap')
                ->setScriptCdns(array('//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js'))
                ->setScriptPaths(array('/js/bootstrap/bootstrap.min.js'))
                ->setStyleCdns(array('//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css'))
                ->setStylePaths(array('/css/bootstrap/bootstrap.min.css'));
        $base = array();
        $base[] = $jquery;
        $base[] = $bootstrap;

        return array_merge($base, $requests);
    }
}
