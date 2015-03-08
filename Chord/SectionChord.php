<?php
namespace Fminor\Repertoire\Chord;

use Fminor\Core\Chord\ChordAbstract;
use Fminor\Repertoire\Request\TemplateRequest;
use Fminor\Core\Templating\TwigEngine;
use Fminor\Core\Config\ParametersManager;

class SectionChord extends ChordAbstract
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
                ->scalarNode('inline')
                    ->defaultValue(true)
                ->end()
                ->arrayNode('parts')
                    ->prototype('scalar')->end()
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
        $sections = $parManager->getChordParameters('fminor', 'section');
        $twig = new TwigEngine(__DIR__);
        $requests = array();
        foreach ($sections as $key => $section) {
            foreach ($section['parts'] as $part) {
                if (!$parManager->hasFeatureById($part, 'embeddedable')) {
                    throw new \InvalidArgumentException(
                        'parts should support be embeddedable'
                    );
                }
            }
            $request = new TemplateRequest();
            $type = $section['inline'] === true ?
                TemplateRequest::INLINE : TemplateRequest::INCLUDED;
            $request
                ->setId('fminor.section.'.$key)
                ->setType($type)
                ->setContent($twig->render('section.php.twig', array(
                        'name' => $key,
                        'parts' => $section['parts'])
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
        return 'section';
    }

    /* (non-PHPdoc)
     * @see \Fminor\Core\Chord\ChordAbstract::getSupportedFeatures()
     */
    public function getSupportedFeatures()
    {
        return array(
            'embeddedable',
            'linkeable',
        );
    }
}
