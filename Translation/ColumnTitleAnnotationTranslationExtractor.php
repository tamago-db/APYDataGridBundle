<?php

namespace APY\DataGridBundle\Translation;

use Doctrine\Common\Annotations\AnnotationReader as DoctrineAnnotationReader;

use APY\DataGridBundle\Grid\Mapping\Driver\Annotation;
use APY\DataGridBundle\Grid\Mapping\Metadata\Manager;

use JMS\TranslationBundle\Model\FileSource;
use JMS\TranslationBundle\Model\Message;
use JMS\TranslationBundle\Model\MessageCatalogue;
use JMS\TranslationBundle\Translation\Extractor\FileVisitorInterface;

class ColumnTitleAnnotationTranslationExtractor implements FileVisitorInterface, \PHPParser\NodeVisitor
{
    private $annotated;
    private $catalogue;
    private $parsedClassName;

    public function beforeTraverse(array $nodes) {
        $this->annotated = false;
        $this->parsedClassName = null;
    }

    public function enterNode(\PHPParser\Node $node) {
        if ($node instanceof \PHPParser\Node\Stmt\Namespace_) {
            // Base namespace
            $this->parsedClassName = $node->name->toString();
        }
        elseif ($node instanceof \PHPParser\Node\Stmt\UseUse) {
            // Don't worry about classes that don't import the grid mapper
            if ('APY_DataGridBundle_Grid_Mapping' == $node->name->toString('_')) {
                $this->annotated = true;
            }
        }
        elseif ($node instanceof \PHPParser\Node\Stmt\Class_) {
            // Append class name to base namespace
            $this->parsedClassName .= '\\' . $node->name;
        }
    }

    public function leaveNode(\PHPParser\Node $node) { }
    public function afterTraverse(array $nodes) { }

    public function visitFile(\SplFileInfo $file, MessageCatalogue $catalogue) { }

    public function visitPhpFile(\SplFileInfo $file, MessageCatalogue $catalogue, array $ast) {
        $this->catalogue = $catalogue;

        // Traverse document to assemble class name
        $traverser = new \PHPParser\NodeTraverser();
        $traverser->addVisitor($this);
        $traverser->traverse($ast);

        if ($this->annotated) {
            // Get annotations for the class
            $annotationDriver = new Annotation(new DoctrineAnnotationReader());
            $manager = new Manager();
            $manager->addDriver($annotationDriver, -1);
            $metadata = $manager->getMetadata($this->parsedClassName);

            // Save messages for title
            foreach ($metadata->getFields() as $field) {
                $mappedField = $metadata->getFieldMapping($field);
                // Don't bother translating if not visible
                if (isset($mappedField['visible']) && empty($mappedField['visible'])) {
                    continue;
                }
                // If title is set and is not empty or false
                if (! empty($mappedField['title'])) {
                    $message = new Message($mappedField['title']);
                    $message->addSource(new FileSource((string) $file));
                    $catalogue->add($message);
                }
            }
        }
    }

    public function visitTwigFile(\SplFileInfo $file, MessageCatalogue $catalogue, \Twig_Node $node) { }
}
