<?php
namespace Fminor\Repertoire\Chord;

use Fminor\Core\Chord\ChordAbstract;
use Fminor\Repertoire\Request\TemplateRequest;
use Fminor\Core\Templating\TwigEngine;
use Fminor\Core\Config\ParametersManager;

class HeaderChord extends ChordAbstract
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
        $headers = $parManager->getChordParameters('fminor', 'header');
        $twig = new TwigEngine(__DIR__);
        $requests = array();
        foreach ($headers as $key => $header) {
            foreach ($header['parts'] as $part) {
                if (!$parManager->hasFeatureById($part, 'embeddedable')) {
                    throw new \InvalidArgumentException(
                        'parts should support be embeddedable'
                    );
                }
            }
            $request = new TemplateRequest();
            $type = $header['inline'] === true ?
                TemplateRequest::INLINE : TemplateRequest::INCLUDED;
            $request
                ->setId('fminor.header.'.$key)
                ->setType($type)
                ->setContent($twig->render('header.php.twig', array(
                        'name' => $key,
                        'parts' => $header['parts'])
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
        return 'header';
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
