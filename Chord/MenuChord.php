<?php
namespace Fminor\Repertoire\Chord;

use Fminor\Core\Chord\ChordAbstract;
use Fminor\Repertoire\Request\TemplateRequest;
use Fminor\Core\Templating\TwigEngine;
use Fminor\Core\Config\ParametersManager;

class MenuChord extends ChordAbstract
{
    /* (non-PHPdoc)
     * @see \Fminor\Core\Chord\ChordInterface::getConfigNode()
     */
    public function getConfigNode()
    {
        $node = $this->getChordNode();
        $node
        ->prototype('array')
            ->children()
                ->booleanNode('inline')
                    ->defaultTrue()
                ->end()
                ->enumNode('brand')
                    ->values(array('image', 'text', 'none'))
                    ->defaultValue('text')
                ->end()
                ->arrayNode('parts')
                    ->prototype('array')
                    ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return 'links' === $v['type'] && isset($v['part']);
                        })
                        ->thenInvalid('Invalid "part" key when type is "links"')
                    ->end()
                    ->beforeNormalization()
                        ->ifTrue(function ($v) {
                            return 'embedded' === $v['type'] && isset($v['parts']);
                        })
                        ->thenInvalid('Invalid "parts" key when type is "embedded"')
                    ->end()
                        ->children()
                            ->enumNode('type')
                                ->values(array('links', 'embedded'))
                            ->end()
                            ->enumNode('position')
                                ->values(array('right', 'left'))
                                ->defaultValue('right')
                            ->end()
                            ->scalarNode('part')->end()
                            ->arrayNode('parts')
                                ->prototype('array')
                                    ->beforeNormalization()
                                        ->ifTrue(function ($v) {
                                            return 'link' === $v['type'] && isset($v['parts']);
                                        })
                                        ->thenInvalid('Invalid "parts" key when type is "link"')
                                    ->end()
                                    ->beforeNormalization()
                                        ->ifTrue(function ($v) {
                                            return 'dropdown' === $v['type'] && isset($v['part']);
                                        })
                                        ->thenInvalid('Invalid "part" key when type is "dropdown"')
                                    ->end()
                                    ->children()
                                        ->enumNode('type')
                                            ->values(array('link', 'dropdown'))
                                        ->end()
                                        ->scalarNode('part')->end()
                                        ->arrayNode('parts')
                                            ->prototype('array')
                                                ->children()
                                                    ->scalarNode('part')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $node;
    }

    /* (non-PHPdoc)
     * @see \Fminor\Core\Chord\ChordInterface::generateWriteRequests()
     */
    public function generateRequests(ParametersManager $parManager)
    {
        $menus = $parManager->getChordParameters('fminor', 'menu');
        $twig = new TwigEngine(__DIR__);
        $project = $parManager->getBaseParameter('project_name');
        $requests = array();
        foreach ($menus as $key => $menu) {
            foreach ($menu['parts'] as $menuPart) {
                if ($menuPart['type'] === 'links') {
                    foreach ($menuPart['parts'] as $part) {
                        if ($part['type'] === 'link') {
                            if (!$parManager->hasFeatureById($part['part'], 'linkable')) {
                                throw new \InvalidArgumentException(
                                    "part in a link group of menu $key should support be linkable"
                                );
                            }
                        } elseif ($part['type'] === 'dropdown') {
                            foreach ($part['parts'] as $link) {
                                if (!$parManager->hasFeatureById($link['part'], 'linkable')) {
                                    throw new \InvalidArgumentException(
                                        "part in a dropdown of menu $key should support be linkable"
                                    );
                                }
                            }
                        }
                    }
                } elseif ($menuPart['type'] === 'embedded') {
                    if (!$parManager->hasFeatureById($menuPart['part'], 'embeddedable')) {
                        throw new \InvalidArgumentException(
                            "part type embedded in menu $key should support be embeddedable"
                        );
                    }
                }
            }
            $request = new TemplateRequest();
            $type = $menu['inline'] === true ?
                TemplateRequest::INLINE : TemplateRequest::INCLUDED;
            $request
                ->setId('fminor.menu.'.$key)
                ->setType($type)
                ->setContent($twig->render('menu.php.twig', array(
                        'name' => $key,
                        'brand' => $menu['brand'],
                        'project_name' => $project,
                        'parts' => $menu['parts'], )
                        )
                    );
            $requests[] = $request;
        }

        return $requests;
    }
    /* (non-PHPdoc)
     * @see \Fminor\Core\Chord\ChordInterface::getName()
     */
    public function getName()
    {
        return 'menu';
    }

    /* (non-PHPdoc)
     * @see \Fminor\Core\Chord\ChordAbstract::getSupportedFeatures()
     */
    public function getSupportedFeatures()
    {
        return array(
            'embeddedable',
        );
    }
}
