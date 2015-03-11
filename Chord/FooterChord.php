<?php
namespace Fminor\Repertoire\Chord;

use Fminor\Core\Chord\ChordAbstract;
use Fminor\Repertoire\Request\TemplateRequest;
use Fminor\Core\Templating\TwigEngine;
use Fminor\Core\Config\ParametersManager;

class FooterChord extends ChordAbstract
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
        $footers = $parManager->getChordParameters('fminor', 'footer');
        $twig = new TwigEngine(__DIR__);
        $year = date("Y");
        $company = $parManager->getBaseParameter('company_name');
        $requests = array();
        foreach ($footers as $key => $footer) {
            foreach ($footer['parts'] as $part) {
                if (!$parManager->hasFeatureById($part, 'embeddedable')) {
                    throw new \InvalidArgumentException(
                        'parts should support be embeddedable'
                    );
                }
            }
            $request = new TemplateRequest();
            $type = $footer['inline'] === true ?
                TemplateRequest::INLINE : TemplateRequest::INCLUDED;
            $request
                ->setId('fminor.footer.'.$key)
                ->setType($type)
                ->setContent($twig->render('footer.php.twig', array(
                        'name' => $key,
                        'company_name' => $company,
                        'year' => $year,
                        'parts' => $footer['parts'], )
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
        return 'footer';
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
